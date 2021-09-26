<?php

namespace Hyqo\Task;

use Hyqo\CLI\Arguments;
use Hyqo\CLI\Output;

use Hyqo\Task\Exception\InvalidInvoke;

use JetBrains\PhpStorm\NoReturn;

use function Hyqo\String\PascalCase;

class Handler
{
    public function __construct(
        private string $namespace = '',
        private $outputStream = STDOUT,
        private $errorStream = STDERR,
    ) {
    }

    public function handle(?array $argv = null): mixed
    {
        $arguments = new Arguments($argv);

        $this->setErrorHandlers($arguments);

        $flags = $arguments->getShortOptions();

        $task = new Task($this->prepareTaskClass($arguments->getFirst()));

        if ($flags['h'] ?? false) {
            Output::send($task->generateDescription(), $this->outputStream);
        } else {
            try {
                return $task->run($arguments->getLongOptions());
            } catch (InvalidInvoke $e) {
                $message = sprintf('<error>The task is not invoking correctly: %s</error>', $e->getMessage());

                Output::send($message, $this->errorStream);

                $this->terminate();
            } catch (\Throwable $e) {
                Output::send([
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

    private function setErrorHandlers(Arguments $arguments): void
    {
        set_error_handler(
            function (int $number, string $message, string $filename, int $line) use ($arguments) {
                Output::send([
                    sprintf('<error>%d: %s</error>', $number, $message),
                    sprintf('<error>%s: %d</error>', $filename, $line),
                    sprintf('<trace>Call: %s</trace>', implode(' ', $arguments->getAll())),
                ], $this->errorStream);

                return true;
            }
        );
    }

    private function restoreErrorHandlers(): void
    {
        restore_error_handler();
    }

    /** @codeCoverageIgnore */
    #[NoReturn]
    protected function terminate(): void
    {
        exit(1);
    }

    private function prepareTaskClass(?string $dirtyClassname): string
    {
        if ($dirtyClassname === null) {
            throw new \InvalidArgumentException('You must specify the task name');
        }

        $classname = implode(
            '\\',
            array_map(
                fn(string $chunk) => PascalCase($chunk),
                explode(':', $dirtyClassname)
            )
        );

        return $this->namespace . $classname;
    }
}
