<?php

namespace App\TableComponents\Filters\Spatie;

class FiltersPartial extends \Spatie\QueryBuilder\Filters\FiltersPartial
{
    protected function getWhereRawParameters(mixed $value, string $property, string $driver): array
    {
        $value = mb_strtolower((string) $value, 'UTF8');

        return [
            "{$property} LIKE ?".self::maybeSpecifyEscapeChar($driver),
            ['%'.self::escapeLike($value).'%'],
        ];
    }
}
