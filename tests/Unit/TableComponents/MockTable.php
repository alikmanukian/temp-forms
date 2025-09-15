<?php

namespace Tests\Unit\TableComponents;

use App\Models\User;
use App\TableComponents\Table;

/**
 * Mock table class for testing
 */
class MockTable extends Table
{
    protected ?string $resource = User::class;
    protected ?string $name = 'MockTable';
    protected string $defaultSort = 'name';
    protected bool $resizable = true;

    public function columns(): array
    {
        return [
            \App\TableComponents\Columns\TextColumn::make('id', 'ID')->sortable(),
            \App\TableComponents\Columns\TextColumn::make('name', 'Name')->searchable(),
            \App\TableComponents\Columns\TextColumn::make('email', 'Email')->searchable(),
            \App\TableComponents\Columns\BadgeColumn::make('status', 'Status'),
            \App\TableComponents\Columns\DateColumn::make('created_at', 'Created'),
        ];
    }

    public function filters(): array
    {
        return [
            \App\TableComponents\Filters\TextFilter::make('name', 'Name'),
            \App\TableComponents\Filters\TextFilter::make('email', 'Email'),
            \App\TableComponents\Filters\DropdownFilter::make('status', 'Status')
                ->options([
                    'active' => 'Active',
                    'inactive' => 'Inactive',
                ]),
        ];
    }

    public function getPublicName(): string
    {
        return \Illuminate\Support\Str::lower($this->name ?? class_basename($this));
    }
}