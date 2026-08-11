@extends('layouts.app')

@section('title', __('dashboard.member_title'))

@section('content')
    <h1 class="text-xl font-semibold text-text">
        {{ __('dashboard.welcome_back', ['name' => auth()->user()->display_name]) }}
    </h1>

    @php
        $badgeClass = match (true) {
            ! $membership || $membership->isDraft() => 'bg-border text-muted',
            $membership->status === 'approved' => 'bg-primary-light text-primary',
            $membership->status === 'rejected' => 'bg-accent/10 text-accent',
            default => 'bg-secondary/20 text-secondary',
        };
    @endphp

    <div class="mt-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
        <div class="rounded-2xl border border-border bg-surface p-5 shadow-sm transition hover:shadow-md">
            <span class="flex h-11 w-11 items-center justify-center rounded-xl bg-linear-to-br from-primary/20 to-primary/5 text-primary shadow-sm">
                <i class="bi bi-card-checklist text-xl"></i>
            </span>

            <p class="mt-4 text-sm text-muted">{{ __('dashboard.member_status') }}</p>

            @if ($membership && ! $membership->isDraft())
                <span class="mt-1 inline-block rounded-full px-2.5 py-1 text-sm font-semibold {{ $badgeClass }}">
                    {{ __('dashboard.status_'.$membership->status) }}
                </span>
                @if ($membership->member_number)
                    <p class="mt-2 text-sm text-muted">
                        {{ __('dashboard.member_registration_number') }} : <span class="font-medium text-text">{{ $membership->member_number }}</span>
                    </p>
                @endif
                @if ($membership->status === 'rejected')
                    <a href="{{ route('profile.membership') }}" class="mt-3 inline-flex items-center gap-2 rounded-lg bg-primary px-4 py-2 text-sm font-semibold text-white hover:bg-primary-dark">
                        <i class="bi bi-arrow-right-circle"></i>
                        {{ __('dashboard.member_continue_registration') }}
                    </a>
                @endif
            @else
                <p class="mt-1 text-sm text-muted">{{ __('dashboard.member_no_membership') }}</p>
                <a href="{{ route('profile.membership') }}" class="mt-3 inline-flex items-center gap-2 rounded-lg bg-primary px-4 py-2 text-sm font-semibold text-white hover:bg-primary-dark">
                    <i class="bi bi-arrow-right-circle"></i>
                    {{ __('dashboard.member_continue_registration') }}
                </a>
            @endif
        </div>

        @if ($membership?->isApproved())
            <div class="rounded-2xl border border-border bg-surface p-5 shadow-sm transition hover:shadow-md">
                <span class="flex h-11 w-11 items-center justify-center rounded-xl bg-linear-to-br from-secondary/25 to-secondary/5 text-secondary shadow-sm">
                    <i class="bi bi-postcard text-xl"></i>
                </span>
                <p class="mt-4 text-sm text-muted">{{ __('members.nav_my_card') }}</p>
                <a href="{{ route('card.show') }}" class="mt-3 inline-flex items-center gap-2 rounded-lg border border-primary px-4 py-2 text-sm font-semibold text-primary hover:bg-primary-light">
                    <i class="bi bi-postcard"></i>
                    {{ __('dashboard.member_view_card') }}
                </a>
            </div>
        @endif
    </div>
@endsection
