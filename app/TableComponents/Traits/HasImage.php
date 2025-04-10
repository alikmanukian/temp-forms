<?php

namespace App\TableComponents\Traits;

use App\TableComponents\Image;
use Illuminate\Database\Eloquent\Model;

/**
 * @method setColumnParamToModel(Model $model, string $paramName, mixed $paramValue)
 */
trait HasImage
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
        if (! $this->image instanceof Image) {
            $this->image = new Image();
        }

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

    protected function setImage(Model $inputModel, Model $outputModel): void
    {
        $value = [];

        if ($this->imageCallback && is_callable($this->imageCallback)) {
            call_user_func($this->imageCallback, $inputModel, $this->image);
            $valueFromCallback = array_merge($value, $this->image->toArray());

            if ($this->imageAttribute && isset($valueFromCallback['url']) && is_array($valueFromCallback['url'])) {
                $this->imageAttribute = null; // use multiple images
            }

            $value = $valueFromCallback;
        }

        if ($this->imageAttribute) {
            $value['url'] = $inputModel->{$this->imageAttribute};
        }

        if ($value) {
            $this->setColumnParamToModel($outputModel, 'image', $value);
        }
    }
}
