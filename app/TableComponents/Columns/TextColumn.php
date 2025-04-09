<?php

namespace App\TableComponents\Columns;

use App\TableComponents\Column;
use App\TableComponents\Image;
use Illuminate\Database\Eloquent\Model;

class TextColumn extends Column
{
    protected ?Image $image = null;
    protected mixed $imageCallback = null;
    protected ?string $imageAttribute = null;

    /**
     * Usage:
     * TextColumn::make()->image(fn(Model $model, Image $image) => $image->url($model->avatar_url))
     * TextColumn::make()->image('image_attribute', fn(Model $model, Image $image) => $image->rounded())
     */
    public function image(string|callable $attribute, ?callable $callback = null): static
    {
        $this->image = new Image();

        if (is_string($attribute)) {
            $this->imageAttribute = $attribute;

            if (is_callable($callback)) {
                $this->imageCallback = $callback;
            }
        } elseif (is_callable($attribute)) {
            $this->imageCallback = $attribute;
        }

        return $this;
    }

    public function transform(Model $model): void
    {
        if ($this->image) {
            $this->setImage($model);
        }
    }

    private function setImage(Model $model): void
    {
        $value = [];

        if ($this->imageAttribute) {
            $value['src'] = $model->{$this->imageAttribute};
        }

        if ($this->imageCallback && is_callable($this->imageCallback)) {
            call_user_func($this->imageCallback, $model, $this->image);
            $value = array_merge($value, $this->image->toArray());
        }

        if ($value) {
            $this->setColumnParamToModel($model, 'image', $value);
        }
    }
}
