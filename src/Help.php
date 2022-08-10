<?php

namespace Hyqo\Task;

use Hyqo\Collection\Collection;

class Help
{
    /** @var Collection<Option> */
    protected $required;

    /** @var Collection<Option> */
    protected $optional;

    public function __construct(Collection $options)
    {
        $this->required = $options->filter(function (Option $option) {
            return $option->isRequired();
        });
        $this->optional = $options->filter(function (Option $option) {
            return !$option->isRequired();
        });
    }

    public function generateExample(): string
    {
        return implode(
            ' ',
            array_filter(
                array_map(
                    static function (array $values) {
                        return implode(' ', $values);
                    },
                    [
                        $this->required->toArray(
                            function (Option $option) {
                                return $option->getShortHelp();
                            }
                        ),
                        $this->optional->toArray(
                            function (Option $option) {
                                return "[{$option->getShortHelp()}]";
                            }
                        ),
                    ]
                ),
                static function (string $chunk) {
                    return $chunk !== '';
                }
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
