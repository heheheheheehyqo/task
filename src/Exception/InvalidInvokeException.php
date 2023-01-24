<?php

namespace Hyqo\Task\Exception;

class InvalidInvokeException extends \RuntimeException
{
    public function __construct(string $classname, string $example, array $errors)
    {
        parent::__construct(sprintf("%s\n%s\n\n%s", $classname, implode("\n", $errors), $example));
    }
}
