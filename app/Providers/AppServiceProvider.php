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
    }
}
