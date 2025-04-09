<?php

namespace App\Tables;

use App\Models\User;
use App\TableComponents\Enums\ImageSize;
use App\TableComponents\Enums\Variant;
use App\TableComponents\Image;
use App\TableComponents\Table;
use App\TableComponents\Columns;
use App\TableComponents\Filters;
use Illuminate\Database\Eloquent\Model;

class Users extends Table
{
    /** @var class-string $resource */
    protected ?string $resource = User::class;

    protected ?string $pageName = 'uPage';

    protected string $defaultSort = 'name';

    protected array $search = ['name', 'email'];

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
                ->image('avatar', function (Model $model, Image $image) {
                    return $image
                        ->alt($model->name)
                        ->class('rounded-lg');
                }),
            Columns\BadgeColumn::make('status')
                ->variant([
                    'active' => Variant::Green,
                    'inactive' => Variant::Red
                ])
                ->icon([
                    'active' => 'airplay',
                    'inactive' => 'angry'
                ]),
            Columns\BooleanColumn::make('is_verified', 'IsVerified')
                ->mapAs(function (mixed $value, Model $model) {
                    return (bool) $model->email_verified_at;
                })
                ->trueIcon('check'),
            Columns\TextColumn::make('email')->notSortable(),
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
