<?php

namespace App\Http\Controllers;

use App\Tables\Users;
use Illuminate\Http\Request;
use Inertia\Inertia;

class TableController extends Controller
{
    public function __invoke(Request $request)
    {
        /*$name = $request->string('filter.users.name')->toString();
        if (is_string($name) && !empty($name) && $name[0] === '^') {
            $request->merge([
                'filter.users.name' => substr($name, 1)
            ]);
        }*/

        return Inertia::render('Users', [
            'users' => fn () => Users::make()->as('users'),
            'additional' => [],
//        'employees' => fn() => Employees::make(),
        ]);
    }
}
