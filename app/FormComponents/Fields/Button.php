<?php

namespace App\FormComponents\Fields;

use JsonSerializable;
use RuntimeException;

class Button implements JsonSerializable
{
    /**
     * @var array{class?: string} $attributes
     */
    protected array $attributes = [];

    protected ?string $component = 'Button';

    private function __construct(private string $name)
    {
    }

    public static function make(string $name): static
    {
        return new static($name);
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
