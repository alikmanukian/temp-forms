<?php

namespace App\TableComponents\Filters;

use App\TableComponents\Enums\Clause;

class TextFilter extends Filter
{
    protected ?Clause $defaultClause = Clause::Contains;
}
