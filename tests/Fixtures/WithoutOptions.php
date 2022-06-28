<?php

namespace Hyqo\Task\Test\Fixtures;

class WithoutOptions
{
    /** @var string */
    public $message = 'bar';

    public function __invoke(): string
    {
        return $this->message;
    }
}
