@extends('layouts.app')

@section('title', __('ambassadors.title'))

@section('content')
    <div>
        <h1 class="text-xl font-semibold text-text">{{ __('ambassadors.title') }}</h1>
        <p class="mt-1 text-sm text-muted">{{ __('ambassadors.subtitle') }}</p>
    </div>

    @if (session('status'))
        <div class="mt-4 rounded-lg border border-primary/30 bg-primary-light px-4 py-3 text-sm text-primary">
            {{ session('status') }}
        </div>
    @endif

    @php
        $badgeClass = match (true) {
            ! $ambassadorRequest => 'bg-border text-muted',
            $ambassadorRequest->isApproved() => 'bg-primary-light text-primary',
            $ambassadorRequest->isRejected() => 'bg-accent/10 text-accent',
            default => 'bg-secondary/20 text-secondary',
        };
    @endphp

    <div class="mt-6 max-w-2xl space-y-6">
        <div class="rounded-2xl border border-border bg-surface p-6">
            <span class="inline-block rounded-full px-3 py-1.5 text-sm font-semibold {{ $badgeClass }}">
                @if (! $ambassadorRequest)
                    {{ __('ambassadors.status_none') }}
                @else
                    {{ __('dashboard.status_'.$ambassadorRequest->status) }}
                @endif
            </span>

            @if ($ambassadorRequest?->isRejected() && $ambassadorRequest->rejection_reason)
                <div class="mt-4 rounded-lg border border-accent/30 bg-accent/5 px-4 py-3 text-sm text-accent">
                    <strong>{{ __('ambassadors.rejection_reason') }} :</strong> {{ $ambassadorRequest->rejection_reason }}
                </div>
            @endif

            @if (! $isEligible)
                <p class="mt-4 text-sm text-muted">{{ __('ambassadors.not_eligible') }}</p>
            @elseif (! $ambassadorRequest || $ambassadorRequest->canBeSubmitted())
                <form method="POST" action="{{ route('profile.ambassador.request') }}" class="mt-4">
                    @csrf
                    <button type="submit" class="inline-flex items-center gap-2 rounded-lg bg-primary px-4 py-2 text-sm font-semibold text-white hover:bg-primary-dark">
                        <i class="bi bi-award"></i>
                        {{ $ambassadorRequest?->isRejected() ? __('ambassadors.resubmit_button') : __('ambassadors.request_button') }}
                    </button>
                </form>
            @endif
        </div>
    </div>
@endsection
