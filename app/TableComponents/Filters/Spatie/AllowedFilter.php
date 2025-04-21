<?php

namespace App\TableComponents\Filters\Spatie;

class AllowedFilter extends \Spatie\QueryBuilder\AllowedFilter
{
    public static function equals(string $name, ?string $internalName = null, bool $addRelationConstraint = true, ?string $arrayValueDelimiter = null): static
    {
        static::setFilterArrayValueDelimiter($arrayValueDelimiter);

        return new static($name, new FiltersExact($addRelationConstraint), $internalName);
    }

    public static function contains(string $name, $internalName = null, bool $addRelationConstraint = true, ?string $arrayValueDelimiter = null): static
    {
        static::setFilterArrayValueDelimiter($arrayValueDelimiter);

        return new static($name, new FiltersPartial($addRelationConstraint), $internalName);
    }
}
