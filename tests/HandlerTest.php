<?php

namespace Hyqo\Task\Test;

use Hyqo\Task\Exception\InvalidResolverException;
use Hyqo\Task\Handler;
use PHPUnit\Framework\TestCase;
use Spatie\Snapshots\MatchesSnapshots;

class HandlerTest extends TestCase
{
    use MatchesSnapshots;

    private array $normalTaskCall = ['', 'hyqo:task:test:fixtures:normal-task', '--message="test"'];

    public function test_no_task_name(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        (new Handler())->handle(['']);
    }

    public function test_run(): void
    {
        $result = (new Handler())->handle(['', 'hyqo:task:test:fixtures:without-options']);
        $this->assertEquals('bar', $result);
    }

    public function test_error_inside(): void
    {
        $tmp = tmpfile();

        $handler = new class (null, STDOUT, $tmp) extends Handler {
            protected function terminate(): void
            {
            }
        };

        $handler->handle(['bin/task', 'hyqo:task:test:fixtures:error-inside']);

        $output = read_and_close($tmp);

        $this->assertStringContainsString('Undefined', $output);
        $this->assertStringContainsString('Call: bin/task hyqo:task:test:fixtures:error-inside', $output);
    }

    public function test_exception_inside(): void
    {
        $tmp = tmpfile();

        $handler = new class (null, STDOUT, $tmp) extends Handler {
            protected function terminate(): void
            {
            }
        };

        $handler->handle(['bin/task', 'hyqo:task:test:fixtures:exception-inside']);

        $this->assertStringContainsString('Exception: error message', read_and_close($tmp));
    }

    public function test_invalid_run(): void
    {
        $tmp = tmpfile();

        $handler = new class (null, STDOUT, $tmp) extends Handler {
            protected function terminate(): void
            {
            }
        };

        $handler->handle(['', 'hyqo:task:test:fixtures:normal-task']);

        $this->assertMatchesSnapshot(read_and_close($tmp));
    }

    public function test_options(): void
    {
        $result = (new Handler())->handle(array_merge($this->normalTaskCall, ['--flag']));
        $this->assertEquals('bar "test" with flag', $result);

        $result = (new Handler())->handle(array_merge($this->normalTaskCall, ['--flag=false']));
        $this->assertEquals('bar "test"', $result);
    }

    public function test_help(): void
    {
        $tmp = tmpfile();

        $handler = new class (null, $tmp) extends Handler {
        };

        $handler->handle(array_merge($this->normalTaskCall, ['-h']));

        $this->assertMatchesSnapshot(read_and_close($tmp));
    }

    public function test_resolver(): void
    {
        $handler = new Handler();
        $handler->setResolver(function (array $chunks) {
            end($chunks);
            $chunks[key($chunks)] .= 'Task';

            return ['Hyqo', 'Task', 'Test', 'Fixtures', ...$chunks];
        });

        $result = $handler->handle(['', 'normal', '--message=foo']);

        $this->assertMatchesSnapshot($result);
    }

    public function test_incorrect_resolver(): void
    {
        $this->expectException(InvalidResolverException::class);

        $handler = new Handler();
        $handler->setResolver(static function (array $chunks) {
        });

        $handler->handle(['', 'foo']);
    }
}


function read_and_close($tmp): string
{
    $content = file_get_contents(stream_get_meta_data($tmp)['uri']);
    fclose($tmp);

    return $content;
}
