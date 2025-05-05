<?php

namespace App\TableComponents\Filters;

use App\TableComponents\Enums\Clause;
use App\TableComponents\Filters\Spatie\AllowedFilter;
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
            'name' => $this->name,
            'title' => $this->title ?? Str::title($this->name),
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
            $this->getQueryParam($tableName) => $this->value,
        ]);
    }

    public function getQueryParam(string $tableName): string
    {
        $filterName = $this->getName();
        $queryParam = ($tableName ? $tableName . '.' : '') . $filterName;

        return 'filter.' . $queryParam;
    }

    public function getAllowedFilterMethod(): ?AllowedFilter
    {
        return match ($this->selectedClause) {
            Clause::Contains => AllowedFilter::contains($this->name),
            Clause::DoesNotContain => AllowedFilter::doesNotContain($this->name),
            Clause::StartsWith => AllowedFilter::startsWith($this->name),
            Clause::DoesNotStartWith => AllowedFilter::doesNotStartWith($this->name),
            Clause::EndsWith => AllowedFilter::endsWith($this->name),
            Clause::DoesNotEndWith => AllowedFilter::doesNotEndWith($this->name),
            Clause::Equals => AllowedFilter::equals($this->name),
            Clause::DoesNotEqual => AllowedFilter::doesNotEqual($this->name),
            Clause::IsIn => AllowedFilter::equals($this->name),
            Clause::IsNotIn => AllowedFilter::doesNotEqual($this->name),
            default => null,
        };
    }
}
