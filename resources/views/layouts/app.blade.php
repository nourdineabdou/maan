<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ ($isRtl ?? false) ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="vapid-public-key" content="{{ config('webpush.vapid.public_key') }}">
    <title>@yield('title', __('messages.platform_name'))</title>
    <link rel="icon" type="image/png" href="{{ asset(app()->getLocale() === 'ar' ? 'logo_ar.png' : 'logo_fr.png') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-background text-text antialiased">
    <div class="flex min-h-screen">
        @include('layouts.partials.sidebar')

        <div class="flex min-h-screen flex-1 flex-col lg:ms-0">
            @include('layouts.partials.header')

            <main class="flex-1 px-4 py-6 pb-28 sm:px-6 lg:pb-6">
                @if (session('status'))
                    <div class="mb-4 rounded-lg border border-primary/30 bg-primary-light px-4 py-3 text-sm text-primary">
                        {{ session('status') }}
                    </div>
                @endif

                @yield('content')
            </main>

            @include('layouts.partials.footer')
        </div>
    </div>

    @include('layouts.partials.mobile-navigation')

    @stack('scripts')
</body>
</html>
