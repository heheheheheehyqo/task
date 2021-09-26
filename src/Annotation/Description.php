<?php

namespace Hyqo\Task\Annotation;

#[\Attribute(\Attribute::TARGET_PARAMETER)]
class Description
{
    public function __construct(private string $text)
    {
    }

    public function getText(): string
    {
        return $this->text;
    }
}
