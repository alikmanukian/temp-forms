<?php

namespace App\Tables;

use App\Models\User;
use App\TableComponents\Columns;
use App\TableComponents\Enums\Clause;
use App\TableComponents\Enums\Variant;
use App\TableComponents\Filters;
use App\TableComponents\Image;
use App\TableComponents\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Users extends Table
{
    /** @var class-string */
    protected ?string $resource = User::class;

    protected string $defaultSort = 'name';

    protected bool $stickyHeader = true;

    protected bool $stickyPagination = true;

    public function columns(): array
    {
        return [
            Columns\TextColumn::make('id', 'ID')
                ->notToggleable()
                ->sortable()
                ->stickable()
                ->width('75px'),

            Columns\TextColumn::make('name', 'Full Name')
                ->as('full_name')
                ->stickable()
                ->linkTo(fn (Model $model) => $model->id === 1 ? [
                    'href' => 'https://google.com',
                    'target' => '_blank',
                ] : route('dashboard'))
                ->image('avatar', fn (Model $model, Image $image) => $image
                    ->alt($model->name)
//                        ->position(Position::End)
                    ->class('rounded-md'))
//                ->rightAligned()
                ->searchable(),

            Columns\BadgeColumn::make('status')
                ->variant([
                    'active' => Variant::Green,
                    'inactive' => Variant::Red,
                ])
                ->icon([
                    'active' => 'airplay',
                    'inactive' => 'angry',
                ]),

            /*Columns\ImageColumn::make('avatar')->image(function (Model $model, Image $image) {
                return $image->url($model->friends->pluck('avatar'))->limit(2);
            }),*/

            Columns\BooleanColumn::make('email_verified_at', 'Is Verified')
                ->as('is_verified')
                ->mapAs(function (Model $model, mixed $value) {
                    return (bool) $value;
                })
                // ->trueLabel('Yes')
                // ->falseLabel('No')
                ->trueIcon('Check')
                ->centerAligned(),

            Columns\TextColumn::make('bio')->truncate(2),

            Columns\TextColumn::make('email')->notSortable()->searchable(),

            Columns\DateColumn::make('created_at')
                ->format('Y, M d'),

            //            Columns\NumericColumn::make('visit_count', sortable: true),
            //            Columns\ActionColumn::new(),
        ];
    }

    public function filters(): array
    {
        return [
            Filters\TextFilter::make('id', 'ID')->showInHeader(),
            Filters\TextFilter::make('name', 'Full Name')
                ->as('full_name')
                ->showInHeader(),
            Filters\TextFilter::make('bio')->showInHeader(),
            Filters\TextFilter::make('email')->showInHeader(),
            Filters\DropdownFilter::make('status')
                ->showInHeader()
                ->options([
                    'active' => 'Active',
                    'inactive' => 'Inactive',
                ])
                ->nullable()
                ->multiple(),
            Filters\BooleanFilter::make('email_verified_at', 'Is Verified')
                ->as('is_verified')
                ->applyUsing(function (Builder $query, string $attribute, Clause $clause, mixed $value) {
                    if ($value) {
                        $query->whereNotNull($attribute);
                    } else {
                        $query->whereNull($attribute);
                    }
                })
                ->showInHeader()
                ->trueLabel('Yes')
                ->falseLabel('No'),
            //            Filters\NumericFilter::make('visit_count'),
            //            Filters\BooleanFilter::make('is_admin', 'Admin'),
            Filters\DateFilter::make('created_at')
                ->showInHeader()
                ->nullable(),
        ];
    }
}
