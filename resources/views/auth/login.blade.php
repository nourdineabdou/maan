@extends('layouts.guest')

@section('above')
    <div class="flex justify-center">
        <a
            href="#auth-card"
            class="inline-flex items-center gap-1.5 rounded-full border border-border bg-surface px-4 py-1.5 text-xs font-semibold text-primary shadow-sm transition-colors hover:bg-primary-light"
        >
            {{ __('auth.jump_to_login') }} <i class="bi bi-arrow-down"></i>
        </a>
    </div>

    <div class="rounded-2xl border border-border bg-surface p-6 shadow-sm sm:p-8">
        <h2 class="flex items-center justify-center gap-2 text-center text-sm font-bold uppercase tracking-wide text-text">
            <i class="bi bi-bar-chart-fill text-primary"></i>
            {{ __('messages.stats_title') }}
        </h2>

        <div class="mt-5 grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
            <div class="rounded-xl border border-border bg-background p-4 text-center shadow-sm">
                <span class="mx-auto flex h-11 w-11 items-center justify-center rounded-full bg-linear-to-br from-primary/20 to-primary/5 text-lg text-primary">
                    <i class="bi bi-people-fill"></i>
                </span>
                <p class="mt-3 text-3xl font-bold text-primary">{{ $stats['total_members'] }}</p>
                <p class="mt-1 text-sm font-medium text-text/70">{{ __('messages.stats_total_members') }}</p>
            </div>

            <div class="rounded-xl border border-border bg-background p-4 text-center shadow-sm">
                <span class="mx-auto flex h-11 w-11 items-center justify-center rounded-full bg-linear-to-br from-secondary/25 to-secondary/5 text-lg text-secondary">
                    <i class="bi bi-briefcase-fill"></i>
                </span>
                <p class="mt-3 text-3xl font-bold text-secondary">{{ $stats['sector_private'] }}</p>
                <p class="mt-1 text-sm font-medium text-text/70">{{ __('messages.stats_sector_private') }}</p>
            </div>

            <div class="rounded-xl border border-border bg-background p-4 text-center shadow-sm">
                <span class="mx-auto flex h-11 w-11 items-center justify-center rounded-full bg-linear-to-br from-primary/20 to-primary/5 text-lg text-primary">
                    <i class="bi bi-bank2"></i>
                </span>
                <p class="mt-3 text-3xl font-bold text-primary">{{ $stats['sector_public'] }}</p>
                <p class="mt-1 text-sm font-medium text-text/70">{{ __('messages.stats_sector_public') }}</p>
            </div>

            <div class="rounded-xl border border-border bg-background p-4 text-center shadow-sm">
                <span class="mx-auto flex h-11 w-11 items-center justify-center rounded-full bg-linear-to-br from-accent/20 to-accent/5 text-lg text-accent">
                    <i class="bi bi-person-dash-fill"></i>
                </span>
                <p class="mt-3 text-3xl font-bold text-accent">{{ $stats['unemployed'] }}</p>
                <p class="mt-1 text-sm font-medium text-text/70">{{ __('messages.stats_unemployed') }}</p>
            </div>
        </div>

        <div class="mt-6 border-t border-border pt-5">
            <p class="text-xs font-semibold uppercase tracking-wide text-text/70">
                {{ __('messages.stats_by_region') }}
            </p>

            @if (count($stats['by_region']) > 0)
                <ul class="mt-3 space-y-2">
                    @foreach ($stats['by_region'] as $region)
                        <li class="flex items-center justify-between rounded-lg bg-background px-3 py-2 text-sm">
                            <span class="font-medium text-text">{{ $region['name'] }}</span>
                            <span class="font-bold text-primary">{{ $region['total'] }}</span>
                        </li>
                    @endforeach
                </ul>
            @else
                <p class="mt-3 text-sm text-muted">{{ __('messages.stats_no_region_data') }}</p>
            @endif
        </div>
    </div>

    @if ($announcement)
        <div
            id="announcement-banner"
            data-announcement-id="{{ $announcement->id }}"
            class="relative overflow-hidden rounded-2xl bg-linear-to-br from-primary via-primary to-primary-dark p-5 text-white shadow-md sm:p-6"
        >
            <div class="pointer-events-none absolute -end-8 -top-8 h-32 w-32 rounded-full bg-secondary/25 blur-3xl"></div>

            <div class="relative z-10 flex items-start gap-4">
                <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full bg-white/20">
                    <i class="bi bi-megaphone-fill text-lg"></i>
                </span>

                <div class="min-w-0 flex-1">
                    <p class="text-[11px] font-bold uppercase tracking-wide text-secondary">{{ __('announcements.badge_label') }}</p>
                    <p class="mt-0.5 text-lg font-bold leading-snug">{{ $announcement->getTranslation('title') }}</p>
                    <p class="mt-1 text-sm font-medium leading-snug text-white/95">{{ $announcement->getTranslation('message') }}</p>
                </div>

                <button
                    type="button" id="announcement-dismiss"
                    class="shrink-0 rounded-full p-1.5 text-white/70 transition-colors hover:bg-white/15 hover:text-white"
                    aria-label="{{ __('announcements.dismiss') }}"
                >
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>

            @if ($announcement->images->isNotEmpty())
                <div class="relative z-10 mt-4 flex gap-2.5 overflow-x-auto pb-1">
                    @foreach ($announcement->images as $image)
                        <button
                            type="button" data-announcement-image-trigger data-image-src="{{ $image->url }}"
                            class="shrink-0"
                        >
                            <img
                                src="{{ $image->url }}" alt=""
                                class="h-24 w-32 rounded-lg border border-white/20 object-cover shadow-sm transition-transform hover:scale-[1.03] sm:h-28 sm:w-36"
                            >
                        </button>
                    @endforeach
                </div>
            @endif
        </div>
    @endif

    <div
        id="announcement-image-modal"
        class="fixed inset-0 z-50 hidden items-center justify-center bg-black/70 p-4"
    >
        <div class="relative h-96 w-full max-w-lg">
            <button
                type="button" id="announcement-image-modal-close"
                class="absolute -end-2 -top-2 flex h-8 w-8 items-center justify-center rounded-full bg-white text-text shadow-md hover:bg-background"
                aria-label="{{ __('announcements.dismiss') }}"
            >
                <i class="bi bi-x-lg text-sm"></i>
            </button>
            <img id="announcement-image-modal-img" src="" alt="" class="h-full w-full rounded-xl object-contain">
        </div>
    </div>
@endsection

@section('content')
    <h1 class="text-xl font-semibold text-primary">{{ __('auth.login_title') }}</h1>
    <p class="mt-1 text-sm text-muted">{{ __('auth.login_subtitle') }}</p>

    @if ($errors->any())
        <div class="mt-4 rounded-lg border border-accent/30 bg-accent/5 px-4 py-3 text-sm text-accent">
            <ul class="list-inside list-disc space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('login') }}" class="mt-6 space-y-4">
        @csrf

        <div>
            <label for="identifier" class="block text-sm font-medium text-text">
                {{ __('auth.login_identifier') }}
            </label>
            <input
                type="text"
                id="identifier"
                name="identifier"
                value="{{ old('identifier') }}"
                placeholder="{{ __('auth.login_identifier_placeholder') }}"
                required
                autofocus
                class="mt-1 block w-full rounded-lg border border-border px-3 py-2 text-sm focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary"
            >
        </div>

        <div>
            <label for="password" class="block text-sm font-medium text-text">
                {{ __('auth.password_label') }}
            </label>
            <input
                type="password"
                id="password"
                name="password"
                required
                class="mt-1 block w-full rounded-lg border border-border px-3 py-2 text-sm focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary"
            >
        </div>

        <div class="flex items-center justify-between text-sm">
            <label class="flex items-center gap-2 text-muted">
                <input type="checkbox" name="remember" class="rounded border-border text-primary focus:ring-primary">
                {{ __('auth.remember_me') }}
            </label>

            <a href="#" class="text-primary hover:underline">{{ __('auth.forgot_password') }}</a>
        </div>

        <button
            type="submit"
            class="w-full rounded-lg bg-primary px-4 py-2.5 text-sm font-semibold text-white transition-colors hover:bg-primary-dark"
        >
            {{ __('auth.login_button') }}
        </button>
    </form>

    <p class="mt-6 text-center text-sm text-muted">
        {{ __('auth.no_account') }}
        <a href="{{ route('register') }}" class="font-medium text-primary hover:underline">
            {{ __('auth.create_account') }}
        </a>
    </p>
@endsection

@if ($announcement)
    @push('scripts')
        <script>
            (function () {
                var banner = document.getElementById('announcement-banner');
                var dismissKey = 'dismissed-announcement-{{ $announcement->id }}';

                if (! banner) {
                    return;
                }

                if (localStorage.getItem(dismissKey)) {
                    banner.remove();
                    return;
                }

                document.getElementById('announcement-dismiss')?.addEventListener('click', function () {
                    localStorage.setItem(dismissKey, '1');
                    banner.remove();
                });

                var modal = document.getElementById('announcement-image-modal');
                var modalImg = document.getElementById('announcement-image-modal-img');

                function closeModal() {
                    modal.classList.add('hidden');
                    modal.classList.remove('flex');
                    modalImg.src = '';
                }

                document.querySelectorAll('[data-announcement-image-trigger]').forEach(function (trigger) {
                    trigger.addEventListener('click', function () {
                        modalImg.src = trigger.dataset.imageSrc;
                        modal.classList.remove('hidden');
                        modal.classList.add('flex');
                    });
                });

                document.getElementById('announcement-image-modal-close')?.addEventListener('click', closeModal);

                modal.addEventListener('click', function (event) {
                    if (event.target === modal) {
                        closeModal();
                    }
                });
            })();
        </script>
    @endpush
@endif
