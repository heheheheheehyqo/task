<?php

namespace Hyqo\Task\Test\Fixtures;

class WithoutOptions
{
    public string $message = 'bar';

    public function __invoke(): string
    {
        return $this->message;
    }
}
