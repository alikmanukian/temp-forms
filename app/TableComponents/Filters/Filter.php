<?php

namespace App\TableComponents\Filters;

class Filter
{
    private function __construct(protected string $name)
    {
        
    }

    public static function make(...$arguments): static
    {
        return (new static(...$arguments));
    }
}
