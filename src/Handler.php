<?php

namespace Hyqo\Task;

use Hyqo\Cli;

use Hyqo\Container\Container;
use Hyqo\Task\Exception\InvalidInvoke;

use Hyqo\Task\Exception\InvalidResolver;

use function Hyqo\String\PascalCase;

class Handler
{
    /** @var Container */
    protected $container;

    /** @var string */
    protected $namespace;

    /** @var ?\Closure */
    protected $resolver;

    /** @var resource */
    protected $outputStream;

    /** @var resource */
    protected $errorStream;

    public function __construct(
        $outputStream = STDOUT,
        $errorStream = STDERR
    ) {
        $this->outputStream = $outputStream;
        $this->errorStream = $errorStream;
    }

    /** @codeCoverageIgnore */
    public function setContainer(Container $container): self
    {
        $this->container = $container;

        return $this;
    }

    public function setResolver(\Closure $resolver): self
    {
        $this->resolver = $resolver;

        return $this;
    }

    public function handle(?array $argv = null)
    {
        $arguments = new Cli\Arguments($argv);

        $this->setErrorHandlers($arguments);

        $flags = $arguments->getShortOptions();

        $task = new Task($this->prepareTaskClass($arguments->getFirst()), $this->container);

        if ($flags['h'] ?? false) {
            Cli\Output::send($task->generateDescription(), $this->outputStream);
        } else {
            try {
                return $task->run($arguments->getLongOptions());
            } catch (InvalidInvoke $e) {
                $message = sprintf('<error>The task is not invoking correctly: %s</error>', $e->getMessage());

                Cli\Output::send($message, $this->errorStream);

                $this->terminate();
            } catch (\Throwable $e) {
                Cli\Output::send([
                    sprintf('<error>%s</error>: %s', get_class($e), $e->getMessage()),
                    sprintf('%s:%s', $e->getFile(), $e->getLine()),
                    sprintf('<trace>%s</trace>', $e->getTraceAsString()),
                ], $this->errorStream);
            } finally {
                $this->restoreErrorHandlers();
            }
        }

        $this->restoreErrorHandlers();

        return null;
    }

    protected function setErrorHandlers(Cli\Arguments $arguments): void
    {
        set_error_handler(
            function (int $number, string $message, string $filename, int $line) use ($arguments) {
                Cli\Output::send([
                    sprintf('<error>%d: %s</error>', $number, $message),
                    sprintf('<error>%s: %d</error>', $filename, $line),
                    sprintf('<trace>Call: %s</trace>', implode(' ', $arguments->getAll())),
                ], $this->errorStream);

                return true;
            }
        );
    }

    protected function restoreErrorHandlers(): void
    {
        restore_error_handler();
    }

    /** @codeCoverageIgnore */
    protected function terminate(): void
    {
        exit(1);
    }

    protected function prepareTaskClass(?string $dirtyClassname): string
    {
        if ($dirtyClassname === null) {
            throw new \InvalidArgumentException('You must specify the task name');
        }

        $chunks = array_map(
            static function (string $chunk) {
                return PascalCase($chunk);
            },
            explode(':', $dirtyClassname)
        );

        if (null !== $this->resolver) {
            $chunks = ($this->resolver)($chunks);

            if (!is_array($chunks)) {
                throw new InvalidResolver('Resolver must return an array');
            }
        }

        return implode('\\', $chunks);
    }
}
