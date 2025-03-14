<?php

namespace App\TableComponents;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use RuntimeException;

abstract class Table
{
    protected string $defaultSort = 'id';

    protected array $search = [];

    private Builder $builder;

    /** @var class-string $resource */
    protected ?string $resource = null;

    private function __construct()
    {
        if (! $this->resource
            || ! class_exists($this->resource)
            || ! is_subclass_of($this->resource, Model::class)) {
            throw new RuntimeException('The $resource property is not defined or it is not subtype of Builder', 400);
        }

        $this->builder = $this->resource::query();
    }

    public static function make(): array
    {
        $table = (new static());

        foreach($table->columns() as $column) {
            $table->builder->addSelect($column->getName());
        }

        return $table->response();
    }

    /**
     * @return list<Column>
     */
    abstract public function columns(): array;

    /**
     * This function used when form sending in response data
     */
    private function response(): array
    {
        $paginator = $this->builder->paginate(20);

        return [
            'data' => $paginator->items(),
            'meta' => [
                'current_page' => $paginator->currentPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
                'last_page' => $paginator->lastPage(),
            ],
            'links' => [
                'first' => $paginator->url(1),
                'last' => $paginator->url($paginator->lastPage()),
                'prev' => $paginator->previousPageUrl(),
                'next' => $paginator->nextPageUrl(),
            ],
            'headers' => collect($this->columns())
                ->map(fn(Column $column) => [
                    $column->getName() => $column->getHeader()
                ])->collapse()
        ];
    }
}
