<?php

namespace Hyqo\Task\Test\Fixtures;

class ExceptionInside
{
    public function __invoke()
    {
        throw new \Exception('error message');
    }
}
