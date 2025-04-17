<?php

namespace App\Http\Controllers;

use App\Tables\Users;
use Illuminate\Http\Request;
use Inertia\Inertia;

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
