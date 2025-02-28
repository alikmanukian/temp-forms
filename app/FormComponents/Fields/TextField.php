<?php

namespace App\FormComponents\Fields;

use App\FormComponents\Field;

class TextField extends Field
{
    protected ?string $component = 'TextField';

    /**
     * @var array{required: bool, precognitive: bool, type: string} $attributes
     */
    protected array $attributes = [
        'type' => 'text',
    ];

    public function email(): static
    {
        $this->attributes['type'] = 'email';

        return $this;
    }
}
