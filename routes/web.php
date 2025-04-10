<?php

use App\Models\User;
use App\Tables\Employees;
use App\Tables\Users;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');

Route::get('dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';

Route::inertiaTable();

Route::get('tables', function () {
//    dd(Users::make()->resolve()['data'][0]->toArray());
    return Inertia::render('Users', [
        'users' => fn() => Users::make()
            ->withQuery(User::query()->with('friends')),
//        'employees' => fn() => Employees::make(),
    ]);
})->middleware(['auth']);
