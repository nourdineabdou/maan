<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ ($isRtl ?? false) ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('card.verify_title') }}</title>
    <link rel="icon" type="image/png" href="{{ asset(app()->getLocale() === 'ar' ? 'logo_ar.png' : 'logo_fr.png') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="flex min-h-screen items-center justify-center bg-background px-4 py-8 text-text antialiased">
    @php
        $badge = match ($status) {
            'valid' => ['label' => __('card.verify_status_valid'), 'class' => 'bg-primary-light text-primary', 'icon' => 'bi-patch-check-fill'],
            'suspended' => ['label' => __('card.verify_status_suspended'), 'class' => 'bg-secondary/20 text-secondary', 'icon' => 'bi-pause-circle-fill'],
            'invalid' => ['label' => __('card.verify_status_invalid'), 'class' => 'bg-accent/10 text-accent', 'icon' => 'bi-x-circle-fill'],
            default => ['label' => __('card.verify_status_not_found'), 'class' => 'bg-accent/10 text-accent', 'icon' => 'bi-question-circle-fill'],
        };

        $profile = $membership?->user?->profile;
    @endphp

    <div class="w-full max-w-sm rounded-2xl border border-border bg-surface p-6 text-center shadow-sm sm:p-8">
        <img
            src="{{ asset(app()->getLocale() === 'ar' ? 'logo_ar.png' : 'logo_fr.png') }}"
            alt="{{ __('messages.platform_name') }}"
            class="animate-logo-fall mx-auto h-14 w-auto"
        >

        <h1 class="mt-4 text-lg font-semibold text-text">{{ __('card.verify_title') }}</h1>

        <span class="mt-4 inline-flex items-center gap-2 rounded-full px-4 py-1.5 text-sm font-semibold {{ $badge['class'] }}">
            <i class="bi {{ $badge['icon'] }}"></i>
            {{ $badge['label'] }}
        </span>

        @if ($membership && $status !== 'not_found')
            <div class="mt-6 space-y-3 text-start">
                @if ($profile?->photo_url)
                    <img src="{{ $profile->photo_url }}" alt="{{ $profile->full_name }}" class="mx-auto h-20 w-20 rounded-full object-cover">
                @endif

                <div class="flex items-center justify-between border-b border-border pb-2 text-sm">
                    <span class="text-muted">{{ __('card.member_label') }}</span>
                    <span class="font-medium text-text">{{ $profile?->full_name ?? $membership->user->name }}</span>
                </div>

                <div class="flex items-center justify-between border-b border-border pb-2 text-sm">
                    <span class="text-muted">{{ __('card.verify_matricule') }}</span>
                    <span class="font-semibold text-primary">{{ $membership->member_number }}</span>
                </div>

                <div class="flex items-center justify-between text-sm">
                    <span class="text-muted">{{ __('card.verify_member_since') }}</span>
                    <span class="font-medium text-text">{{ $membership->approved_at?->translatedFormat('d/m/Y') }}</span>
                </div>
            </div>
        @else
            <p class="mt-6 text-sm text-muted">{{ __('card.verify_not_found_text') }}</p>
        @endif
    </div>
</body>
</html>
