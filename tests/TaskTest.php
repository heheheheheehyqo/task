<?php

namespace Hyqo\Task\Test;

use Hyqo\Task\Exception\InvalidInvokeException;
use Hyqo\Task\Exception\InvalidOptionException;
use Hyqo\Task\Exception\InvokeNotExistsException;
use Hyqo\Task\Exception\TaskNotFoundException;
use Hyqo\Task\Test\Fixtures\InvalidOptionType;
use Hyqo\Task\Test\Fixtures\NormalTask;
use Hyqo\Task\Test\Fixtures\UnionType;
use Hyqo\Task\Test\Fixtures\UntypedOption;
use Hyqo\Task\Test\Fixtures\WithoutInvoke;
use PHPUnit\Framework\TestCase;

use function Hyqo\Task\task;

class TaskTest extends TestCase
{
    public function test_run(): void
    {
        $result = task(NormalTask::class, ['message' => 'foo', 'number' => 2, 'flag' => true]);

        $this->assertEquals('bar foo with flag', $result);
    }

    public function test_invalid_task_name(): void
    {
        $this->expectException(TaskNotFoundException::class);

        task('class');
    }

    public function test_invoke_not_exists(): void
    {
        $this->expectException(InvokeNotExistsException::class);

        task(WithoutInvoke::class);
    }

    public function test_invalid_run(): void
    {
        $this->expectException(InvalidInvokeException::class);

        task(NormalTask::class);
    }

    public function test_invalid_option_type(): void
    {
        $this->expectException(InvalidOptionException::class);

        task(InvalidOptionType::class);
    }

    public function test_untyped_option(): void
    {
        $this->expectException(InvalidOptionException::class);

        task(UntypedOption::class);
    }

    public function test_invalid_option_union_type(): void
    {
        $this->expectException(InvalidOptionException::class);

        task(UnionType::class);
    }
}
