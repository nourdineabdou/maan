@extends('layouts.guest')

@section('above')
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
            <div class="relative mt-1">
                <input
                    type="password"
                    id="password"
                    name="password"
                    required
                    class="block w-full rounded-lg border border-border px-3 py-2 pe-10 text-sm focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary"
                >
                <button
                    type="button"
                    id="password-toggle"
                    tabindex="-1"
                    class="absolute inset-y-0 end-0 flex w-10 items-center justify-center text-muted hover:text-primary"
                    aria-label="{{ __('auth.password_label') }}"
                >
                    <i class="bi bi-eye"></i>
                </button>
            </div>
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

@push('scripts')
    <script>
        (function () {
            var toggle = document.getElementById('password-toggle');
            var input = document.getElementById('password');

            if (! toggle || ! input) {
                return;
            }

            toggle.addEventListener('click', function () {
                var isHidden = input.type === 'password';
                input.type = isHidden ? 'text' : 'password';
                toggle.querySelector('i').className = isHidden ? 'bi bi-eye-slash' : 'bi bi-eye';
            });
        })();
    </script>
@endpush

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
