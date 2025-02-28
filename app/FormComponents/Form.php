<?php

namespace App\FormComponents;

use JsonSerializable;
use RuntimeException;

abstract class Form implements JsonSerializable
{
    protected ?string $actionRoute = null;

    abstract public function fields(): array;

    public function jsonSerialize(): array
    {
        if (! $this->actionRoute) {
            throw new RuntimeException('Action route is required');
        }

        return [
            'url' => route($this->actionRoute),
            'fields' => collect($this->fields())
                ->map(function (Field $field) {
                    return $field->jsonSerialize();
                })->toArray()
        ];
    }
}
