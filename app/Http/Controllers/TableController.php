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
        return Inertia::render('Users', [
            'users' => fn () => Users::make()->as('users'),
//        'employees' => fn() => Employees::make(),
        ]);
    }
}
