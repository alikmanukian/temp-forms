<?php

namespace App\TableComponents\Filters;

use App\TableComponents\Enums\Clause;

class BooleanFilter extends Filter
{
    protected ?Clause $defaultClause = Clause::Equals;
    protected array $options = [
        true => "True",
        false => "False"
    ];

    protected array $clauses = [
        Clause::Equals
    ];

    public function toArray(): array
    {
        return array_merge(parent::toArray(), [
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
