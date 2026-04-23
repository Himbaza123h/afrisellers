<?php

namespace App\Helpers;

class LocaleHelper
{
    public static function route($name, $parameters = [], $absolute = true)
    {
        $locale = app()->getLocale();
        $defaultLocale = config('app.locale');

        // Don't add locale prefix if it's the default locale
        if ($locale === $defaultLocale) {
            return route($name, $parameters, $absolute);
        }

        return route($name, array_merge(['locale' => $locale], $parameters), $absolute);
    }
}
