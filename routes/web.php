<?php

use App\Http\Controllers\ImageTransformationController;
use App\Http\Controllers\TableController;
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

//Route::inertiaTable();

Route::get('tables', TableController::class)->middleware(['auth']);

Route::get('images/{image}-placeholder.{extension}', ImageTransformationController::class)
    ->where('image', '[a-zA-Z0-9\-_]+')
    ->where('extension', 'png|jpg|jpeg|webp|gif|avif')
    ->name('image.placeholder');
