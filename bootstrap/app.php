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
    ->withSchedule(function (\Illuminate\Console\Scheduling\Schedule $schedule) {
        $schedule->command('subscriptions:expiry-warnings')->dailyAt('08:00');
    })
->withMiddleware(function (Middleware $middleware): void {
    $middleware->trustProxies(at: '*');
    $middleware->web(append: [
        SetLocale::class,
        \App\Http\Middleware\DisableLiteSpeedCache::class, // ADD THIS
        \App\Http\Middleware\CheckMaintenanceMode::class,
    ]);

        $middleware->alias([
            'product-plan-middleware'   => ProductPlanMiddleware::class,
            'inquiries-plan-middleware' => InquiriesPlanMiddleware::class,
            'auth'                      => \App\Http\Middleware\Authenticate::class,
            'check.plan'                => \App\Http\Middleware\CheckPlanLimits::class,
            'vendor.feature'            => \App\Http\Middleware\CheckVendorFeature::class,
            'admin.permission'          => \App\Http\Middleware\CheckAdminPermission::class,
            'admin.access'              => \App\Http\Middleware\CheckAdminAccess::class,
            'track.visitor'             => \App\Http\Middleware\TrackVisitor::class,
            'partner.access'            => \App\Http\Middleware\CheckPartnerAccess::class,
        ]);
    })
->withExceptions(function (Exceptions $exceptions): void {
    $exceptions->render(function (\Symfony\Component\HttpKernel\Exception\NotFoundHttpException $e, $request) {
        return response()->view('errors.404', [], 404);
    });
    $exceptions->render(function (\Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException $e, $request) {
        return response()->view('errors.403', [], 403);
    });
    $exceptions->render(function (\Throwable $e, $request) {
        if (!config('app.debug')) {
            return response()->view('errors.500', [], 500);
        }
    });
})->create();
