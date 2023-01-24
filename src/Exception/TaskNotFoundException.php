<?php

namespace Hyqo\Task\Exception;

class TaskNotFoundException extends \InvalidArgumentException
{
    public function __construct(string $classname)
    {
        parent::__construct(sprintf('Class "%s" does not exist', $classname));
    }
}
