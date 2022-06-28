<?php

namespace Hyqo\Task\Test\Fixtures;

use Hyqo\Task\Annotation\Description;

class NormalTask
{
    /** @var WithoutOptions */
    private $bar;

    public function __construct(WithoutOptions $bar)
    {
        $this->bar = $bar;
    }

    public function __invoke(
        #[Description('Message for return')]
        string $message,
        int $number = 1,
        bool $flag = false
    ) {
        if ($flag) {
            return sprintf('%s %s with flag', $this->bar->message, $message);
        }

        return sprintf('%s %s', $this->bar->message, $message);
    }
}
