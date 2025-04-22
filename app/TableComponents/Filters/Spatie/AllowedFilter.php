<?php

namespace App\TableComponents\Filters\Spatie;

class AllowedFilter extends \Spatie\QueryBuilder\AllowedFilter
{
    public static function equals(string $name, ?string $internalName = null, bool $addRelationConstraint = true, ?string $arrayValueDelimiter = null): static
    {
        static::setFilterArrayValueDelimiter($arrayValueDelimiter);

        return new static($name, new FiltersEqual($addRelationConstraint), $internalName);
    }

    public static function doesNotEqual(string $name, ?string $internalName = null, bool $addRelationConstraint = true, ?string $arrayValueDelimiter = null): static
    {
        static::setFilterArrayValueDelimiter($arrayValueDelimiter);

        return new static($name, new FiltersEqual($addRelationConstraint, true), $internalName);
    }

    public static function contains(string $name, $internalName = null, bool $addRelationConstraint = true, ?string $arrayValueDelimiter = null): static
    {
        static::setFilterArrayValueDelimiter($arrayValueDelimiter);

        return new static($name, new FiltersContains($addRelationConstraint), $internalName);
    }

    public static function doesNotContain(string $name, $internalName = null, bool $addRelationConstraint = true, ?string $arrayValueDelimiter = null): static
    {
        static::setFilterArrayValueDelimiter($arrayValueDelimiter);

        return new static($name, new FiltersContains($addRelationConstraint, true), $internalName);
    }

    public static function startsWith(string $name, $internalName = null, bool $addRelationConstraint = true, ?string $arrayValueDelimiter = null): static
    {
        static::setFilterArrayValueDelimiter($arrayValueDelimiter);

        return new static($name, new FiltersStartsWith($addRelationConstraint), $internalName);
    }

    public static function doesNotStartWith(string $name, $internalName = null, bool $addRelationConstraint = true, ?string $arrayValueDelimiter = null): static
    {
        static::setFilterArrayValueDelimiter($arrayValueDelimiter);

        return new static($name, new FiltersStartsWith($addRelationConstraint, true), $internalName);
    }

    public static function endsWith(string $name, $internalName = null, bool $addRelationConstraint = true, ?string $arrayValueDelimiter = null): static
    {
        static::setFilterArrayValueDelimiter($arrayValueDelimiter);

        return new static($name, new FiltersEndsWith($addRelationConstraint), $internalName);
    }

    public static function doesNotEndWith(string $name, $internalName = null, bool $addRelationConstraint = true, ?string $arrayValueDelimiter = null): static
    {
        static::setFilterArrayValueDelimiter($arrayValueDelimiter);

        return new static($name, new FiltersEndsWith($addRelationConstraint, true), $internalName);
    }
}
