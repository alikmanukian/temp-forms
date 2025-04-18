<?php

namespace App\TableComponents\Filters;

use App\TableComponents\Enums\Clause;
use BadMethodCallException;

/**
 * @method title(string $title)
 * @method showInHeader()
 */
class Filter
{
    protected array $clauses = [];
    protected ?Clause $defaultClause = null;

    private function __construct(
        protected string $name,
        protected ?string $title = null,
        protected bool $showInHeader = false,
    )
    {

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
            'title' => $this->title,
            'clauses' => array_map(fn($clause) => $clause->value, $this->clauses),
            'defaultClause' => $this->defaultClause?->value,
            'showInHeader' => $this->showInHeader,
            'component' => class_basename(static::class),
        ];
    }
}
