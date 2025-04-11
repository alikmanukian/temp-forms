<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Tables\Users;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class TableController extends Controller
{
    public function __invoke(Request $request)
    {

        /*if ($request->ajax() && $request->has('filter.search')) {

        }*/

        $query = QueryBuilder::for(User::class)
            ->allowedFilters([
                AllowedFilter::partial('name') // this will be a partial match without using LOWER('%name%')
            ])
            ->getEloquentBuilder();
//            ->allowedSorts(['name', 'email'])
//            ->defaultSort('name');

        return Inertia::render('Users', [
            'users' => fn() => Users::make()
                ->withQuery($query),
//        'employees' => fn() => Employees::make(),
        ]);
    }
}
