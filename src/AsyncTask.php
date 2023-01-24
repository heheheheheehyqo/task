<?php

namespace Hyqo\Task;

use function Hyqo\String\s;

class AsyncTask extends Task
{
    public function run(array $arguments = []): void
    {
        $command = sprintf(
            'php %s %s %s > /dev/null 2>&1 &',
            dirname(__DIR__) . '/bin/task',
            $this->generateTaskName(),
            $this->generateOptions($arguments)
        );

        exec($command);
    }

    protected function generateTaskName(): string
    {
        return s($this->getClassname())
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
            $collection->reduce(function (?string $carry, Argument $argument) {
                return $carry . " " . $argument->stringify();
            })
        );
    }
}
