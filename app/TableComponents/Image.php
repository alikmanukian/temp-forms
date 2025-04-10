<?php

namespace App\TableComponents;

use App\TableComponents\Enums\Position;
use App\TableComponents\Enums\ImageSize;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;

class Image
{
    protected array $style = [];
    protected array $class = [];
    protected string $alt = 'Image';
    protected string $title = '';

    protected Position $position = Position::Start;

    protected string|array|null|Collection $url = null;

    public function __construct()
    {
        $this->size(ImageSize::Medium);
    }

    public function rounded(): static
    {
        $this->class[] = 'rounded-full';
        return $this;
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

    public function size(ImageSize $imageSize): static
    {
        $this->class = array_filter($this->class, static fn($value) => ! Str::startsWith($value, 'size-'));
        $this->class[] = $imageSize->value;
        return $this;
    }

    public function small(): static
    {
        $this->size(ImageSize::Small);
        return $this;
    }

    public function medium(): static
    {
        $this->size(ImageSize::Medium);
        return $this;
    }

    public function large(): static
    {
        $this->size(ImageSize::Large);
        return $this;
    }

    public function extraLarge(): static
    {
        $this->size(ImageSize::ExtraLarge);
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

    public function url(string|array|Collection|null $url): static
    {
        if ($url instanceof Collection) {
            $url = $url->all();
        }

        $this->url = $url;
        return $this;
    }

    /**
     * Alias of url() method.
     */
    public function to(string $url): static
    {
        return $this->url($url);
    }

    public function route(string $route, array ...$parameters): static
    {
        $this->url = route($route, ...$parameters);
        return $this;
    }

    public function signedRoute(string $route, array ...$parameters): static
    {
        $this->url = URL::signedRoute($route, ...$parameters);
        return $this;
    }

    public function temporarySignedRoute(string $route, Carbon $time, array ...$parameters): static
    {
        $this->url = URL::temporarySignedRoute($route, $time, ...$parameters);
        return $this;
    }

    public function limit(int $count): static
    {
        if ($this->url instanceof Collection) {
            if ($count === 1) {
                $this->url = $this->url->first();
            } else {
                $this->url = $this->url->take($count);
            }
        } elseif (is_array($this->url)) {
            if ($count === 1) {
                $this->url = Arr::first($this->url);
            } else {
                $this->url = Arr::take($this->url, $count);
            }
        }

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

        if (!is_null($this->url)) {
            $data['url'] = $this->url;
        }

        $data['position'] = $this->position->value;

        return $data;
    }
}
