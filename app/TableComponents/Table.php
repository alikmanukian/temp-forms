<?php

namespace App\TableComponents;

use App\TableComponents\Columns\Column;
use App\TableComponents\Filters\Filter;
use App\TableComponents\Filters\Spatie\AllowedFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Str;
use JsonException;
use JsonSerializable;
use ReflectionClass;
use RuntimeException;
use Spatie\QueryBuilder\QueryBuilder;

/**
 * @method AllowedFilter searchUsing(Builder $query, string $value)
 */
abstract class Table implements JsonSerializable
{
    protected string $defaultSort = 'id';

    protected array $search = [];

    private Builder $builder;

    /** @var class-string $resource */
    protected ?string $resource = null;

    protected ?string $name = null;

    protected bool $resizable = true;

    /**
     * @var int[]|null
     */
    protected static ?array $defaultPerPageOptions = [10, 25, 50, 100, 250, 500];

    protected static bool $defaultStickyHeader = false;
    protected static bool $defaultStickyPagination = false;

    protected array $columns = [];

    protected array $filters = [];

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
        $table->filters = $table->filters();

        // set searchable
        $table->setSearch();

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

        // in case of multiple tables on the same page instead of ?filter[search]=something
        // we need to use ?filter[tableName][search]=something
        config(['query-builder.parameters.filter' => "filter.{$name}"]);

        return $this;
    }

    public function setSearch(): void
    {
        $searchable = [];
        
        foreach ($this->columns as $column) {
            if ($column->isSearchable()) {
                $searchable[] = $column->getName();
            }
        }
        
        $this->search = array_unique($searchable);
    }

    private function getSearchable(): array
    {
        $searchable = [];
        
        foreach ($this->columns as $column) {
            if ($column->isSearchable()) {
                $searchable[] = $column->getAlias();
            }
        }
        
        return array_unique($searchable);
    }

    private static ?array $cachedCookieNames = null;

    public static function dontEncryptCookies(): array
    {
        // Cache the result since this method is called frequently
        if (self::$cachedCookieNames !== null) {
            return self::$cachedCookieNames;
        }

        $appClasses = glob(app_path('**/*.php'));
        $classMap = require base_path('vendor/composer/autoload_classmap.php');
        $cookieNames = [];

        $intersectedClasses = array_intersect($classMap, $appClasses);
        
        foreach (array_keys($intersectedClasses) as $class) {
            if (class_exists($class) && is_subclass_of($class, self::class)) {
                try {
                    $reflection = new ReflectionClass($class);
                    $defaults = $reflection->getDefaultProperties();
                    $name = $defaults['name'] ?? class_basename($class);
                    $cookieNames[] = "perPage_" . strtolower($name);
                } catch (\ReflectionException) {
                    // Skip classes that can't be reflected
                    continue;
                }
            }
        }

        self::$cachedCookieNames = array_values(array_unique(array_filter($cookieNames)));
        
        return self::$cachedCookieNames;
    }

    /**
     * This function used when form sending in response data
     * @throws JsonException
     */
    public function jsonSerialize(): array
    {
        return $this->resolve();
    }

    private function getFilters(): Collection
    {
        $this->parseRequest();

        $allowedFilters = [];
        
        foreach ($this->filters as $filter) {
            if ($filter) {
                $allowedFilter = $filter->getAllowedFilterMethod();
                if ($allowedFilter) {
                    $allowedFilters[] = $allowedFilter;
                }
            }
        }

        return collect($allowedFilters);
    }

    private function parseRequest(): void
    {
        $request = request();
        
        foreach ($this->filters as $filter) {
            if (!$filter) {
                continue;
            }
            
            $value = $request->input($filter->getQueryParam($this->name));

            if (empty($value)) {
                continue;
            }

            if (is_string($value)) {
                $filter->parseRequestValue($this->name, urldecode($value));
            }
        }
    }

    private function getQueryBuilder(): Builder
    {
        $allowedFilters = $this->getFilters();

        // prepare global search filter
        if (! empty($this->search)) {
            $allowedFilters->push($this->getCompoundSearch());
        }

        return QueryBuilder::for($this->builder)
            ->allowedFilters($allowedFilters->toArray())
//            ->allowedSorts(['name', 'email'])
//            ->defaultSort('name')
            ->getEloquentBuilder();
    }

    private function getCompoundSearch(): ?AllowedFilter
    {
        if (method_exists($this, 'searchUsing')) {
            return AllowedFilter::callback('search', [$this, 'searchUsing']);
        }

        return AllowedFilter::callback('search', function (Builder $query, string $value) {
            $query->where(function (Builder $query) use ($value) {
                foreach ($this->search as $field) {
                    $query->orWhere($field, 'LIKE', "%{$value}%");
                }
            });
        });
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
            'stickyHeader' => $this->getStickyHeader(),
            'stickyPagination' => $this->getStickyPagination(),
            'searchable' => $this->getSearchable(),
            'resizable' => $this->resizable,
            'headers' => collect($this->columns)
                ->map(fn (Column $column) => $column->toArray())
                ->toArray(),
            'filters' => collect($this->filters)
                ->map(
                    fn (Filter $filter) => Arr::except($filter->toArray(), ['value'])
                ) // we do this to reset if filters structure changed except value
                ->toArray(),
        ];

        // 6914b414afa6210774fedcae4c93cdc94b227d254cfba5dbd0d7883e88eb6737
        // ad1d108be0504c40893d757e1e3f316173c357480b17191837c586990564ee93
        $hash = $this->hash($data);

        return array_merge($data, [
            'hash' => $hash,
            'data' => $paginator->items(),
            'filters' => collect($this->filters)
                ->map(fn (Filter $filter) => $filter->toArray())
                ->toArray(),
            'meta' => [
                'currentPage' => $paginator->currentPage(),
                'perPage' => $paginator->perPage(),
                'total' => $paginator->total(),
                'lastPage' => $paginator->lastPage(),
                'perPageOptions' => $this->getPerPageOptions(),
            ],
        ]);
    }

    private function getName(): string
    {
        return Str::lower($this->name ?? class_basename($this));
    }

    private function getPageName(): string
    {
        return 'page' . ($this->name ? '.' . $this->name : '');
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
        $builder = $this->getQueryBuilder();

        return $builder
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
        foreach ($this->columns as $column) {
            $column->useMappings($inputModel, $outputModel);
        }
    }

    private function transformValues(Model $inputModel, Model $outputModel): void
    {
        $appendList = [];
        
        foreach ($this->columns as $column) {
            $column->transform($inputModel, $outputModel);

            if ($column->appends) {
                $appendList[] = $column->appends;
            }
        }
        
        // Batch append all at once instead of one by one
        if (!empty($appendList)) {
            $outputModel->append($appendList);
        }
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

    /**
     * @return Filter[]
     */
    public function filters(): array
    {
        return [];
    }
}
