<?php

namespace App\FormComponents;

use JsonSerializable;
use RuntimeException;

class Field implements JsonSerializable
{
    /**
     * @var array{required: bool, precognitive: bool} $attributes
     */
    protected array $attributes = [];
    protected string|array|null $rule = null;

    protected ?string $component = null;

    private function __construct(private string $name)
    {
    }

    public static function make(string $name): static
    {
        return new static($name);
    }

    public function required(): static
    {
        $this->attributes['required'] = true;

        return $this;
    }

    public function precognitive(): static
    {
        $this->attributes['precognitive'] = true;

        return $this;
    }

    public function rule(string|array $rule): static
    {
        $this->rule = $rule;

        return $this;
    }

    public function jsonSerialize(): array
    {
        if (! $this->component) {
            throw new RuntimeException('Component property is not defined');
        }

        return array_merge([
            'name' => $this->name,
            'component' => $this->component
        ], $this->attributes);
    }
}
