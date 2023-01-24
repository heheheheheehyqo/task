<?php

namespace Hyqo\Task;

use Hyqo\Task\Annotation\Description;
use Hyqo\Task\Exception\InvalidOptionException;

class Option
{
    protected const TYPES = ['int', 'string', 'bool'];

    protected \ReflectionParameter $reflectionParameter;

    protected string $name;

    protected string $type;

    protected ?string $description;

    protected bool $required = true;

    protected mixed $default = null;

    public function __construct(\ReflectionParameter $reflectionParameter)
    {
        $this->reflectionParameter = $reflectionParameter;
        $this->name = $this->reflectionParameter->getName();
        $this->type = $this->extractType();
        $this->description = $this->extractDescription();

        if ($this->reflectionParameter->isDefaultValueAvailable()) {
            /** @noinspection PhpUnhandledExceptionInspection */
            $this->default = $this->reflectionParameter->getDefaultValue();
            $this->required = false;
        }
    }

    private function extractType(): string
    {
        $typeReflection = $this->reflectionParameter->getType();

        if ($typeReflection instanceof \ReflectionUnionType) {
            throw new InvalidOptionException("The parameter \${$this->name} can't be a union type");
        }

        if ($typeReflection instanceof \ReflectionNamedType) {
            if (!in_array($typeReflection->getName(), self::TYPES)) {
                throw new InvalidOptionException(
                    sprintf(
                        "The parameter \${$this->name} can be only typed as: %s",
                        implode(', ', self::TYPES)
                    )
                );
            }
        } else {
            throw new InvalidOptionException(
                sprintf(
                    "The parameter \${$this->name} must be typed (%s)",
                    implode(', ', self::TYPES)
                )
            );
        }
        return $typeReflection->getName();
    }

    private function extractDescription(): ?string
    {
        $descriptionAttributes = $this->reflectionParameter->getAttributes(Description::class);
        if ($descriptionAttributes) {
            /** @var Description $descriptionAttribute */
            $descriptionAttribute = $descriptionAttributes[0]->newInstance();
            return $descriptionAttribute->getText();
        }
        return null;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function isRequired(): bool
    {
        return $this->required;
    }

    public function getDefault()
    {
        return $this->default;
    }

    public function getShortHelp(): string
    {
        return "--{$this->name}=<{$this->type}>";
    }

    public function getLongHelp(): string
    {
        return $this->getDescription() === null ?
            $this->getShortHelp() :
            "{$this->getShortHelp()} — {$this->getDescription()}";
    }
}
