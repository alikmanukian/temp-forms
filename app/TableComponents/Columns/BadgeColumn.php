<?php

namespace App\TableComponents\Columns;

use App\TableComponents\Column;
use App\TableComponents\Enums\Variant;
use Illuminate\Database\Eloquent\Model;

class BadgeColumn extends Column
{
    public mixed $variantMapping = null;
    protected string $variant = 'default';

    public mixed $iconMapping = null;
    protected string $icon = '';

    public function transform(Model $model): void
    {
        $this->setVariant($model);
        $this->setIcon($model);
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

    /**
     * Usage:
     * BadgeColumn::make()->icon(fn(string $value, Model $model) => 'CloseIcon')
     *
     * If the column only has a limited set of values,
     * you can use an array to map the values.
     * The array should have the original value as the key
     * and the mapped value as the value.
     *
     * BadgeColumn::make()->icon([
     *      'is_approved' => 'CheckIcon',
     *      'is_pending' => 'ClockIcon',
     *      'is_rejected' => 'ExclamationTriangleIcon',
     * ])
     *
     */
    public function icon(callable|array $callback): static
    {
        $this->iconMapping = $callback;

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

    private function setIcon(Model $model): void
    {
        $value = $model->{$this->name};

        if (is_callable($this->iconMapping)) {
            $this->setColumnParamToModel($model, 'icon', call_user_func($this->iconMapping, $value, $model));
        }

        if (is_array($this->iconMapping) && array_key_exists($value, $this->iconMapping)) {
            $this->setColumnParamToModel($model, 'icon', $this->iconMapping[$value]);
        }

        // if the column is mutated, we need to append it to the model
        if (in_array($this->name, $model->getMutatedAttributes(), true)) {
            $this->appends[] = '_params';
        }
    }
}
