<?php

namespace Hyqo\Task\Test\Fixtures;

class UnionType
{
    public function __invoke(
        string|int $number
    ) {
    }
}
