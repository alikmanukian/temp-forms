<?php

use App\Http\Middleware\HandleInertiaRequests;
use App\TableComponents\Table;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Foundation\Http\Middleware\HandlePrecognitiveRequests;
use Illuminate\Http\Middleware\AddLinkHeadersForPreloadedAssets;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->web(append: [
            HandleInertiaRequests::class,
            AddLinkHeadersForPreloadedAssets::class,
            HandlePrecognitiveRequests::class
        ]);

        $middleware->encryptCookies(except: Table::dontEncryptCookies()); // this cookie set from js
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
