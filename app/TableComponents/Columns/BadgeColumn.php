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

    public function variant(callable|array $callback): static
    {
        $this->variantMapping = $callback;

        return $this;
    }

    public function useVariants(Model $model): void
    {
        $value = $model->{$this->name};

        if (is_callable($this->variantMapping)) {
            $model->_params = array_merge_recursive(
                $model->_params ?? [],
                [
                    'BadgeColumn' => [
                        $this->name => [
                            'variant' => call_user_func($this->variantMapping, $value, $model),
                        ]
                    ]
                ]
            );
        }

        if (is_array($this->variantMapping) && array_key_exists($value, $this->variantMapping)) {
            $value = $this->variantMapping[$value];
            $model->_params = array_merge_recursive(
                $model->_params ?? [],
                [
                    'BadgeColumn' => [
                        $this->name => [
                            'variant' => $value instanceof Variant ? $value->value : $value,
                        ]
                    ]
                ]
            );
        }

        // if the column is mutated, we need to append it to the model
        if (in_array($this->name, $model->getMutatedAttributes(), true)) {
            $this->appends[] = '_params';
        }
    }

    public function icon(callable|array $callback): static
    {
        $this->iconMapping = $callback;

        return $this;
    }

    public function useIcon(Model $model): void
    {
        $value = $model->{$this->name};

        if (is_callable($this->iconMapping)) {
            $model->_params = array_merge_recursive(
                $model->_params ?? [],
                [
                    'BadgeColumn' => [
                        $this->name => [
                            'icon' => call_user_func($this->iconMapping, $value, $model),
                        ]
                    ]
                ]
            );
        }

        if (is_array($this->iconMapping) && array_key_exists($value, $this->iconMapping)) {
            $model->_params = array_merge_recursive(
                $model->_params ?? [],
                [
                    'BadgeColumn' => [
                        $this->name => [
                            'icon' => $this->iconMapping[$value],
                        ]
                    ]
                ]
            );
        }

        // if the column is mutated, we need to append it to the model
        if (in_array($this->name, $model->getMutatedAttributes(), true)) {
            $this->appends[] = '_params';
        }
    }

    public function useMapping(Model $model): void
    {
        parent::useMapping($model);

        $this->useVariants($model);
        $this->useIcon($model);

        if ($this->appends) {
            $model->append(array_unique($this->appends));
        }
    }
}
