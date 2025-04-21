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
    protected array $clauses = [];
    protected ?Clause $defaultClause = null;

    protected ?Clause $selectedClause = null;
    protected ?string $value = null;

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
            'clauses' => array_map(static fn ($clause) => $clause->value, $this->clauses),
            'defaultClause' => $this->defaultClause?->value,
            'showInHeader' => $this->showInHeader,
            'component' => class_basename(static::class),
            'value' => $this->value,
            'selectedClause' => $this->selectedClause?->toString(),
        ];
    }

    public function parseRequestValue(string $tableName, string $value): void
    {
        $clauses = collect($this->clauses)
            ->map(fn ($clause) => $clause->value)
            ->sortByDesc(fn ($clause) => Str::length($clause))
            ->filter()
            ->values();

        foreach ($clauses as $clause) {
            if (Str::startsWith($value, $clause)) {
                $this->selectedClause = Clause::from($clause);
                $this->value = Str::after($value, $clause);
                break;
            }
        }

        if (is_null($this->selectedClause) && collect($this->clauses)->contains(
                fn ($clause) => $clause === Clause::Equals
            )) {
            $this->selectedClause = Clause::Equals;
            $this->value = $value;
        }

        if (! is_null($this->value)) {
            request()->merge([
                $this->getQueryParam($tableName) => $this->value,
            ]);
        }
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
//            Clause::DoesNotContain => ,
//            Clause::StartsWith => 'beginsWithStrict',
//            Clause::EndsWith => 'endsWithStrict',
            Clause::Equals => AllowedFilter::equals($this->name),
//            Clause::DoesNotEqual,
            default => null,
        };
    }
}
