<?php

namespace App\TableComponents\Filters;

use App\TableComponents\Enums\Clause;
use Illuminate\Support\Str;
use PhpParser\Builder\Class_;

class TextFilter extends Filter
{
    protected ?Clause $defaultClause = Clause::Contains;

    protected array $clauses = [
        Clause::Contains,
        Clause::DoesNotContain,
        Clause::StartsWith,
        Clause::DoesNotStartWith,
        Clause::EndsWith,
        Clause::DoesNotEndWith,
        Clause::Equals,
        Clause::DoesNotEqual,
    ];
}
