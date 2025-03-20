<?php

namespace App\Providers;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {

        Route::macro('inertiaTable', function() {
            // defined routes for execute actions and export data
            // todo: add your code here
        });

        Artisan::addCommandPaths([
            base_path('app/TableComponents/Commands'),
        ]);
    }
}
