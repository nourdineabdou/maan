<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ ($isRtl ?? false) ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ __('messages.platform_name') }}</title>
    <link rel="icon" type="image/png" href="{{ asset(app()->getLocale() === 'ar' ? 'logo_ar.png' : 'logo_fr.png') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-background text-text antialiased">
    <div class="flex min-h-screen flex-col lg:flex-row">
        {{--
            Panneau de marque (desktop uniquement). Utilise logo_{fr,ar}_emblem.png
            (juste le blason circulaire, sans le nom de la plateforme en dessous)
            plutôt que logo_fr.png/logo_ar.png : le nom est écrit en vert foncé
            dans ces fichiers, illisible sur ce fond dégradé vert foncé — le nom
            en blanc ci-dessous (balise h1) le remplace avec un vrai contraste.
        --}}
        <div class="relative hidden overflow-hidden bg-linear-to-br from-primary-dark via-primary to-primary-dark lg:flex lg:w-5/12 lg:flex-col lg:justify-between lg:p-10 xl:w-1/2">
            <div class="pointer-events-none absolute -end-24 -top-24 h-72 w-72 rounded-full bg-secondary/25 blur-3xl"></div>
            <div class="pointer-events-none absolute -start-16 bottom-10 h-64 w-64 rounded-full bg-accent/20 blur-3xl"></div>
            <div class="pointer-events-none absolute inset-0 opacity-[0.04]" style="background-image: radial-gradient(currentColor 1px, transparent 1px); background-size: 22px 22px; color: white;"></div>

            <div class="relative z-10">
                <span class="inline-flex items-center gap-2 rounded-full border border-white/20 bg-white/10 px-3.5 py-1.5 text-xs font-semibold uppercase tracking-wide text-white/90 backdrop-blur-sm">
                    <span class="flex h-1.5 w-1.5 rounded-full bg-secondary"></span>
                    {{ __('messages.platform_name') }}
                </span>
            </div>

            <div class="relative z-10 flex flex-1 items-center">
                <div class="relative">
                    <div class="pointer-events-none absolute inset-0 -m-6 rounded-full bg-white/10 blur-2xl"></div>
                    <img
                        src="{{ asset(app()->getLocale() === 'ar' ? 'logo_ar_emblem.png' : 'logo_fr_emblem.png') }}"
                        alt="{{ __('messages.platform_name') }}"
                        class="animate-logo-fall relative h-40 w-40 drop-shadow-xl"
                    >
                </div>
            </div>

            <div class="relative z-10 mt-auto">
                <h1 class="text-3xl font-bold leading-tight text-white">
                    {{ __('messages.platform_name') }}
                </h1>

                <div class="mt-4 flex h-1.5 w-28 overflow-hidden rounded-full">
                    <span class="flex-1 bg-secondary"></span>
                    <span class="flex-1 bg-accent"></span>
                </div>

                <p class="mt-5 max-w-sm text-white/80">
                    {{ __('messages.platform_tagline') }}
                </p>

                <ul class="mt-8 space-y-3">
                    <li class="flex items-center gap-3 text-sm text-white/90">
                        <span class="flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-white/15">
                            <i class="bi bi-check-lg text-secondary"></i>
                        </span>
                        {{ __('messages.brand_feature_1') }}
                    </li>
                    <li class="flex items-center gap-3 text-sm text-white/90">
                        <span class="flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-white/15">
                            <i class="bi bi-check-lg text-secondary"></i>
                        </span>
                        {{ __('messages.brand_feature_2') }}
                    </li>
                    <li class="flex items-center gap-3 text-sm text-white/90">
                        <span class="flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-white/15">
                            <i class="bi bi-check-lg text-secondary"></i>
                        </span>
                        {{ __('messages.brand_feature_3') }}
                    </li>
                </ul>
            </div>

            <p class="relative z-10 mt-10 text-xs text-white/50">
                {{ __('messages.copyright', ['year' => date('Y')]) }}
            </p>
        </div>

        {{-- Panneau du formulaire --}}
        <div class="flex flex-1 flex-col">
            <header class="flex items-center justify-end px-4 py-4 sm:px-8">
                <div class="flex items-center gap-2 rounded-full border border-border bg-surface p-1 text-sm">
                    <a
                        href="{{ route('locale.switch', 'fr') }}"
                        class="rounded-full px-3 py-1 {{ app()->getLocale() === 'fr' ? 'bg-primary text-white' : 'text-muted' }}"
                    >
                        FR
                    </a>
                    <a
                        href="{{ route('locale.switch', 'ar') }}"
                        class="rounded-full px-3 py-1 {{ app()->getLocale() === 'ar' ? 'bg-primary text-white' : 'text-muted' }}"
                    >
                        عربي
                    </a>
                </div>
            </header>

            <main class="flex flex-1 flex-col items-center justify-center gap-6 px-4 py-6">
                <a href="{{ route('login') }}" class="flex flex-col items-center gap-3 lg:hidden">
                    <img
                        src="{{ asset(app()->getLocale() === 'ar' ? 'logo_ar.png' : 'logo_fr.png') }}"
                        alt="{{ __('messages.platform_name') }}"
                        class="animate-logo-fall h-28 w-auto drop-shadow-md sm:h-32"
                    >
                </a>

                @hasSection('above')
                    <div class="w-full max-w-3xl space-y-5">
                        @yield('above')
                    </div>
                @endif

                <div id="auth-card" class="animate-fade-in-up w-full @yield('card_width', 'max-w-md') rounded-2xl border border-border bg-surface p-6 shadow-lg sm:p-8">
                    @yield('content')
                </div>

                @hasSection('below')
                    <div class="w-full max-w-3xl">
                        @yield('below')
                    </div>
                @endif
            </main>

            <footer class="px-4 py-6 text-center text-sm text-muted lg:hidden">
                {{ __('messages.copyright', ['year' => date('Y')]) }}
            </footer>
        </div>
    </div>

    @stack('scripts')
</body>
</html>
