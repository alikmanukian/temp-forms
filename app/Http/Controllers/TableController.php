<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Tables\Users;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class TableController extends Controller
{
    public function __invoke(Request $request)
    {
        $compoundSearch = AllowedFilter::callback('search', static function (Builder $query, string $value) {
            $query->where(function (Builder $query) use ($value) {
                $query
                    ->orWhere('name', 'LIKE', "%{$value}%")
                    ->orWhere('email', 'LIKE', "%{$value}%");
            });
        });

        $query = QueryBuilder::for(User::class)
            ->allowedFilters([
                AllowedFilter::partial('name'), // this will be a partial match without using LOWER('%name%')
                $compoundSearch
            ])
//            ->allowedSorts(['name', 'email'])
//            ->defaultSort('name')
            ->getEloquentBuilder();


        return Inertia::render('Users', [
            'users' => fn () => Users::make()
                ->withQuery($query),
//        'employees' => fn() => Employees::make(),
        ]);
    }
}
