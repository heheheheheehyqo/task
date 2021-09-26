<?php

namespace Hyqo\Task\Test\Fixtures;

class DeleteFile
{
    public function __invoke(string $filename, bool $flag = true)
    {
        unlink($filename);
    }
}
