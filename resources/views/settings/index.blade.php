@extends('layouts.app')

@section('title', __('settings.title'))

@section('content')
    <h1 class="text-xl font-semibold text-text">{{ __('settings.title') }}</h1>

    <div class="mt-6 max-w-xl space-y-6">
        <div class="rounded-2xl border border-border bg-surface p-6">
            <h2 class="text-sm font-semibold uppercase tracking-wide text-muted">{{ __('settings.account_info') }}</h2>
            <dl class="mt-3 space-y-2 text-sm">
                <div class="flex justify-between border-b border-border pb-2">
                    <dt class="text-muted">{{ __('auth.phone') }}</dt>
                    <dd class="text-text">{{ auth()->user()->phone }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-muted">{{ __('auth.email_optional') }}</dt>
                    <dd class="text-text">{{ auth()->user()->email ?? '—' }}</dd>
                </div>
            </dl>
        </div>

        <div class="rounded-2xl border border-border bg-surface p-6">
            <h2 class="text-sm font-semibold uppercase tracking-wide text-muted">{{ __('settings.push_title') }}</h2>
            <p class="mt-2 text-sm text-muted">{{ __('settings.push_description') }}</p>

            <button
                type="button" id="push-toggle-btn"
                class="mt-4 inline-flex items-center gap-2 rounded-lg bg-primary px-4 py-2 text-sm font-semibold text-white hover:bg-primary-dark"
            >
                <i class="bi bi-bell"></i>
                <span id="push-toggle-label">{{ __('settings.push_enable') }}</span>
            </button>
        </div>

        <div class="rounded-2xl border border-border bg-surface p-6">
            <h2 class="text-sm font-semibold uppercase tracking-wide text-muted">{{ __('settings.change_password') }}</h2>

            @if ($errors->any())
                <div class="mt-4 rounded-lg border border-accent/30 bg-accent/5 px-4 py-3 text-sm text-accent">
                    <ul class="list-inside list-disc space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('settings.password') }}" class="mt-4 space-y-4">
                @csrf
                @method('PUT')

                <div>
                    <label class="block text-sm font-medium text-text">{{ __('settings.current_password') }}</label>
                    <input type="password" name="current_password" required
                        class="mt-1 block w-full rounded-lg border border-border px-3 py-2 text-sm focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary">
                </div>

                <div>
                    <label class="block text-sm font-medium text-text">{{ __('settings.new_password') }}</label>
                    <input type="password" name="password" required minlength="4"
                        class="mt-1 block w-full rounded-lg border border-border px-3 py-2 text-sm focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary">
                </div>

                <div>
                    <label class="block text-sm font-medium text-text">{{ __('settings.new_password_confirmation') }}</label>
                    <input type="password" name="password_confirmation" required minlength="4"
                        class="mt-1 block w-full rounded-lg border border-border px-3 py-2 text-sm focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary">
                </div>

                <button type="submit" class="rounded-lg bg-primary px-4 py-2 text-sm font-semibold text-white hover:bg-primary-dark">
                    {{ __('settings.save') }}
                </button>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        (function () {
            const button = document.getElementById('push-toggle-btn');
            const label = document.getElementById('push-toggle-label');

            if (! button) {
                return;
            }

            const enableText = @json(__('settings.push_enable'));
            const disableText = @json(__('settings.push_disable'));
            const unsupportedText = @json(__('settings.push_unsupported'));

            async function refreshState() {
                if (! ('serviceWorker' in navigator) || ! ('PushManager' in window)) {
                    label.textContent = unsupportedText;
                    button.disabled = true;
                    return;
                }

                const registration = await navigator.serviceWorker.ready;
                const subscription = await registration.pushManager.getSubscription();
                label.textContent = subscription ? disableText : enableText;
                button.dataset.subscribed = subscription ? '1' : '0';
            }

            button.addEventListener('click', async () => {
                if (button.dataset.subscribed === '1') {
                    await window.disablePushNotifications();
                } else {
                    await window.enablePushNotifications();
                }
                refreshState();
            });

            document.addEventListener('push-subscribed', refreshState);
            document.addEventListener('push-unsubscribed', refreshState);

            refreshState();
        })();
    </script>
@endpush
