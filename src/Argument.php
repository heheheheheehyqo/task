<?php

namespace Hyqo\Task;

class Argument
{
    public Option $option;

    protected string|int|bool $value;

    public function __construct(Option $option, $value)
    {
        $this->option = $option;
        $this->value = $value;
    }

    public function getOptionName(): string
    {
        return $this->option->getName();
    }

    public function getValue(): bool|int|string
    {
        return match ($this->option->getType()) {
            'bool' => (bool)$this->value,
            'int' => (int)$this->value,
            default => $this->value,
        };
    }

    public function stringify(): string
    {
        $value = match ($this->option->getType()) {
            'string' => sprintf('"%s"', addcslashes($this->value, '"')),
            default => var_export($this->value, true),
        };

        return sprintf(
            '--%s=%s',
            $this->getOptionName(),
            $value
        );
    }
}
