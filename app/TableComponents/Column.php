<?php

namespace App\TableComponents;

use App\TableComponents\Enums\ColumnAlignment;
use App\TableComponents\Traits\HasIcon;
use App\TableComponents\Traits\HasImage;
use BadMethodCallException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use ReflectionProperty;

/**
 * @method sortable()
 * @method notSortable()
 * @method searchable()
 * @method notSearchable()
 * @method toggleable()
 * @method notToggleable()
 * @method stickable()
 * @method notStickable()
 * @method headerAlignment(ColumnAlignment $value)
 * @method alignment(ColumnAlignment $value)
 */
class Column
{
    public mixed $mapping = null;
    public array $appends = []; // for mutated attributes
    protected mixed $linkTo = null;

    private function __construct(
        protected string $name,
        protected ?string $header = null,
        protected bool $sortable = false, // todo
        protected bool $searchable = false, // todo
        protected bool $toggleable = true, // allow/disallow in columns visibility
        protected bool $stickable = false,
        protected ColumnAlignment $headerAlignment = ColumnAlignment::Left,
        protected ColumnAlignment $alignment = ColumnAlignment::Left,
        protected bool $wrap = false,
        protected int $truncate = 1, // number of lines for line-clamp
        protected string $width = 'auto',
        protected string $headerClass = '',
        protected string $cellClass = '',
        mixed $mapAs = null,
    ) {
        if (is_null($mapAs) || is_callable($mapAs) || is_array($mapAs)) {
            $this->mapping = $mapAs;
        } else {
            throw new BadMethodCallException('mapAs must be callable or array');
        }
    }

    public static function make(...$arguments): static
    {
        return (new static(...$arguments));
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getHeader(): string
    {
        return $this->header ?? Str::of($this->name)
            ->title()
            ->replace('_', ' ')
            ->toString();
    }

    public function getWidth(): string
    {
        return $this->width;
    }

    public function truncate(int $lines = 1): static
    {
        $this->truncate = $lines;
        $this->wrap = false;

        return $this;
    }

    public function align(ColumnAlignment $alignment): static
    {
        $this->alignment = $alignment;

        return $this;
    }

    public function leftAligned(): static
    {
        $this->alignment = ColumnAlignment::Left;

        return $this;
    }

    public function rightAligned(): static
    {
        $this->alignment = ColumnAlignment::Right;

        return $this;
    }

    public function centerAligned(): static
    {
        $this->alignment = ColumnAlignment::Center;

        return $this;
    }

    public function wrap(bool $value = true): static
    {
        $this->wrap = $value;
        $this->truncate = false;

        return $this;
    }

    /**
     * This allows to set properties dynamically
     * for example $column->sortable() // set as true
     * for example $column->notSortable() // set as false
     * or $column->sortable(false|true)
     */
    public function __call(string $name, array $arguments): static
    {
        $defaultBoolValue = true;

        if (Str::startsWith($name, 'not')) {
            $defaultBoolValue = false;
            $name = Str::of($name)
                ->replaceFirst('not', '')
                ->lcfirst()
                ->toString();
        }

        if (property_exists($this, $name)) {
            $reflection = new ReflectionProperty($this, $name);
            $type = $reflection->getType();

            if ($type?->getName() === 'bool') {
                $this->$name = $defaultBoolValue;
            } elseif (! empty($arguments)) {
                $this->$name = $arguments[0];
            }

            return $this;
        }

        throw new BadMethodCallException("Method [$name] doesn't exists");
    }

    /**
     * This function used when form sending in response data
     */
    public function headerInfo(): array
    {
        return [
            'name' => $this->getName(),
            'header' => $this->getHeader(),
            'width' => $this->getWidth(),
            'type' => Str::afterLast(get_class($this), '\\'),
            'options' => [
                'sortable' => $this->sortable,
                'searchable' => $this->searchable,
                'toggleable' => $this->toggleable,
                'headerAlignment' => $this->headerAlignment->value,
                'alignment' => $this->alignment->value,
                'wrap' => $this->wrap,
                'truncate' => $this->truncate,
                'stickable' => $this->stickable,
                'headerClass' => $this->headerClass,
                'cellClass' => $this->cellClass,
            ]
        ];
    }

    /**
     * Usage:
     * TextColumn::make()->mapAs(fn(string $value, Model $model) => '#'.$value)
     * TextColumn::make()->mapAs(fn(string $value, Model $model) => '#'.$model->isAdmin() ? $value : 'N/A')
     *
     * If the column only has a limited set of values,
     * you can use an array to map the values.
     * The array should have the original value as the key
     * and the mapped value as the value.
     *
     * TextColumn::make()->mapAs([
     *      'is_approved' => __('Approved'),
     *      'is_pending' => __('Waiting'),
     *      'is_rejected' => __('Rejected'),
     * ])
     *
     */
    public function mapAs(callable|array $callback): static
    {
        $this->mapping = $callback;

        return $this;
    }

    public function useMappings(Model $model): void
    {
        $value = $model->{$this->name};

        if (is_callable($this->mapping)) {
            $model->{$this->name} = call_user_func($this->mapping, $value, $model);
        } else if (is_array($this->mapping) && array_key_exists($value, $this->mapping)) {
            $model->{$this->name} = $this->mapping[$value];
        }

        // if the column is mutated, we need to append it to the model
        if (in_array($this->name, $model->getMutatedAttributes(), true)) {
            $this->appends[] = $this->name;
        }
    }

    /**
     * Rewrite this function if you need to transform final value for column
     */
    public function transform(Model $model): void
    {
        if (in_array(HasIcon::class, class_uses(static::class), true)) {
            /** @var HasIcon $this */
            $this->setIcon($model);
        }

        if (in_array(HasImage::class, class_uses(static::class), true)) {
            /** @var HasImage $this */
            $this->setImage($model);
        }

        if ($this->linkTo) {
            $link = call_user_func($this->linkTo, $model);

            if (is_string($link)) {
                $link = ['href' => $link];
            }

            $this->setColumnParamToModel($model, 'link', $link);
        }
    }

    public function linkTo(string|callable $url, array $params = []): static
    {
        if (is_string($url)) {
            $this->linkTo = static fn () => array_merge(['href' => $url], $params);
        }

        if (is_callable($url)) {
            $this->linkTo = $url;
        }

        return $this;
    }

    public function setLink()
    {

    }

    protected function setColumnParamToModel(Model $model, string $paramName, mixed $paramValue): void
    {
        $key = $this->name . '.' . $paramName;
        if (is_null($model->_customColumnsParams)) {
            $model->_customColumnsParams = [];
        }

        $params = $model->_customColumnsParams;

        Arr::set($params, $key, $paramValue);

        $model->_customColumnsParams = $params;
    }
}
