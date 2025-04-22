<?php

namespace App\TableComponents\Filters\Spatie;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class FiltersEqual extends \Spatie\QueryBuilder\Filters\FiltersExact
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
                values: $value,
                not: $this->negative
            );

            return;
        }

        $query->where(DB::raw('LOWER(' . $query->qualifyColumn($property) . ')'), ($this->negative ? '!' : '').'=', $value);
    }
}
