<?php

namespace App\Tables;

use App\Models\User;
use App\TableComponents\Table;
use App\TableComponents\Columns;
use App\TableComponents\Filters;

class Users extends Table
{
    /** @var class-string $resource */
    protected ?string $resource = User::class;

    protected string $defaultSort = 'name';

    protected array $search = ['name', 'email'];

    protected bool $stickyHeader = true;
    protected bool $stickyPagination = true;

    public function columns(): array
    {
        return [
            Columns\TextColumn::make('id', 'ID')
                ->toggleable(false)
                ->stickable()
                ->width('75px'),
            Columns\TextColumn::make('name', 'Full Name')
                ->stickable(),
            Columns\TextColumn::make('email'),
            Columns\TextColumn::make('bio')->truncate(2),
            Columns\TextColumn::make('email_verified_at'),
            Columns\TextColumn::make('created_at'),
            Columns\TextColumn::make('updated_at'),
//            Columns\NumericColumn::make('visit_count', sortable: true),
//            Columns\DateColumn::make('email_verified_at'),
//            Columns\ActionColumn::new(),
        ];
    }

    public function filters(): array
    {
        return [
            Filters\TextFilter::make('name', 'Full Name'),
            Filters\TextFilter::make('email'),
//            Filters\NumericFilter::make('visit_count'),
//            Filters\BooleanFilter::make('is_admin', 'Admin'),
//            Filters\DateFilter::make('email_verified_at')->nullable(),
        ];
    }
}
