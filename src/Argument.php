<?php

namespace Hyqo\Task;

class Argument
{
    public function __construct(public Option $option, private string|int|bool $value)
    {
    }

    public function getOptionName(): string
    {
        return $this->option->getName();
    }

    public function getValue(): string|int|bool
    {
        return match ($this->option->getType()) {
            'bool' => (bool)$this->value,
            'int' => (int)$this->value,
            default => $this->value
        };
    }

    public function stringify(): string
    {
        return sprintf(
            '--%s=%s',
            $this->getOptionName(),
            match ($this->option->getType()) {
                'string' => sprintf('"%s"', addcslashes($this->value, '"')),
                default => var_export($this->value, true),
            }
        );
    }
}
