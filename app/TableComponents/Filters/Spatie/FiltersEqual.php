<?php

namespace App\TableComponents\Filters\Spatie;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Spatie\QueryBuilder\Filters\FiltersExact;

class FiltersEqual extends FiltersExact
{
    public function __construct(protected bool $addRelationConstraint = true, protected bool $negative = false)
    {
        parent::__construct($addRelationConstraint);
    }

    /** {@inheritdoc} */
    public function __invoke(Builder $query, $value, string $property): void
    {
        if ($this->addRelationConstraint && $this->isRelationProperty($query, $property)) {
            $this->withRelationConstraint($query, $value, $property);

            return;
        }

        if (is_array($value)) {
            $query->whereIn(
                column: DB::raw('LOWER(' . $query->qualifyColumn($property) . ')'),
                values: array_map(static fn(string|int $v) => mb_strtolower((string) $v, 'UTF8'), $value),
                not: $this->negative
            );

            return;
        }

        $query->where(
            DB::raw('LOWER(' . $query->qualifyColumn($property) . ')'),
            ($this->negative ? '!' : '').'=',
            mb_strtolower((string) $value, 'UTF8')
        );
    }
}
