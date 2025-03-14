<?php

namespace App\TableComponents;

use Illuminate\Support\Str;

class Column
{
    private function __construct(
        protected string $name,
        protected ?string $header = null,
        protected bool $sortable = false
    ) {
    }

    public static function make(
        string $name,
        ?string $header = null,
        bool $sortable = false
    ): static
    {
        return (new static(
            name: $name,
            header: $header,
            sortable: $sortable
        ));
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getHeader(): string
    {
        return $this->header ?? Str::title($this->name);
    }
}
