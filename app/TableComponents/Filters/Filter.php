<?php

namespace App\TableComponents\Filters;

use App\TableComponents\Enums\Clause;
use BadMethodCallException;

class Filter
{
    protected array $clauses = [];
    protected ?Clause $defaultClause = null;

    private function __construct(
        protected string $name,
        protected ?string $title = null
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
}
