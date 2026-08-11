<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        RateLimiter::for('login', function (Request $request) {
            $key = strtolower((string) $request->input('identifier')).'|'.$request->ip();

            return Limit::perMinute(5)->by($key);
        });

        // Certaines installations PHP sous Windows n'ont pas de configuration
        // OpenSSL par défaut permettant la génération/signature de clés EC,
        // nécessaires aux notifications push (VAPID). On fournit la nôtre.
        if (! getenv('OPENSSL_CONF') && is_file(storage_path('openssl.cnf'))) {
            putenv('OPENSSL_CONF='.storage_path('openssl.cnf'));
        }
    }
}
