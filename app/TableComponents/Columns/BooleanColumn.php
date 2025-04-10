<?php

namespace App\TableComponents\Columns;

use App\TableComponents\Column;
use App\TableComponents\Icon;
use App\TableComponents\Traits\HasIcon;
use Illuminate\Database\Eloquent\Model;

class BooleanColumn extends Column
{
    use HasIcon;

    private static ?string $defaultTrueLabel = '';
    private static ?string $defaultFalseLabel = '';
    private static ?string $defaultTrueIcon = null;
    private static ?string $defaultFalseIcon = null;

    private ?string $trueLabel = null;
    private ?string $falseLabel = null;
    private ?string $trueIcon = null;
    private ?string $falseIcon = null;

    public static function make(...$arguments): static
    {
        $object = parent::make(...$arguments);

        if (static::$defaultTrueIcon) {
            $object->iconCallback[true] = static::$defaultTrueIcon;
        }

        if (static::$defaultFalseIcon) {
            $object->iconCallback[false] = static::$defaultFalseIcon;
        }

        return $object;
    }

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
        if (!$this->icon) {
            $this->icon = new Icon();
        }

        $this->iconCallback[true] = $icon;

        return $this;
    }

    public function falseIcon(string $icon): static
    {
        if (!$this->icon) {
            $this->icon = new Icon();
        }

        $this->iconCallback[false] = $icon;

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

    public function transform(Model $inputModel, Model $outputModel): void
    {
        parent::transform($inputModel, $outputModel);

        if (is_array($this->iconCallback) && array_key_exists((bool) $outputModel->{$this->name}, $this->iconCallback)) {
            $iconValue = $this->iconCallback[$outputModel->{$this->name}] ?? '';
            $value = $this->icon->icon($iconValue)->toArray();

            if ($value) {
                $this->setColumnParamToModel($outputModel, 'icon', $value);
            }
        }

        // rewrite model value for the boolean column
        $outputModel->{$this->name} = match((bool) $inputModel->{$this->name}) {
            true => $this->trueLabel ?? static::$defaultTrueLabel,
            false => $this->falseLabel ?? static::$defaultFalseLabel,
        };

    }
}
