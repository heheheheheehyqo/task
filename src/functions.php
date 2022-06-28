<?php

namespace Hyqo\Task;

function task(string $classname, array $options = [])
{
    return (new Task($classname))->run($options);
}

function async_task(string $classname, array $options = []): void
{
    (new AsyncTask($classname))->run($options);
}
