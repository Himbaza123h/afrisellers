<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;

class SetLocale
{
    public function handle(Request $request, Closure $next)
    {
        // Get locale from URL segment
        $locale = $request->segment(1);
        $supportedLocales = ['en', 'fr', 'sw'];

        // Check if locale is in URL and supported
        if (in_array($locale, $supportedLocales)) {
            App::setLocale($locale);
            Session::put('locale', $locale);
        } else {
            // Get locale from session or use default
            $locale = Session::get('locale', config('app.locale'));
            App::setLocale($locale);
        }

        // Share current locale with all views
        view()->share('currentLocale', App::getLocale());
        view()->share('supportedLocales', $supportedLocales);

        return $next($request);
    }
}
