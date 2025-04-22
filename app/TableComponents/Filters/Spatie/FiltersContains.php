<?php

namespace App\TableComponents\Filters\Spatie;

class FiltersContains extends \Spatie\QueryBuilder\Filters\FiltersPartial
{
    public function __construct(protected bool $addRelationConstraint = true, protected bool $negative = false)
    {
        parent::__construct($addRelationConstraint);
    }

    protected function getWhereRawParameters(mixed $value, string $property, string $driver): array
    {
        $value = mb_strtolower((string) $value, 'UTF8');

        return [
            "LOWER({$property}) ".($this->negative ? 'not' : '')." LIKE ?".self::maybeSpecifyEscapeChar($driver),
            ['%'.self::escapeLike($value).'%'],
        ];
    }
}
