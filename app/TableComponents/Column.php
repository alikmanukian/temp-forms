<?php

namespace App\TableComponents;

use App\TableComponents\Enums\ColumnAlignment;
use BadMethodCallException;
use Illuminate\Support\Str;
use JsonSerializable;
use ReflectionProperty;

/**
 * @method sortable(?boolean $value)
 * @method searchable(?boolean $value)
 * @method toggleable(?boolean $value)
 * @method headerAlignment(ColumnAlignment $value)
 * @method alignment(ColumnAlignment $value)
 * @method visible(?boolean $value)
 */
class Column
{
    private function __construct(
        protected string $name,
        protected ?string $header = null,
        protected bool $sortable = false, // todo
        protected bool $searchable = false, // todo
        protected bool $toggleable = true, // allow/disallow in columns visibility
        protected ColumnAlignment $headerAlignment = ColumnAlignment::LEFT,
        protected ColumnAlignment $alignment = ColumnAlignment::LEFT,
        protected bool $wrap = false,
        protected int $truncate = 1, // number of lines for line-clamp
        protected bool $visible = true,
        protected bool $stickable = false,
        protected bool $sticked = false,
        protected string $width = 'auto',
        protected string $headerClass = '',
        protected string $cellClass = '',
    ) {
    }

    public static function make(...$arguments): static
    {
        return (new static(...$arguments));
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getHeader(): string
    {
        return $this->header ?? Str::of($this->name)
            ->title()
            ->replace('_', ' ')
            ->toString();
    }

    public function getWidth(): string
    {
        return $this->width;
    }

    public function truncate(int $lines = 1): static
    {
        $this->truncate = $lines;
        $this->wrap = false;

        return $this;
    }

    public function wrap(bool $value = true): static
    {
        $this->wrap = $value;
        $this->truncate = false;

        return $this;
    }

    /**
     * This allows to set properties dynamically
     * for example $column->sortable()
     * or $column->sortable(false|true)
     */
    public function __call(string $name, array $arguments): static
    {
        if (property_exists($this, $name)) {
            $reflection = new ReflectionProperty($this, $name);
            $type = $reflection->getType();

            if ($type?->getName() === 'bool') {
                $this->$name = $arguments[0] ?? true;
            } elseif (!empty($arguments)) {
                $this->$name = $arguments[0];
            }

            return $this;
        }

        throw new BadMethodCallException("Method [$name] doesn't exists");
    }

    /**
     * This function used when form sending in response data
     */
    public function headerInfo(): array
    {
        return [
            'name' => $this->getName(),
            'header' => $this->getHeader(),
            'width' => $this->getWidth(),
            'options' => [
                'sortable' => $this->sortable,
                'searchable' => $this->searchable,
                'toggleable' => $this->toggleable,
                'headerAlignment' => $this->headerAlignment->value,
                'alignment' => $this->alignment->value,
                'wrap' => $this->wrap,
                'truncate' => $this->truncate,
                'visible' => $this->visible,
                'stickable' => $this->stickable,
                'sticked' => $this->sticked,
                'headerClass' => $this->headerClass,
                'cellClass' => $this->cellClass,
            ]
        ];
    }
}
