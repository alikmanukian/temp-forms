<?php

namespace App\TableComponents\Traits;

use App\TableComponents\Columns\BooleanColumn;
use App\TableComponents\Icon;
use Illuminate\Database\Eloquent\Model;

/**
 * @property-read string $name
 * @method setColumnParamToModel(Model $model, string $paramName, mixed $paramValue)
 */
trait HasIcon
{
    protected ?Icon $icon = null;

    protected mixed $iconCallback = null;

    /**
     * Usage:
     * TextColumn::make()->icon(fn(Model $model, Icon $icon) => $icon->icon('CloseIcon')->size(IconSize::Medium)->position(Position::End))
     *
     * If the column only has a limited set of values,
     * you can use an array to map the values.
     * The array should have the original value as the key
     * and the mapped value as the value.
     *
     * TextColumn::make()->icon([
     *      'is_approved' => 'CheckIcon',
     *      'is_pending' => 'ClockIcon',
     *      'is_rejected' => 'ExclamationTriangleIcon',
     * ])
     *
     */
    public function icon(callable|array $callback): static
    {
        $this->icon = new Icon();
        $this->iconCallback = $callback;

        return $this;
    }

    /**
     * @uses Column::setColumnParamToModel()
     */
    protected function setIcon(Model $inputModel, Model $outputModel): void
    {
        $value = [];

        if ($this->iconCallback && is_callable($this->iconCallback)) {
            call_user_func($this->iconCallback, $inputModel, $this->icon);
            $value = array_merge($value, $this->icon->toArray());
        }

        if (is_array($this->iconCallback) && array_key_exists($inputModel->{$this->name}, $this->iconCallback)) {
            $iconValue = $this->iconCallback[$inputModel->{$this->name}] ?? '';
            $value = array_merge($value, $this->icon->icon($iconValue)->toArray());
        }

        if ($value) {
            $this->setColumnParamToModel($outputModel, 'icon', $value);
        }
    }
}
