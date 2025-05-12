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
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

/**
 * @method $this title(string $title)
 * @method $this showInHeader()
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

    protected mixed $callback = null;

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
            'value' => $this->value,
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

            if (in_array($this->selectedClause, [Clause::IsSet, Clause::IsNotSet], true)) {
                $this->value = 'anyValue';
            }

            if ($this->selectedClause === Clause::IsTrue) {
                $this->value = true;
            }

            if ($this->selectedClause === Clause::IsFalse) {
                $this->value = false;
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
            $this->getQueryParam($tableName, $this->getAlias()) => $this->value,
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
        $name = $this->getAlias();
        $internalName = $this->getName();

        if ($this->selectedClause && is_callable($this->callback)) {
            return AllowedFilter::callback($name, function (Builder $builder, mixed $value) use ($internalName) {
                return call_user_func($this->callback, $builder, $internalName, $this->selectedClause, $value);
            }, $internalName);
        }

        return match ($this->selectedClause) {
            Clause::Contains => AllowedFilter::contains($name, $internalName),
            Clause::DoesNotContain => AllowedFilter::doesNotContain($name, $internalName),
            Clause::StartsWith => AllowedFilter::startsWith($name, $internalName),
            Clause::DoesNotStartWith => AllowedFilter::doesNotStartWith($name, $internalName),
            Clause::EndsWith => AllowedFilter::endsWith($name, $internalName),
            Clause::DoesNotEndWith => AllowedFilter::doesNotEndWith($name, $internalName),
            Clause::Equals => AllowedFilter::equals($name, $internalName),
            Clause::DoesNotEqual => AllowedFilter::doesNotEqual($name, $internalName),
            Clause::IsIn => AllowedFilter::equals($name, $internalName),
            Clause::IsNotIn => AllowedFilter::doesNotEqual($name, $internalName),
            Clause::IsSet => AllowedFilter::custom($name, new FiltersIsSet, $internalName),
            Clause::IsNotSet => AllowedFilter::custom($name, new FiltersIsNotSet, $internalName),
            Clause::IsTrue => AllowedFilter::custom($name, new FiltersIsTrue, $internalName),
            Clause::IsFalse => AllowedFilter::custom($name, new FiltersIsFalse, $internalName),
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

    /**
     * Use callback for filtering column
     */
    public function applyUsing(callable $callback): static
    {
        $this->callback = $callback;

        return $this;
    }
}
