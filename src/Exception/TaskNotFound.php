<?php

namespace Hyqo\Task\Exception;

class TaskNotFound extends \InvalidArgumentException
{
    public function __construct(string $classname)
    {
        parent::__construct(sprintf('Class "%s" does not exist', $classname));
    }
}
