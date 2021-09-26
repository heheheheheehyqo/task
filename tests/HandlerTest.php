<?php

namespace Hyqo\Task\Test;

use Hyqo\Task\Handler;
use PHPUnit\Framework\TestCase;
use Spatie\Snapshots\MatchesSnapshots;

class HandlerTest extends TestCase
{
    use MatchesSnapshots;

    private array $normalTaskCall = ['', 'hyqo:task:test:fixtures:normal-task', '--message="test"'];

    public function test_no_task_name()
    {
        $this->expectException(\InvalidArgumentException::class);
        $result = (new Handler())->handle(['']);
    }

    public function test_run()
    {
        $result = (new Handler())->handle(['', 'hyqo:task:test:fixtures:without-options']);
        $this->assertEquals('bar', $result);
    }

    public function test_error_inside()
    {
        $tmp = tmpfile();

        $handler = new class (errorStream: $tmp) extends Handler {
            protected function terminate(): void
            {
            }
        };

        $handler->handle(['bin/task', 'hyqo:task:test:fixtures:error-inside']);

        $this->assertStringContainsString('2: Undefined array key 1', read_and_close($tmp));
    }

    public function test_exception_inside()
    {
        $tmp = tmpfile();

        $handler = new class (errorStream: $tmp) extends Handler {
            protected function terminate(): void
            {
            }
        };

        $handler->handle(['bin/task', 'hyqo:task:test:fixtures:exception-inside']);

        $this->assertStringContainsString('Exception: error message', read_and_close($tmp));
    }

    public function test_invalid_run()
    {
        $tmp = tmpfile();

        $handler = new class (errorStream: $tmp) extends Handler {
            protected function terminate(): void
            {
            }
        };

        $handler->handle(['', 'hyqo:task:test:fixtures:normal-task']);

        $this->assertMatchesSnapshot(read_and_close($tmp));
    }

    public function test_options()
    {
        $result = (new Handler())->handle([...$this->normalTaskCall, '--flag']);
        $this->assertEquals('bar "test" with flag', $result);

        $result = (new Handler())->handle([...$this->normalTaskCall, '--flag=false']);
        $this->assertEquals('bar "test"', $result);
    }

    public function test_help()
    {
        $tmp = tmpfile();

        $handler = new class (outputStream: $tmp) extends Handler {
        };

        $handler->handle([...$this->normalTaskCall, '-h']);

        $this->assertMatchesSnapshot(read_and_close($tmp));
    }
}


function read_and_close($tmp): string
{
    $content = file_get_contents(stream_get_meta_data($tmp)['uri']);
    fclose($tmp);

    return $content;
}
