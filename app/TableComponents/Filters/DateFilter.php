<?php

namespace App\TableComponents\Filters;

use App\TableComponents\Enums\Clause;

class DateFilter extends Filter
{
    protected ?Clause $defaultClause = Clause::Equals;

    protected array $clauses = [
        Clause::Equals,
        Clause::After,
        Clause::Before,
        Clause::Between,
    ];
}
