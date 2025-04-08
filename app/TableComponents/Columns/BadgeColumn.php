<?php

namespace App\TableComponents\Columns;

use App\TableComponents\Column;
use App\TableComponents\Enums\Variant;
use Illuminate\Database\Eloquent\Model;

use function Pest\Laravel\instance;

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
        if (is_callable($this->variantMapping)) {
            $value = call_user_func($this->variantMapping, $model->{$this->name}, $model);
            $model->_params = array_merge_recursive(
                $model->_params ?? [],
                [
                    'BadgeColumn' => [
                        $this->name => [
                            'variant' => $value,
                        ]
                    ]
                ]
            );
        }

        if (is_array($this->variantMapping) && array_key_exists($model->{$this->name}, $this->variantMapping)) {
            $value = $this->variantMapping[$model->{$this->name}];
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
        if (is_callable($this->iconMapping)) {
            $value = call_user_func($this->iconMapping, $model->{$this->name}, $model);
            $model->_params = array_merge_recursive(
                $model->_params ?? [],
                [
                    'BadgeColumn' => [
                        $this->name => [
                            'icon' => $value,
                        ]
                    ]
                ]
            );
        }

        if (is_array($this->iconMapping) && array_key_exists($model->{$this->name}, $this->iconMapping)) {
            $value = $this->iconMapping[$model->{$this->name}];

            $model->_params = array_merge_recursive(
                $model->_params ?? [],
                [
                    'BadgeColumn' => [
                        $this->name => [
                            'icon' => $value,
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
        $this->useVariants($model);
        $this->useIcon($model);

        parent::useMapping($model);

    }
}
