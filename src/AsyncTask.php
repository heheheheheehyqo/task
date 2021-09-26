<?php

namespace Hyqo\Task;

use function Hyqo\String\s;
use function Hyqo\String\snake_case;

class AsyncTask extends Task
{
    public function run(array $arguments = []): mixed
    {
        $command = sprintf(
            'php %s %s %s > /dev/null 2>&1 &',
            dirname(__DIR__) . '/bin/task',
            $this->generateTaskName(),
            $this->generateOptions($arguments),
        );

        exec($command);

        return null;
    }

    protected function generateTaskName(): string
    {
        return (string)s($this->getClassname())
            ->pregReplace([
                '/\\\\([A-Z])/',
                '/(?<=[a-z])([A-Z])/',
            ], [
                ':$1',
                '-$1'
            ])->lower();
    }

    protected function generateOptions(array $arguments = []): string
    {
        $collection = $this->validateArguments($arguments);

        return trim(
            $collection->reduce(fn(?string $carry, Argument $argument) => $carry . " " . $argument->stringify())
        );
    }
}
