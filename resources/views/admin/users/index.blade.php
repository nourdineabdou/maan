@extends('layouts.app')

@section('title', __('admin_users.title'))

@section('content')
    <div class="flex flex-wrap items-center justify-between gap-3">
        <div>
            <h1 class="text-xl font-semibold text-text">{{ __('admin_users.title') }}</h1>
            <p class="mt-1 text-sm text-muted">{{ __('admin_users.subtitle') }}</p>
        </div>
    </div>

    @if (session('status'))
        <div class="mt-4 rounded-lg border border-primary/30 bg-primary-light px-4 py-3 text-sm text-primary">
            {{ session('status') }}
        </div>
    @endif

    <div class="mt-6 grid gap-6 lg:grid-cols-[minmax(0,1fr)_minmax(320px,400px)]">
        <section class="rounded-2xl border border-border bg-surface p-5">
            <h2 class="text-sm font-semibold uppercase tracking-wide text-muted">{{ __('admin_users.existing') }}</h2>
            <div class="mt-4 space-y-3">
                @forelse ($administrators as $administrator)
                    <div class="flex items-center justify-between gap-3 rounded-xl border border-border px-4 py-3">
                        <div>
                            <p class="font-medium text-text">{{ $administrator->name }}</p>
                            <p class="text-sm text-muted">{{ $administrator->email ?? $administrator->phone }}</p>
                        </div>
                        <span class="rounded-full bg-primary-light px-2.5 py-1 text-xs font-semibold text-primary">
                            {{ __('members.role_administrateur') }}
                        </span>
                    </div>
                @empty
                    <p class="text-sm text-muted">{{ __('admin_users.empty') }}</p>
                @endforelse
            </div>
            <div class="mt-4">{{ $administrators->links() }}</div>
        </section>

        <section class="rounded-2xl border border-border bg-surface p-5">
            <h2 class="text-sm font-semibold uppercase tracking-wide text-muted">{{ __('admin_users.create') }}</h2>
            <form method="POST" action="{{ route('admin.users.store') }}" class="mt-4 space-y-4">
                @csrf
                <div>
                    <label for="name" class="block text-sm font-medium text-text">{{ __('admin_users.name') }}</label>
                    <input id="name" name="name" value="{{ old('name') }}" required class="mt-1 block w-full rounded-lg border border-border px-3 py-2 text-sm focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary">
                    @error('name')<p class="mt-1 text-xs text-accent">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="phone" class="block text-sm font-medium text-text">{{ __('admin_users.phone') }}</label>
                    <input id="phone" name="phone" value="{{ old('phone') }}" class="mt-1 block w-full rounded-lg border border-border px-3 py-2 text-sm focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary">
                    @error('phone')<p class="mt-1 text-xs text-accent">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="email" class="block text-sm font-medium text-text">{{ __('admin_users.email') }}</label>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" class="mt-1 block w-full rounded-lg border border-border px-3 py-2 text-sm focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary">
                    @error('email')<p class="mt-1 text-xs text-accent">{{ $message }}</p>@enderror
                </div>
                <p class="text-xs text-muted">{{ __('admin_users.identifier_hint') }}</p>
                <div>
                    <label for="password" class="block text-sm font-medium text-text">{{ __('admin_users.password') }}</label>
                    <input id="password" type="password" name="password" required class="mt-1 block w-full rounded-lg border border-border px-3 py-2 text-sm focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary">
                    @error('password')<p class="mt-1 text-xs text-accent">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-text">{{ __('admin_users.password_confirmation') }}</label>
                    <input id="password_confirmation" type="password" name="password_confirmation" required class="mt-1 block w-full rounded-lg border border-border px-3 py-2 text-sm focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary">
                </div>
                <div>
                    <label for="preferred_locale" class="block text-sm font-medium text-text">{{ __('admin_users.language') }}</label>
                    <select id="preferred_locale" name="preferred_locale" class="mt-1 block w-full rounded-lg border border-border px-3 py-2 text-sm focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary">
                        <option value="fr" @selected(old('preferred_locale', 'fr') === 'fr')>Français</option>
                        <option value="ar" @selected(old('preferred_locale') === 'ar')>العربية</option>
                    </select>
                </div>
                <button type="submit" class="inline-flex items-center gap-2 rounded-lg bg-primary px-4 py-2 text-sm font-semibold text-white hover:bg-primary-dark">
                    <i class="bi bi-person-plus"></i>{{ __('admin_users.create_button') }}
                </button>
            </form>
        </section>
    </div>
@endsection