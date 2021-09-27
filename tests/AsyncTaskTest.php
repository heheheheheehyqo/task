<?php

namespace Hyqo\Task\Test;

use Hyqo\Task\AsyncTask;
use Hyqo\Task\Test\Fixtures\DeleteFile;
use PHPUnit\Framework\TestCase;

use function Hyqo\Task\async_task;

class AsyncTaskTest extends TestCase
{
    public function test_generate_task_name()
    {
        $generateTaskName = new \ReflectionMethod(AsyncTask::class, "generateTaskName");
        $generateTaskName->setAccessible(true);

        $asyncTask = new AsyncTask(DeleteFile::class);

        $taskName = $generateTaskName->invoke($asyncTask);
        $this->assertEquals('hyqo:task:test:fixtures:delete-file', $taskName);
    }

    public function test_generate_options()
    {
        $generateOptions = new \ReflectionMethod(AsyncTask::class, "generateOptions");
        $generateOptions->setAccessible(true);

        $asyncTask = new AsyncTask(DeleteFile::class);

        $options = $generateOptions->invoke($asyncTask, ['filename' => 'path', 'flag' => true]);
        $this->assertEquals('--filename="path" --flag=true', $options);
    }

    public function test_run()
    {
        $tmp = tempnam(__DIR__, 'test');

        async_task(DeleteFile::class, ['filename' => $tmp]);
        sleep(1);

        $this->assertFileDoesNotExist($tmp);
    }
}
