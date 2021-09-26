<?php

namespace Hyqo\Task\Test\Fixtures;

class ErrorInside
{
    public function __invoke() {
        return [][1];
    }
}
