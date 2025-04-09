<?php

namespace App\TableComponents;

use App\TableComponents\Enums\IconSize;
use App\TableComponents\Enums\Position;
use Illuminate\Support\Str;

class Icon
{
    protected array $style = [];
    protected array $class = [];
    protected string $alt = 'Icon';
    protected string $title = '';

    protected Position $position = Position::Start;

    protected ?string $icon = null;

    public function __construct()
    {
        $this->size(IconSize::Medium);
    }

    public function width(int $width): static
    {
        $this->style['width'] = $width . 'px';
        return $this;
    }

    public function height(int $height): static
    {
        $this->style['height'] = $height . 'px';
        return $this;
    }

    public function dimensions(int $width, int $height): static
    {
        $this->width($width);
        $this->height($height);
        return $this;
    }

    public function size(IconSize $imageSize): static
    {
        $this->class = array_filter($this->class, static fn($value) => ! Str::startsWith($value, 'size-'));
        $this->class[] = $imageSize->value;
        return $this;
    }

    public function small(): static
    {
        $this->size(IconSize::Small);
        return $this;
    }

    public function medium(): static
    {
        $this->size(IconSize::Medium);
        return $this;
    }

    public function large(): static
    {
        $this->size(IconSize::Large);
        return $this;
    }

    public function extraLarge(): static
    {
        $this->size(IconSize::ExtraLarge);
        return $this;
    }

    public function position(Position $position): static
    {
        $this->position = $position;
        return $this;
    }

    public function start(): static
    {
        $this->position(Position::Start);
        return $this;
    }

    public function end(): static
    {
        $this->position(Position::End);
        return $this;
    }

    public function class(string $class): static
    {
        $this->class[] = $class;
        return $this;
    }

    public function alt(string $alt): static
    {
        $this->alt = $alt;
        return $this;
    }

    public function title(string $title): static
    {
        $this->title = $title;
        return $this;
    }

    public function icon(string $name): static
    {
        $this->icon = $name;
        return $this;
    }

    public function toArray(): array
    {
        $data = [];

        if ($this->class) {
            $data['class'] = implode(' ', array_unique($this->class));
        }

        if ($this->style) {
            $data['style'] = collect($this->style)
                    ->filter() // removes null, false, '' etc.
                    ->map(fn($value, $key) => "$key: $value")
                    ->implode('; ') . ';';
        }

        if ($this->alt) {
            $data['alt'] = $this->alt;
        }

        if ($this->title) {
            $data['title'] = $this->title;
        }

        if ($this->icon) {
            $data['name'] = $this->icon;
        }

        $data['position'] = $this->position->value;

        return $data;
    }
}
