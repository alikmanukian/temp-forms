<?php

namespace App\TableComponents\Filters\Spatie;

class FiltersEndsWith extends FiltersContains
{
    protected function getWhereRawParameters(mixed $value, string $property, string $driver): array
    {
        $value = mb_strtolower((string) $value, 'UTF8');

        return [
            "LOWER({$property}) ".($this->negative ? 'not' : '')." LIKE ?",
            ['%'.self::escapeLike($value)],
        ];
    }
}
