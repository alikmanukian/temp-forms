<?php

namespace App\TableComponents\Filters;

use App\TableComponents\Enums\Clause;

class DropdownFilter extends Filter
{
    protected ?Clause $defaultClause = Clause::IsIn;
    protected array $options = [];
    protected bool $multiple = false;

    protected array $clauses = [
        Clause::Equals,
        Clause::DoesNotEqual,
        Clause::IsIn,
        Clause::IsNotIn,
    ];

    public function options(array $options): static
    {
        $this->options = $options;

        return $this;
    }

    public function multiple(): static
    {
        $this->multiple = true;
        return $this;
    }

    public function toArray(): array
    {
        return array_merge(parent::toArray(), [
            'multiple' => $this->multiple,
            'options' => collect($this->options)
                ->map(fn (mixed $value, mixed $key) => [
                    'label' => $value,
                    'value' => $key,
                ])
                ->values()
                ->toArray()
        ]);
    }
}
