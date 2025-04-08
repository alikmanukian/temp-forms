<?php

namespace App\TableComponents\Columns;

use App\TableComponents\Column;
use Illuminate\Database\Eloquent\Model;

class BooleanColumn extends Column
{
    private static ?string $defaultTrueLabel = '';
    private static ?string $defaultFalseLabel = '';
    private static ?string $defaultTrueIcon = null;
    private static ?string $defaultFalseIcon = null;

    private ?string $trueLabel = null;
    private ?string $falseLabel = null;
    private ?string $trueIcon = null;
    private ?string $falseIcon = null;

    public mixed $iconMapping = null;
    protected string $icon = '';

    public function trueLabel(string $label): static
    {
        $this->trueLabel = $label;
        return $this;
    }

    public function falseLabel(string $label): static
    {
        $this->falseLabel = $label;
        return $this;
    }

    public function trueIcon(string $icon): static
    {
        $this->trueIcon = $icon;
        return $this;
    }

    public function falseIcon(string $icon): static
    {
        $this->falseIcon = $icon;
        return $this;
    }

    public static function setDefaultTrueLabel(string $label): void
    {
        static::$defaultTrueLabel = $label;
    }

    public static function setDefaultFalseLabel(string $label): void
    {
        static::$defaultFalseLabel = $label;
    }

    public static function setDefaultTrueIcon(string $icon): void
    {
        static::$defaultTrueIcon = $icon;
    }

    public static function setDefaultFalseIcon(string $icon): void
    {
        static::$defaultFalseIcon = $icon;
    }

    public function useIcon(Model $model): void
    {
        if (!$this->trueIcon && !static::$defaultTrueIcon && !$this->falseIcon && !static::$defaultFalseIcon) {
            return;
        }

        $this->iconMapping = [
            true => $this->trueIcon ?? static::$defaultTrueIcon,
            false => $this->falseIcon ?? static::$defaultFalseIcon,
        ];

        $value = $model->{$this->name};

        if (array_key_exists($value, $this->iconMapping)) {
            $model->_params = array_merge_recursive(
                $model->_params ?? [],
                [
                    'BooleanColumn' => [
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

        $this->useIcon($model);

        if ($this->appends) {
            $model->append(array_unique($this->appends));
        }
    }

    public function value(Model $model): string
    {
        return match((bool) $model->{$this->name}) {
            true => $this->trueLabel ?? static::$defaultTrueLabel,
            false => $this->falseLabel ?? static::$defaultFalseLabel,
        };
    }
}
