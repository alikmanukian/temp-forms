<?php

namespace App\TableComponents\Columns;

use App\TableComponents\Column;
use App\TableComponents\Enums\Variant;
use App\TableComponents\Traits\HasIcon;
use Illuminate\Database\Eloquent\Model;

class BadgeColumn extends Column
{
    use HasIcon;

    public mixed $variantMapping = null;
    protected string $variant = 'default';

    public function transform(Model $model): void
    {
        parent::transform($model);

        $this->setVariant($model);
    }

    /**
     * Usage:
     * BadgeColumn::make()->variant(fn(string $value, Model $model) => Variant::Green)
     *
     * If the column only has a limited set of values,
     * you can use an array to map the values.
     * The array should have the original value as the key
     * and the mapped value as the value.
     *
     * BadgeColumn::make()->variant([
     *      'is_approved' => Variant::Green,
     *      'is_pending' => Variant::Yellow,
     *      'is_rejected' => Variant::Red,
     * ])
     *
     */
    public function variant(callable|array $callback): static
    {
        $this->variantMapping = $callback;

        return $this;
    }

    private function setVariant(Model $model): void
    {
        $value = $model->{$this->name};

        if (is_callable($this->variantMapping)) {
            $this->setColumnParamToModel($model, 'variant', call_user_func($this->variantMapping, $value, $model));
        }

        if (is_array($this->variantMapping) && array_key_exists($value, $this->variantMapping)) {
            $value = $this->variantMapping[$value];
            $this->setColumnParamToModel($model, 'variant', $value instanceof Variant ? $value->value : $value);
        }

        // if the column is mutated, we need to append it to the model
        if (in_array($this->name, $model->getMutatedAttributes(), true)) {
            $this->appends[] = '_params';
        }
    }
}
