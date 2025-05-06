<?php

namespace App\TableComponents\Filters\Spatie;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class FiltersIsNotSet implements \Spatie\QueryBuilder\Filters\Filter
{
    /** {@inheritdoc} */
    public function __invoke(Builder $query, $value, string $property): void
    {
       $query->whereNull($property);
    }
}
