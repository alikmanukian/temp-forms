<?php

namespace App\FormComponents\Fields;

use App\FormComponents\Field;

class TextField extends Field
{
    protected ?string $component = 'TextField';

    /**
     * @var array{
     *     required: bool,
     *     precognitive: bool,
     *     type: string,
     *     label?: string,
     *     value?: string|int|bool|float,
     *     class?: string
     * } $attributes
     */
    protected array $attributes = [
        'type' => 'text',
        'labelComponent' => 'Label',
        'inputComponent' => 'Input',
        'errorComponent' => 'Error'
    ];

    public function email(): static
    {
        $this->attributes['type'] = 'email';

        return $this;
    }
}
