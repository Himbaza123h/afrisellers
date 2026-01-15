<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Http\Middleware\SetLocale;
use App\Http\Middleware\ProductPlanMiddleware;
use App\Http\Middleware\InquiriesPlanMiddleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        //
        $middleware->web(append: [
            SetLocale::class,
        ]);

        // Register plan middleware alias
        $middleware->alias([
            'product-plan-middleware' => ProductPlanMiddleware::class,
            'inquiries-plan-middleware' => InquiriesPlanMiddleware::class,
            'auth' => \App\Http\Middleware\Authenticate::class,
            'check.plan' => \App\Http\Middleware\CheckPlanLimits::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
