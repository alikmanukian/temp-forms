<?php

namespace App\TableComponents\Filters;

use App\TableComponents\Enums\Clause;

class BooleanFilter extends Filter
{
    protected ?Clause $defaultClause = Clause::IsTrue;

    protected array $options = [
        true => 'True',
        false => 'False',
    ];

    protected array $clauses = [
        Clause::IsTrue,
        Clause::IsFalse,
    ];

    public function toArray(): array
    {
        if ($this->value === false) {
            $this->defaultClause = Clause::IsFalse;
        }

        return array_merge(parent::toArray(), [
            'value' => is_null($this->value) ? null : (bool) $this->value,
            'selected' => ! is_null($this->value),
            'options' => collect($this->options)
                ->map(fn (mixed $label, mixed $value) => [
                    'label' => $label,
                    'value' => (bool) $value,
                ])
                ->values()
                ->toArray(),
        ]);
    }

    public function falseLabel(string $label): self
    {
        $this->options[false] = $label;

        return $this;
    }

    public function trueLabel(string $label): self
    {
        $this->options[true] = $label;

        return $this;
    }
}
