<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

/**
 * Équivalent de SetLocale pour l'API : l'API étant sans état (tokens, pas de
 * session), la langue est résolue depuis l'en-tête X-Locale envoyé par le
 * client mobile à chaque requête, avec repli sur la langue préférée de
 * l'utilisateur authentifié puis sur la langue par défaut.
 */
class SetApiLocale
{
    public function handle(Request $request, Closure $next): Response
    {
        $supported = config('localization.supported_locales', ['fr']);

        $locale = $request->header('X-Locale')
            ?? $request->user()?->preferred_locale
            ?? config('localization.default', 'fr');

        if (! in_array($locale, $supported, true)) {
            $locale = config('localization.default', 'fr');
        }

        App::setLocale($locale);

        return $next($request);
    }
}
