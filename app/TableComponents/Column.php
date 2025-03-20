<?php

namespace App\TableComponents;

use Illuminate\Support\Str;

class Column
{
    private function __construct(
        protected string $name,
        protected ?string $header = null,
        protected bool $sortable = false,
        protected bool $searchable = false,
        protected bool $toggleable = false,
        protected bool $alignment = false,
        protected bool $wrap = false,
        protected bool $truncate = false,
        protected bool $visible = true,
        protected string|int $width = 'auto'
    ) {
        
    }

    public static function make(
        string $name,
        ?string $header = null,
        bool $sortable = false,
        string|int $width = 'auto'
    ): static
    {
        return (new static(
            name: $name,
            header: $header,
            sortable: $sortable,
            width: $width,
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

    public function getWidth(): string
    {
        if (is_int($this->width)) {
            return "{$this->width}px";
        }

        return $this->width;
    }
}
