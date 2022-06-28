<?php

namespace Hyqo\Task;

use Hyqo\Collection\Collection;
use Hyqo\Container\Container;
use Hyqo\Task\Exception\InvalidInvoke;
use Hyqo\Task\Exception\InvokeNotExists;
use Hyqo\Task\Exception\TaskNotFound;

class Task
{
    /** @var Container */
    protected $container;

    /** @var string */
    protected $classname;

    /** @var \ReflectionMethod */
    protected $invokeMethod;

    /** @var Collection<Option> */
    protected $options;

    public function __construct(string $classname, ?Container $container = null)
    {
        $this->container = $container ?? new Container();
        $this->classname = $classname;
        $this->invokeMethod = self::getInvokeMethod($classname);
        $this->options = new Collection();

        foreach ($this->invokeMethod->getParameters() as $reflectionParameter) {
            $option = new Option($reflectionParameter);

            $this->options->add($option);
        }
    }

    public function getClassname(): string
    {
        return $this->classname;
    }

    protected static function getInvokeMethod(string $classname): \ReflectionMethod
    {
        try {
            $reflection = new \ReflectionClass($classname);
        } catch (\ReflectionException $e) {
            throw new TaskNotFound($classname);
        }

        try {
            return $reflection->getMethod('__invoke');
        } catch (\ReflectionException $e) {
            throw new InvokeNotExists($classname);
        }
    }

    public function generateDescription(): string
    {
        return (new Help($this->options))->generateDescription();
    }

    /**
     * @param array $arguments
     * @return Collection<Argument>
     */
    protected function validateArguments(array $arguments): Collection
    {
        $collection = new Collection();
        $errors = [];

        /** @var Option $option */
        foreach ($this->options as $option) {
            $name = $option->getName();
            $value = $arguments[$name] ?? null;

            if ($value === null) {
                if ($option->isRequired()) {
                    $errors[] = sprintf('The option --%s is required', $option->getName());
                } else {
                    $collection->add(new Argument($option, $option->getDefault()));
                }
            } else {
                $collection->add(new Argument($option, $value));
            }
        }

        if ($errors) {
            $example = (new Help($this->options))->generateExample();

            throw new InvalidInvoke($this->classname, $example, $errors);
        }

        return $collection;
    }

    public function run(array $arguments = [])
    {
        $collection = $this->validateArguments($arguments);
        $invokeArguments = $collection->map(
            function (Argument $argument) {
                yield $argument->getOptionName() => $argument->getValue();
            }
        );

        $task = $this->container->make($this->classname);

        return $this->container->call([$task, $this->invokeMethod->getName()], $invokeArguments);
    }
}
