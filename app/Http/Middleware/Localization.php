<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

class Localization
{
    private const SUPPORTED_LOCALES = ['ar', 'en'];

    public function handle(Request $request, Closure $next): Response
    {
        $locale = Session::get('locale', config('app.locale', 'ar'));

        if (! in_array($locale, self::SUPPORTED_LOCALES, true)) {
            $locale = config('app.fallback_locale', 'en');
        }

        App::setLocale($locale);

        return $next($request);
    }
}
