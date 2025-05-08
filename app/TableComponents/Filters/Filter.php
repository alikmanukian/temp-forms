<?php

namespace App\TableComponents\Filters;

use App\TableComponents\Columns\Column;
use App\TableComponents\Enums\Clause;
use App\TableComponents\Filters\Spatie\AllowedFilter;
use App\TableComponents\Filters\Spatie\FiltersIsFalse;
use App\TableComponents\Filters\Spatie\FiltersIsNotSet;
use App\TableComponents\Filters\Spatie\FiltersIsSet;
use App\TableComponents\Filters\Spatie\FiltersIsTrue;
use BadMethodCallException;
use Illuminate\Support\Str;

/**
 * @method title(string $title)
 * @method showInHeader()
 */
class Filter
{
    /**
     * @var Clause[]
     */
    protected array $clauses = [];
    protected ?Clause $defaultClause = null;

    protected ?Clause $selectedClause = null;

    /**
     * @var string|string[]|null
     */
    protected string|array|null $value = null;

    protected ?string $alias = null;

    private function __construct(
        protected string $name,
        protected ?string $title = null,
        protected bool $showInHeader = false,
    ) {
    }

    public static function make(...$arguments): static
    {
        return (new static(...$arguments));
    }

    /**
     * This allows to set properties dynamically
     */
    public function __call(string $name, array $arguments): static
    {
        if (property_exists($this, $name)) {
            $this->{$name} = $arguments[0] ?? true;

            return $this;
        }

        throw new BadMethodCallException("Method [$name] doesn't exists");
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function toArray(): array
    {
        return [
            'name' => $this->getAlias(),
            'title' => $this->title ?? Str::of($this->name)->replace('_', ' ')->title($this->name),
            'clauses' => $this->clauses(),
            'defaultClause' => $this->defaultClause?->toArray(),
            'showInHeader' => $this->showInHeader,
            'component' => class_basename(static::class),
            'value' => !in_array($this->selectedClause, [
                Clause::IsSet,
                Clause::IsNotSet,
            ], true) ? $this->value : '',
            'selectedClause' => $this->selectedClause?->toArray(),
            'selected' => ! empty($this->value) || in_array($this->selectedClause, [
                    Clause::IsSet,
                    Clause::IsNotSet,
                ], true),
            'opened' => false,
        ];
    }

    private function clauses(): array
    {
        return collect($this->clauses)
            ->map(fn (Clause $clause) => $clause->toArray())
            ->values()
            ->toArray();
    }

    public function parseRequestValue(string $tableName, string $value): void
    {
        $clauses = Clause::sortByLength($this->clauses);
        foreach ($clauses as $clause) {
            if (! Str::startsWith($value, $clause)) {
                continue;
            }

            $newValue = Str::after($value, $clause);
            $this->selectedClause = Clause::findBySearchSymbol($clause, $newValue);

            if (! is_null($this->selectedClause) && ! empty($newValue)) {
                $this->value = $newValue;
            }

            if (in_array($this->selectedClause, [Clause::IsIn, Clause::IsNotIn], true)) {
                $this->value = explode(',', $newValue);
            }

            if (in_array($this->selectedClause, [Clause::IsSet, Clause::IsNotSet, Clause::IsTrue, Clause::IsFalse], true)) {
                $this->value = 'anyValue';
            }

            break;
        }

        if (! empty($value)
            && is_null($this->selectedClause)
            && collect($this->clauses)->contains(fn ($clause) => $clause === Clause::Equals)
        ) {
            $this->selectedClause = Clause::Equals;
            $this->value = $value;
        }

        request()->merge([
            $this->getQueryParam($tableName, $this->getName()) => $this->value,
        ]);
    }

    public function getQueryParam(string $tableName, ?string $filterName = null): string
    {
        $filterName = $filterName ?? $this->getAlias();
        $queryParam = ($tableName ? $tableName . '.' : '') . $filterName;

        return 'filter.' . $queryParam;
    }

    public function getAllowedFilterMethod(): ?AllowedFilter
    {
        $field = $this->getName();

        return match ($this->selectedClause) {
            Clause::Contains => AllowedFilter::contains($field),
            Clause::DoesNotContain => AllowedFilter::doesNotContain($field),
            Clause::StartsWith => AllowedFilter::startsWith($field),
            Clause::DoesNotStartWith => AllowedFilter::doesNotStartWith($field),
            Clause::EndsWith => AllowedFilter::endsWith($field),
            Clause::DoesNotEndWith => AllowedFilter::doesNotEndWith($field),
            Clause::Equals => AllowedFilter::equals($field),
            Clause::DoesNotEqual => AllowedFilter::doesNotEqual($field),
            Clause::IsIn => AllowedFilter::equals($field),
            Clause::IsNotIn => AllowedFilter::doesNotEqual($field),
            Clause::IsSet => AllowedFilter::custom($field, new FiltersIsSet),
            Clause::IsNotSet => AllowedFilter::custom($field, new FiltersIsNotSet),
            Clause::IsTrue => AllowedFilter::custom($field, new FiltersIsTrue),
            Clause::IsFalse => AllowedFilter::custom($field, new FiltersIsFalse),
            default => null,
        };
    }

    public function nullable(): static
    {
        $this->clauses = [
            ...$this->clauses,
            Clause::IsSet,
            Clause::IsNotSet,
        ];

        return $this;
    }

    public function as(string $name): static
    {
        $this->alias = $name;

        return $this;
    }

    public function getAlias(): string
    {
        return $this->alias ?? $this->name;
    }
}
