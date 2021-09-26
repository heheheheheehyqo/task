<?php

namespace Hyqo\Task;

use Hyqo\Collection\Collection;

class Help
{
    private Collection $required;
    private Collection $optional;

    public function __construct(Collection $options)
    {
        $this->required = $options->filter(fn(Option $option) => $option->isRequired());
        $this->optional = $options->filter(fn(Option $option) => !$option->isRequired());
    }

    public function generateExample(): string
    {
        return implode(
            ' ',
            array_filter(
                array_map(
                    fn(array $values) => implode(' ', $values),
                    [
                        $this->required->map(
                            fn(Option $option) => $option->getShortHelp()
                        ),
                        $this->optional->map(
                            fn(Option $option) => "[{$option->getShortHelp()}]"
                        ),
                    ]
                ),
                fn(string $chunk) => $chunk !== '',
            )
        );
    }

    public function generateDescription(): string
    {
        $lines = [
            'Example:',
            $this->generateExample()
        ];

        if (count($this->required)) {
            $lines[] = '';
            $lines[] = "Required:";

            $this->required->each(
                function (Option $option) use (&$lines) {
                    $lines[] = $option->getLongHelp();
                }
            );
        }

        if (count($this->optional)) {
            $lines[] = '';
            $lines[] = "Optional:";

            $this->optional->each(
                function (Option $option) use (&$lines) {
                    $lines[] = $option->getLongHelp();
                }
            );
        }

        return implode("\n", $lines);
    }
}
