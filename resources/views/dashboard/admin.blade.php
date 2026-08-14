@extends('layouts.app')

@section('title', __('dashboard.admin_title'))

@section('content')
    <div>
        <h1 class="text-xl font-semibold text-text">{{ __('dashboard.admin_title') }}</h1>
        <p class="mt-1 text-sm text-muted">{{ __('dashboard.welcome_back', ['name' => auth()->user()->display_name]) }}</p>
    </div>

    <div class="mt-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <a
            href="{{ route('admin.members.index') }}"
            class="group rounded-2xl border border-border bg-surface p-5 shadow-sm transition hover:-translate-y-0.5 hover:border-primary hover:shadow-md"
        >
            <span class="flex h-11 w-11 items-center justify-center rounded-xl bg-linear-to-br from-primary/20 to-primary/5 text-primary shadow-sm">
                <i class="bi bi-people text-xl"></i>
            </span>
            <p class="mt-4 text-3xl font-bold text-text">{{ $totalMembers }}</p>
            <p class="mt-1 text-sm text-muted">{{ __('dashboard.admin_total_members') }}</p>
        </a>

        <a
            href="{{ route('admin.memberships.index', ['status' => 'pending']) }}"
            class="group rounded-2xl border border-border bg-surface p-5 shadow-sm transition hover:-translate-y-0.5 hover:border-secondary hover:shadow-md"
        >
            <span class="flex h-11 w-11 items-center justify-center rounded-xl bg-linear-to-br from-secondary/25 to-secondary/5 text-secondary shadow-sm">
                <i class="bi bi-hourglass-split text-xl"></i>
            </span>
            <p class="mt-4 text-3xl font-bold text-text">{{ $pendingCount }}</p>
            <p class="mt-1 text-sm text-muted">{{ __('dashboard.admin_pending') }}</p>
        </a>

        <a
            href="{{ route('admin.memberships.index', ['status' => 'approved']) }}"
            class="group rounded-2xl border border-border bg-surface p-5 shadow-sm transition hover:-translate-y-0.5 hover:border-primary hover:shadow-md"
        >
            <span class="flex h-11 w-11 items-center justify-center rounded-xl bg-linear-to-br from-primary/20 to-primary/5 text-primary shadow-sm">
                <i class="bi bi-patch-check text-xl"></i>
            </span>
            <p class="mt-4 text-3xl font-bold text-text">{{ $approvedCount }}</p>
            <p class="mt-1 text-sm text-muted">{{ __('dashboard.admin_approved') }}</p>
        </a>

        <a
            href="{{ route('admin.memberships.index', ['status' => 'rejected']) }}"
            class="group rounded-2xl border border-border bg-surface p-5 shadow-sm transition hover:-translate-y-0.5 hover:border-accent hover:shadow-md"
        >
            <span class="flex h-11 w-11 items-center justify-center rounded-xl bg-linear-to-br from-accent/20 to-accent/5 text-accent shadow-sm">
                <i class="bi bi-x-circle text-xl"></i>
            </span>
            <p class="mt-4 text-3xl font-bold text-text">{{ $rejectedCount }}</p>
            <p class="mt-1 text-sm text-muted">{{ __('dashboard.admin_rejected') }}</p>
        </a>
    </div>

    <div class="mt-8">
        <h2 class="text-sm font-semibold uppercase tracking-wide text-muted">{{ __('dashboard.quick_actions') }}</h2>

        <div class="mt-3 grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
            @can('memberships.approve')
                <a href="{{ route('admin.memberships.index', ['status' => 'pending']) }}" class="flex items-start gap-3 rounded-xl border border-border bg-surface p-4 text-sm font-medium text-text transition hover:border-primary hover:bg-primary-light hover:text-primary">
                    <i class="bi bi-patch-check mt-0.5 shrink-0 text-lg"></i> <span class="leading-snug">{{ __('members.nav_validations') }}</span>
                </a>
            @endcan

            @can('notifications.send')
                <a href="{{ route('admin.notifications.create') }}" class="flex items-start gap-3 rounded-xl border border-border bg-surface p-4 text-sm font-medium text-text transition hover:border-primary hover:bg-primary-light hover:text-primary">
                    <i class="bi bi-send mt-0.5 shrink-0 text-lg"></i> <span class="leading-snug">{{ __('notifications.send_new') }}</span>
                </a>
            @endcan

            @can('members.export')
                <a href="{{ route('admin.exports.index') }}" class="flex items-start gap-3 rounded-xl border border-border bg-surface p-4 text-sm font-medium text-text transition hover:border-primary hover:bg-primary-light hover:text-primary">
                    <i class="bi bi-download mt-0.5 shrink-0 text-lg"></i> <span class="leading-snug">{{ __('members.nav_exports') }}</span>
                </a>
            @endcan

            @can('support_messages.manage')
                <a href="{{ route('admin.support.index') }}" class="flex items-start gap-3 rounded-xl border border-border bg-surface p-4 text-sm font-medium text-text transition hover:border-primary hover:bg-primary-light hover:text-primary">
                    <i class="bi bi-chat-dots mt-0.5 shrink-0 text-lg"></i> <span class="leading-snug">{{ __('members.nav_admin_support') }}</span>
                </a>
            @endcan
        </div>
    </div>

    @can('statistics.view')
    <div class="mt-8 rounded-2xl border border-border bg-surface p-6 shadow-sm">
        <h2 class="flex items-center gap-2 text-sm font-bold uppercase tracking-wide text-text">
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
    @endcan
@endsection
