<?php

namespace Hyqo\Task\Exception;

class InvokeNotExists extends \InvalidArgumentException
{
    public function __construct(string $classname)
    {
        parent::__construct(sprintf('Class "%s" has no invoke method', $classname));
    }
}
