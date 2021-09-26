<?php

namespace Hyqo\Task\Test\Fixtures;

class InvalidOptionType
{
    public function __invoke(
        float $number,
        bool $flag = false
    ) {
    }
}
