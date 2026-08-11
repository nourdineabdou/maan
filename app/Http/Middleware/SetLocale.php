<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $supported = config('localization.supported_locales', ['fr']);

        $locale = session('locale')
            ?? $request->user()?->preferred_locale
            ?? config('localization.default', 'fr');

        if (! in_array($locale, $supported, true)) {
            $locale = config('localization.default', 'fr');
        }

        App::setLocale($locale);

        $isRtl = in_array($locale, config('localization.rtl_locales', []), true);

        view()->share('isRtl', $isRtl);
        view()->share('currentLocale', $locale);

        return $next($request);
    }
}
