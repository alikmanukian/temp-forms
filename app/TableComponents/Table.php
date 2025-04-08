<?php

namespace App\TableComponents;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Cookie;
use JsonException;
use JsonSerializable;
use ReflectionClass;
use RuntimeException;

abstract class Table implements JsonSerializable
{
    protected string $defaultSort = 'id';

    protected array $search = [];

    private Builder $builder;

    /** @var class-string $resource */
    protected ?string $resource = null;

    protected ?string $pageName = 'page';

    /**
     * @var int[]|null
     */
    protected static ?array $defaultPerPageOptions = [10, 25, 50, 100, 250, 500];

    protected static bool $defaultStickyHeader = false;
    protected static bool $defaultStickyPagination = false;

    /**
     * @return list<Column>
     */
    abstract public function columns(): array;

    private function __construct()
    {
        if (! $this->resource
            || ! class_exists($this->resource)
            || ! is_subclass_of($this->resource, Model::class)) {
            throw new RuntimeException('The $resource property is not defined or it is not subtype of Builder', 400);
        }

        $this->builder = $this->resource::query();
    }

    public static function make(): static
    {
        $table = (new static());

        /*foreach ($table->columns() as $column) {
            $table->builder->addSelect($column->getName());
        }*/

        return $table;
    }

    public static function dontEncryptCookies(): array
    {
        $appClasses = glob(app_path('**/*.php'));
        $classMap = require base_path('vendor/composer/autoload_classmap.php');

        return collect(array_intersect($classMap, $appClasses))
            ->keys()
            ->filter(fn (string $class) => class_exists($class) && is_subclass_of($class, self::class))
            ->values()
            ->map(function (string $class) {
                $reflection = new ReflectionClass($class);
                $defaults = $reflection->getDefaultProperties();

                if ($defaults['pageName']) {
                    return "perPage_" . $defaults['pageName'];
                }

                return null;
            })
            ->filter()
            ->unique()
            ->values()
            ->toArray();
    }

    /**
     * This function used when form sending in response data
     */
    public function jsonSerialize(): array
    {
        return $this->resolve();
    }

    /**
     * This function used when form sending in response data
     * @throws JsonException
     */
    public function resolve(): array
    {
        $paginator = $this->getPaginated();

        $data = [
            'pageName' => $this->pageName,
            'data' => $paginator->items(),
            'stickyHeader' => $this->getStickyHeader(),
            'stickyPagination' => $this->getStickyPagination(),
            'meta' => [
                'currentPage' => $paginator->currentPage(),
                'perPage' => $paginator->perPage(),
                'total' => $paginator->total(),
                'lastPage' => $paginator->lastPage(),
                'perPageOptions' => $this->getPerPageOptions(),
            ],
            'headers' => collect($this->columns())->map(fn (Column $column) => $column->headerInfo())->toArray()
        ];

        $data['hash'] = $this->hash($data);

        return $data;
    }

    /**
     * @throws JsonException
     */
    private function hash(array $data): string
    {
        // Sort array by keys to ensure consistent order
        ksort($data);
        return hash('sha256', json_encode($data, JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE));
    }

    private function getPaginated(): LengthAwarePaginator
    {

        return $this->builder
            /*->where('id', '<', 0)*/
            ->paginate(
                perPage: $this->getPerPage(),
                pageName: $this->pageName
            )
            ->through(function ($model) {
                return $this->useMappings($model);
            });
    }

    private function useMappings(Model $model): Model
    {
        collect($this->columns())
            ->each(fn (Column $column) => $column->useMapping($model));

        return $model;
    }
    /**
     * @return int[]|null
     */
    private function getPerPageOptions(): ?array
    {
        return property_exists($this, 'perPageOptions') ? $this->perPageOptions : static::$defaultPerPageOptions;
    }

    private function getPerPage(): int
    {
        $perPageOptions = $this->getPerPageOptions() ?? [10];
        $perPage = (int)Cookie::get('perPage_' . $this->pageName, $perPageOptions[0]);

        if (! in_array($perPage, $perPageOptions)) {
            $perPage = $perPageOptions[0];
        }

        return $perPage;
    }

    private function getStickyHeader(): bool
    {
        return property_exists($this, 'stickyHeader') ? $this->stickyHeader : static::$defaultStickyHeader;
    }

    private function getStickyPagination(): bool
    {
        return property_exists($this, 'stickyPagination') ? $this->stickyPagination : static::$defaultStickyPagination;
    }

    /**
     * @param int[]|null $options
     */
    public static function defaultPerPageOptions(?array $options): void
    {
        static::$defaultPerPageOptions = $options;
    }

    public static function defaultStickyHeader(bool $value = true): void
    {
        static::$defaultStickyHeader = $value;
    }

    public static function defaultStickyPagination(bool $value = true): void
    {
        static::$defaultStickyPagination = $value;
    }
}
