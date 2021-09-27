<?php

namespace Hyqo\Task\Exception;

class InvalidInvoke extends \RuntimeException
{
    /** @var string[] $message */
    public function __construct(array $message)
    {
        parent::__construct(implode("\n", $message));
    }
}
