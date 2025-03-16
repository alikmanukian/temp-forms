<?php

namespace App\TableComponents;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\View\View;
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

        foreach ($table->columns() as $column) {
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
        $request = request();

        $perPageItems = [10, 25, 50, 100, 250, 500];
        $perPage = (int) $request->cookie('perPage', $perPageItems[0]);

        if (! in_array($perPage, $perPageItems)) {
            $perPage = $perPageItems[0];
        }

        $paginator = $this->builder
//            ->where('id', '<', 10)
            ->paginate(
                perPage: $perPage,
//            pageName: Str::of(get_class($this))->afterLast('\\')->lower()->toString()
            );

//        /** @var View $links */
//        $links = $paginator->links();
//        $pagination = Arr::get($links->getData(), 'elements', []);

        return [
            'pageName' => 'page', // change this to value of pageName in the future
            'data' => $paginator->items(),
            'meta' => [
                'currentPage' => $paginator->currentPage(),
                'perPage' => $paginator->perPage(),
                'total' => $paginator->total(),
                'lastPage' => $paginator->lastPage(),
                'perPageItems' => $perPageItems
            ],
           /* 'links' => [
                'first' => $paginator->url(1),
                'last' => $paginator->url($paginator->lastPage()),
                'prev' => $paginator->previousPageUrl(),
                'next' => $paginator->nextPageUrl(),
                'links' => $paginator->links(),
            ],
            'pagination' => $pagination,*/
            'headers' => collect($this->columns())
                ->map(fn (Column $column) => [
                    $column->getName() => $column->getHeader()
                ])->collapse()
        ];
    }
}
