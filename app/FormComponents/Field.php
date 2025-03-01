<?php

namespace App\FormComponents;

use Illuminate\Support\Str;
use JsonSerializable;
use RuntimeException;

class Field implements JsonSerializable
{
    /**
     * @var array{
     *     required: bool,
     *     precognitive: bool,
     *     label?: string,
     *     value?: string|int|bool|float,
     *     class?: string
     * } $attributes
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

    public function getName(): string
    {
        return $this->name;
    }

    public function label(string $label): static
    {
        $this->attributes['label'] = $label;

        return $this;
    }

    public function nolabel(): static
    {
        $this->attributes['label'] = null;

        return $this;
    }

    public function value(string|int|bool|float $value): static
    {
        $this->attributes['value'] = $value;

        return $this;
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

    public function getRule(): string|array|null
    {
        return $this->rule;
    }

    public function jsonSerialize(): array
    {
        if (! $this->component) {
            throw new RuntimeException('Component property is not defined');
        }

        if (!array_key_exists('label', $this->attributes)) {
            $this->attributes['label'] = Str::ucfirst($this->name);
        }

        return array_merge([
            'name' => $this->name,
            'component' => $this->component
        ], $this->attributes);
    }
}
