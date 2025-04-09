<?php

namespace App\TableComponents;

use App\TableComponents\Enums\ImagePosition;
use App\TableComponents\Enums\ImageSize;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;

class Image
{
    protected array $style = [];
    protected array $class = [];
    protected string $alt = 'Image';
    protected string $title = '';

    protected ImagePosition $position = ImagePosition::Start;

    protected ?string $icon = null;
    protected ?string $url = null;

    public function __construct()
    {
        $this->size(ImageSize::MEDIUM);
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
        $this->size(ImageSize::SMALL);
        return $this;
    }

    public function medium(): static
    {
        $this->size(ImageSize::MEDIUM);
        return $this;
    }

    public function large(): static
    {
        $this->size(ImageSize::LARGE);
        return $this;
    }

    public function extraLarge(): static
    {
        $this->size(ImageSize::EXTRA_LARGE);
        return $this;
    }

    public function position(ImagePosition $position): static
    {
        $this->position = $position;
        return $this;
    }

    public function start(): static
    {
        $this->position(ImagePosition::Start);
        return $this;
    }

    public function end(): static
    {
        $this->position(ImagePosition::End);
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

    public function icon(string $icon): static
    {
        $this->icon = $icon;
        return $this;
    }

    public function url(string $url): static
    {
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

        if ($this->url) {
            $data['src'] = $this->url;
        }

        $data['position'] = $this->position->value;

        return $data;
    }
}
