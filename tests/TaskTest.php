<?php

namespace Hyqo\Task\Test;

use Hyqo\Task\Exception\InvalidInvoke;
use Hyqo\Task\Exception\InvalidOption;
use Hyqo\Task\Task;
use Hyqo\Task\Test\Fixtures\ErrorInside;
use Hyqo\Task\Test\Fixtures\InvalidOptionType;
use Hyqo\Task\Test\Fixtures\Foo;
use Hyqo\Task\Test\Fixtures\UnionType;
use Hyqo\Task\Test\Fixtures\UntypedOption;
use PHPUnit\Framework\TestCase;

class TaskTest extends TestCase
{
    public function test_run()
    {
        $result = (new Task(Foo::class))->run(['message' => 'foo', 'number' => 2, 'flag' => true]);

        $this->assertEquals('bar foo with flag', $result);
    }

    public function test_invalid_task_name()
    {
        $this->expectException(\RuntimeException::class);

        (new Task('class'))->run();
    }

    public function test_invalid_run()
    {
        $this->expectException(InvalidInvoke::class);

        (new Task(Foo::class))->run();
    }

    public function test_invalid_option_type()
    {
        $this->expectException(InvalidOption::class);

        new Task(InvalidOptionType::class);
    }

    public function test_untyped_option()
    {
        $this->expectException(InvalidOption::class);

        new Task(UntypedOption::class);
    }

    public function test_invalid_option_union_type()
    {
        $this->expectException(InvalidOption::class);

        new Task(UnionType::class);
    }
}
