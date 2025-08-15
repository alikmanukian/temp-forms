<?php

namespace App\TableComponents\Filters\Spatie;

use Carbon\Exceptions\InvalidFormatException;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Spatie\QueryBuilder\Filters\Filter;

class FiltersBetween implements Filter
{
    /** {@inheritdoc} */
    public function __invoke(Builder $query, $value, string $property): void
    {
        if (is_string($value) && strtotime($value) !== false) {
            $query->whereDate($query->qualifyColumn($property), $value);
        }

        if (! is_array($value)) {
            return;
        }

        if (! empty($value[0])) {
            try {
                $parsedDate = Carbon::parse($value[0]);
                $query->where($query->qualifyColumn($property), '>=', $parsedDate->startOfDay()->toDateString());
            } catch (InvalidFormatException) {
            }
        }

        if (! empty($value[1])) {
            try {
                $parsedDate = Carbon::parse($value[1]);
                $query->where($query->qualifyColumn($property), '<=', $parsedDate->endOfDay()->toDateString());
            } catch (InvalidFormatException) {
            }
        }
    }
}
