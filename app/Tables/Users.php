<?php

namespace App\Tables;

use App\Models\User;
use App\TableComponents\Enums\IconSize;
use App\TableComponents\Enums\ImageSize;
use App\TableComponents\Enums\Position;
use App\TableComponents\Enums\Variant;
use App\TableComponents\Icon;
use App\TableComponents\Image;
use App\TableComponents\Table;
use App\TableComponents\Columns;
use App\TableComponents\Filters;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;

class Users extends Table
{
    /** @var class-string $resource */
    protected ?string $resource = User::class;

    protected string $defaultSort = 'name';

    protected array $search = [];

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
                ->stickable()
                ->linkTo(function (Model $model) {
                    return $model->id === 1 ? [
                        'href' => 'https://google.com',
                        'target' => '_blank',
                    ] : route('dashboard');
                })
                ->image('avatar', function (Model $model, Image $image) {
                    return $image
                        ->alt($model->name)
//                        ->position(Position::End)
                        ->class('rounded-md');
                })
//                ->rightAligned()
                ->searchable(),

            Columns\BadgeColumn::make('status')
                ->variant([
                    'active' => Variant::Green,
                    'inactive' => Variant::Red
                ])
                ->icon([
                    'active' => 'airplay',
                    'inactive' => 'angry'
                ]),

            /*Columns\ImageColumn::make('avatar')->image(function (Model $model, Image $image) {
                return $image->url($model->friends->pluck('avatar'))->limit(2);
            }),*/

            Columns\BooleanColumn::make('is_verified', 'Is Verified')
                ->mapAs(function (Model $model, mixed $value) {
                    return (bool) $model->email_verified_at;
                })
                ->trueIcon('Check')
                ->centerAligned(),

            Columns\TextColumn::make('bio')->truncate(2),

            Columns\TextColumn::make('email')->notSortable()->searchable(),

            Columns\DateColumn::make('created_at')
                ->format('Y, M d'),

//            Columns\NumericColumn::make('visit_count', sortable: true),
//            Columns\DateColumn::make('email_verified_at'),
//            Columns\ActionColumn::new(),
        ];
    }

    public function filters(): array
    {
        return [
            Filters\TextFilter::make('id', 'ID')->showInHeader(),
            Filters\TextFilter::make('name', 'Full Name')->showInHeader(),
            Filters\TextFilter::make('bio')->showInHeader(),
            Filters\TextFilter::make('email')->showInHeader(),
            Filters\DropdownFilter::make('status')
                ->showInHeader()
                ->options([
                    'active' => 'Active',
                    'inactive' => 'Inactive'
                ])
                ->nullable()
                ->multiple(),
            Filters\DropdownFilter::make('email_verified_at', 'Is Verified')
                ->as('is_verified')
                ->showInHeader()
                ->options([
                    'true' => 'True',
                    'false' => 'False'
                ]),
//            Filters\NumericFilter::make('visit_count'),
//            Filters\BooleanFilter::make('is_admin', 'Admin'),
//            Filters\DateFilter::make('email_verified_at')->nullable(),
        ];
    }
}
