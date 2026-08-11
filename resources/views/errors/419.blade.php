<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ in_array(app()->getLocale(), config('localization.rtl_locales', [])) ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('messages.error_419_title') }}</title>
    @vite(['resources/css/app.css'])
</head>
<body class="flex min-h-screen items-center justify-center bg-background px-4 text-text antialiased">
    <div class="w-full max-w-md rounded-2xl border border-border bg-surface p-8 text-center shadow-lg">
        <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-secondary/15 text-3xl text-secondary">
            <i class="bi bi-clock-history"></i>
        </div>

        <h1 class="mt-5 text-lg font-semibold text-text">{{ __('messages.error_419_title') }}</h1>
        <p class="mt-2 text-sm text-muted">{{ __('messages.error_419_text') }}</p>

        <button
            type="button"
            onclick="window.history.length > 1 ? window.history.back() : window.location.assign('{{ url('/') }}')"
            class="mt-6 inline-flex items-center gap-2 rounded-lg bg-primary px-5 py-2.5 text-sm font-semibold text-white hover:bg-primary-dark"
        >
            <i class="bi bi-arrow-counterclockwise"></i> {{ __('messages.error_419_retry') }}
        </button>
    </div>
</body>
</html>
