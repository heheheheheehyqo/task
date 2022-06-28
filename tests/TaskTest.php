<?php

namespace Hyqo\Task\Test;

use Hyqo\Task\Exception\InvalidInvoke;
use Hyqo\Task\Exception\InvalidOption;
use Hyqo\Task\Exception\InvokeNotExists;
use Hyqo\Task\Exception\TaskNotFound;
use Hyqo\Task\Test\Fixtures\InvalidOptionType;
use Hyqo\Task\Test\Fixtures\NormalTask;
use Hyqo\Task\Test\Fixtures\UnionType;
use Hyqo\Task\Test\Fixtures\UntypedOption;
use Hyqo\Task\Test\Fixtures\WithoutInvoke;
use PHPUnit\Framework\TestCase;

use function Hyqo\Task\task;

class TaskTest extends TestCase
{
    public function test_run()
    {
        $result = task(NormalTask::class, ['message' => 'foo', 'number' => 2, 'flag' => true]);

        $this->assertEquals('bar foo with flag', $result);
    }

    public function test_invalid_task_name()
    {
        $this->expectException(TaskNotFound::class);

        task('class');
    }

    public function test_invoke_not_exists()
    {
        $this->expectException(InvokeNotExists::class);

        task(WithoutInvoke::class);
    }

    public function test_invalid_run()
    {
        $this->expectException(InvalidInvoke::class);

        task(NormalTask::class);
    }

    public function test_invalid_option_type()
    {
        $this->expectException(InvalidOption::class);

        task(InvalidOptionType::class);
    }

    public function test_untyped_option()
    {
        $this->expectException(InvalidOption::class);

        task(UntypedOption::class);
    }

//    public function test_invalid_option_union_type()
//    {
//        $this->expectException(InvalidOption::class);
//
//        task(UnionType::class);
//    }
}
