<?php

namespace Hyqo\Task;

class Argument
{
    /** @var Option */
    public $option;

    /** @var string|int|bool */
    protected $value;

    public function __construct(Option $option, $value)
    {
        $this->option = $option;
        $this->value = $value;
    }

    public function getOptionName(): string
    {
        return $this->option->getName();
    }

    /** @return string|int|bool */
    public function getValue()
    {
        switch ($this->option->getType()) {
            case 'bool':
                return (bool)$this->value;
            case 'int':
                return (int)$this->value;
            default:
                return $this->value;
        }
    }

    public function stringify(): string
    {
        switch ($this->option->getType()) {
            case 'string':
                $value = sprintf('"%s"', addcslashes($this->value, '"'));
                break;
            default:
                $value = var_export($this->value, true);
        }

        return sprintf(
            '--%s=%s',
            $this->getOptionName(),
            $value
        );
    }
}
