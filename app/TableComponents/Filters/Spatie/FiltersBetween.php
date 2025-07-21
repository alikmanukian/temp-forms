<?php

namespace App\TableComponents\Filters\Spatie;

use Illuminate\Database\Eloquent\Builder;

class FiltersBetween implements \Spatie\QueryBuilder\Filters\Filter
{
    /** {@inheritdoc} */
    public function __invoke(Builder $query, $value, string $property): void
    {
        if (is_string($value) && strtotime($value) !== false) {
            $query->whereDate($property, $value);
        }

        if (is_array($value)) {
            if (!empty($value[0])) {
                $query->where($property, '>=', $value[0]);
            }
            if (!empty($value[1])) {
                $query->where($property, '<=', $value[1]);
            }
            if (!empty($value[0]) && !empty($value[1])) {
                $query->where($property, $value);
            } elseif

        }

        $query->whereBetween($property, $value);
    }
}
