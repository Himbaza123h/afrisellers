<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Product;
use App\Models\Load;
use App\Models\Car;
use App\Models\Showroom;
use App\Models\Tradeshow;
use App\Observers\ProductObserver;
use App\Observers\LoadObserver;
use App\Observers\CarObserver;
use App\Observers\ShowroomObserver;
use App\Observers\TradeshowObserver;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

public function boot(): void
{
    Product::observe(ProductObserver::class);
    Load::observe(LoadObserver::class);
    Car::observe(CarObserver::class);
    Showroom::observe(ShowroomObserver::class);
    Tradeshow::observe(TradeshowObserver::class);

    // Load mail settings from DB
    try {
        config([
            'mail.mailers.smtp.host'       => \App\Models\SystemSetting::get('mail_host', env('MAIL_HOST')),
            'mail.mailers.smtp.port'       => \App\Models\SystemSetting::get('mail_port', env('MAIL_PORT')),
            'mail.mailers.smtp.username'   => \App\Models\SystemSetting::get('mail_username', env('MAIL_USERNAME')),
            'mail.mailers.smtp.password'   => \App\Models\SystemSetting::get('mail_password', env('MAIL_PASSWORD')),
            'mail.mailers.smtp.encryption' => \App\Models\SystemSetting::get('mail_encryption', env('MAIL_ENCRYPTION')),
            'mail.from.address'            => \App\Models\SystemSetting::get('mail_from_address', env('MAIL_FROM_ADDRESS')),
            'mail.from.name'               => \App\Models\SystemSetting::get('mail_from_name', env('MAIL_FROM_NAME')),
        ]);
    } catch (\Exception $e) {
        // Silently fail if DB not ready yet (e.g. during fresh migrations)
    }
}
}
