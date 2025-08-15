<?php

namespace App\TableComponents\Filters\Spatie;

use Illuminate\Database\Eloquent\Builder;

class FiltersAfter implements \Spatie\QueryBuilder\Filters\Filter
{
    /** {@inheritdoc} */
    public function __invoke(Builder $query, $value, string $property): void
    {
        $query->whereDate($query->qualifyColumn($property), '>=', $value);
    }
}
