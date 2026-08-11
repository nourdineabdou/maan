@extends('layouts.app')

@section('title', __('profile.membership_title'))

@section('content')
    <h1 class="text-xl font-semibold text-text">{{ __('profile.membership_title') }}</h1>

    @php
        $badgeClass = match (true) {
            $membership->isDraft() => 'bg-border text-muted',
            $membership->status === 'approved' => 'bg-primary-light text-primary',
            $membership->status === 'rejected' => 'bg-accent/10 text-accent',
            default => 'bg-secondary/20 text-secondary',
        };
    @endphp

    <div class="mt-6 max-w-2xl space-y-6">
        <div class="rounded-2xl border border-border bg-surface p-6">
            <div class="flex flex-wrap items-center justify-between gap-3">
                <div>
                    <p class="text-sm text-muted">{{ __('dashboard.member_status') }}</p>
                    <span class="mt-1 inline-block rounded-full px-3 py-1.5 text-sm font-semibold {{ $badgeClass }}">
                        {{ $membership->isDraft() ? __('memberships.status_draft') : __('dashboard.status_'.$membership->status) }}
                    </span>
                </div>

                @if ($membership->member_number)
                    <div class="text-end">
                        <p class="text-xs text-muted">{{ __('dashboard.member_registration_number') }}</p>
                        <p class="text-lg font-bold text-primary">{{ $membership->member_number }}</p>
                    </div>
                @endif
            </div>

            @if ($membership->status === 'rejected' && $membership->rejection_reason)
                <div class="mt-4 rounded-lg border border-accent/30 bg-accent/5 px-4 py-3 text-sm text-accent">
                    <strong>{{ __('memberships.rejection_reason') }} :</strong> {{ $membership->rejection_reason }}
                </div>
            @endif

            @if ($membership->isDraft())
                <p class="mt-4 text-sm text-muted">{{ __('profile.draft_notice') }}</p>
            @elseif ($membership->status === 'pending')
                <p class="mt-4 text-sm text-muted">{{ __('profile.submitted_notice') }}</p>
            @endif

            @if ($membership->canBeSubmitted())
                @if ($missingFields !== [])
                    <div class="mt-4 rounded-lg border border-secondary/40 bg-secondary/10 px-4 py-3 text-sm text-secondary">
                        <p class="font-semibold">{{ __('profile.missing_fields_title') }}</p>
                        <ul class="mt-1 list-inside list-disc">
                            @foreach ($missingFields as $field)
                                <li>{{ $field }}</li>
                            @endforeach
                        </ul>
                        <a href="{{ route('profile.personal.edit') }}" class="mt-2 inline-block font-medium underline">{{ __('profile.edit') }}</a>
                    </div>
                @else
                    <form method="POST" action="{{ route('profile.membership.submit') }}" class="mt-4">
                        @csrf
                        <label class="flex items-start gap-2 text-sm text-muted">
                            <input type="checkbox" required class="mt-0.5 rounded border-border text-primary focus:ring-primary">
                            {{ __('profile.certify_accurate') }}
                        </label>
                        <button type="submit" class="mt-3 inline-flex items-center gap-2 rounded-lg bg-primary px-4 py-2 text-sm font-semibold text-white hover:bg-primary-dark">
                            <i class="bi bi-send-check"></i>
                            {{ $membership->isRejected() ? __('profile.resubmit_button') : __('profile.submit_button') }}
                        </button>
                    </form>
                @endif
            @endif

            @if ($membership->isApproved())
                <a href="{{ route('card.show') }}" class="mt-4 inline-flex items-center gap-2 rounded-lg border border-primary px-4 py-2 text-sm font-semibold text-primary hover:bg-primary-light">
                    <i class="bi bi-postcard"></i> {{ __('dashboard.member_view_card') }}
                </a>
            @endif
        </div>

        <div class="rounded-2xl border border-border bg-surface p-6">
            <h2 class="text-sm font-semibold uppercase tracking-wide text-muted">{{ __('memberships.section_history') }}</h2>

            @if ($membership->statusHistories->isEmpty())
                <p class="mt-3 text-sm text-muted">{{ __('memberships.no_history') }}</p>
            @else
                <ul class="mt-3 space-y-3 text-sm">
                    @foreach ($membership->statusHistories->sortByDesc('created_at') as $history)
                        <li class="border-s-2 border-primary ps-3">
                            <p class="font-medium text-text">{{ __('dashboard.status_'.$history->new_status) }}</p>
                            <p class="text-xs text-muted">{{ $history->created_at->format('d/m/Y H:i') }}</p>
                            @if ($history->comment)
                                <p class="mt-1 text-xs text-text">{{ $history->comment }}</p>
                            @endif
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>
    </div>
@endsection
