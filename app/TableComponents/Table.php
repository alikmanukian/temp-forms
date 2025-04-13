<?php

namespace App\TableComponents;

use App\Models\User;
use BadMethodCallException;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Str;
use JsonException;
use JsonSerializable;
use ReflectionClass;
use RuntimeException;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

abstract class Table implements JsonSerializable
{
    protected string $defaultSort = 'id';

    protected array $search = [];

    private Builder $builder;

    /** @var class-string $resource */
    protected ?string $resource = null;

    protected ?string $name = null;

    /**
     * @var int[]|null
     */
    protected static ?array $defaultPerPageOptions = [10, 25, 50, 100, 250, 500];

    protected static bool $defaultStickyHeader = false;
    protected static bool $defaultStickyPagination = false;

    protected array $columns = [];

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
        $table->columns = $table->columns();

        /*foreach ($table->columns() as $column) {
            $table->builder->addSelect($column->getName());
        }*/

        return $table;
    }

    public function withQuery(Builder $builder): static
    {
        $this->builder = $builder;

        return $this;
    }

    public function as(string $name): static
    {
        $this->name = $name;

        return $this;
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

                return "perPage_" . Str::lower($defaults['name'] ?? class_basename($class));
            })
            ->filter()
            ->unique()
            ->values()
            ->toArray();
    }

    /**
     * This function used when form sending in response data
     * @throws JsonException
     */
    public function jsonSerialize(): array
    {
        return $this->resolve();
    }

    private function getBuilder(): Builder
    {
        $compoundSearch = AllowedFilter::callback('search', static function (Builder $query, string $value) {
            $query->where(function (Builder $query) use ($value) {
                $query
                    ->orWhere('name', 'LIKE', "%{$value}%")
                    ->orWhere('email', 'LIKE', "%{$value}%");
            });
        });

        return QueryBuilder::for($this->builder)
            ->allowedFilters([
                AllowedFilter::partial('name'), // this will be a partial match without using LOWER('%name%')
                $compoundSearch
            ])
//            ->allowedSorts(['name', 'email'])
//            ->defaultSort('name')
            ->getEloquentBuilder();
    }

    /**
     * This function used when form sending in response data
     * @throws JsonException
     */
    public function resolve(): array
    {
        $paginator = $this->getPaginated();

        $data = [
            'name' => $this->getName(),
            'pageName' => $this->getPageName(),
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
            'headers' => collect($this->columns)->map(fn (Column $column) => $column->headerInfo())->toArray()
        ];

        $data['hash'] = $this->hash($data);

        return $data;
    }

    private function getName(): string
    {
        return Str::lower($this->name ?? class_basename($this));
    }

    private function getPageName(): string
    {
        return Str::camel(($this->name ? $this->name . '_' : '') . 'page');
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
        return $this->getBuilder()
            ->paginate(
                perPage: $this->getPerPage(),
                pageName: $this->getPageName()
            )
            ->through(function ($model) {
                $outputModel = $this->createOutputModel();
                $this->applyMappings($model, $outputModel); // Apply mappings for each row
                $this->transformValues($model, $outputModel); // Transform values for each row

                return $outputModel;
            });
    }

    private function createOutputModel(): Model
    {
        return new class extends Model {
            public $timestamps = false;

            /*public function getCreatedAtAttribute($value)
            {
                return \Carbon\Carbon::parse($value)->format('d/m/Y');
            }*/
        };
    }

    private function applyMappings(Model $inputModel, Model $outputModel): void
    {
        collect($this->columns)->each(fn (Column $column) => $column->useMappings($inputModel, $outputModel));
    }

    private function transformValues(Model $inputModel, Model $outputModel): void
    {
        collect($this->columns)->each(function (Column $column) use ($inputModel, $outputModel) {
            $column->transform($inputModel, $outputModel);

            if ($column->appends) {
                $outputModel->append($column->appends);
            }
        });
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
        $perPage = (int)Cookie::get('perPage_' . $this->getName(), $perPageOptions[0]);

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
