<?php

namespace Hyqo\Task\Test\Fixtures;

class Bar
{
    public string $message = 'bar';

    public function __invoke(): string
    {
        return $this->message;
    }
}
