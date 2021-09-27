<?php

namespace Hyqo\Task;

function task(string $classname, array $options = []): mixed
{
    return (new Task($classname))->run($options);
}

function async_task(string $classname, array $options = []): mixed
{
    return (new AsyncTask($classname))->run($options);
}
