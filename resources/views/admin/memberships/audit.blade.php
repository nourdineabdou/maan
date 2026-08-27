@extends('layouts.app')

@section('title', __('memberships.audit_title'))

@section('content')
    <div class="flex flex-wrap items-center justify-between gap-3">
        <div>
            <h1 class="text-xl font-semibold text-text">{{ __('memberships.audit_title') }}</h1>
            <p class="mt-1 text-sm text-muted">{{ __('memberships.audit_subtitle') }}</p>
        </div>
    </div>

    <form method="GET" class="mt-4 flex flex-wrap items-end gap-3 rounded-2xl border border-border bg-surface p-4">
        <div class="min-w-[160px]">
            <label class="block text-xs font-medium text-muted">{{ __('memberships.filter_status') }}</label>
            <select name="status" class="mt-1 block w-full rounded-lg border border-border px-3 py-2 text-sm focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary">
                <option value="">{{ __('memberships.filter_all_statuses') }}</option>
                @foreach (['approved', 'rejected'] as $statusOption)
                    <option value="{{ $statusOption }}" @selected(($filters['status'] ?? '') === $statusOption)>
                        {{ __('dashboard.status_'.$statusOption) }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="min-w-[200px]">
            <label class="block text-xs font-medium text-muted">{{ __('memberships.filter_reviewer') }}</label>
            <select name="reviewer" class="mt-1 block w-full rounded-lg border border-border px-3 py-2 text-sm focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary">
                <option value="">{{ __('memberships.filter_all_reviewers') }}</option>
                @foreach ($reviewers as $reviewer)
                    <option value="{{ $reviewer->id }}" @selected(($filters['reviewer'] ?? '') == $reviewer->id)>
                        {{ $reviewer->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <button type="submit" class="rounded-lg bg-primary px-4 py-2 text-sm font-semibold text-white hover:bg-primary-dark">
            {{ __('memberships.apply_filters') }}
        </button>
    </form>

    @if ($histories->isEmpty())
        <div class="mt-6 rounded-2xl border border-dashed border-border bg-surface p-10 text-center text-sm text-muted">
            {{ __('memberships.no_audit_results') }}
        </div>
    @else
        {{-- Table (desktop) --}}
        <div class="mt-6 hidden overflow-x-auto rounded-2xl border border-border bg-surface lg:block">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-border text-start text-xs uppercase text-muted">
                        <th class="px-4 py-3 text-start">{{ __('memberships.column_date') }}</th>
                        <th class="px-4 py-3 text-start">{{ __('memberships.column_member') }}</th>
                        <th class="px-4 py-3 text-start">{{ __('memberships.column_action') }}</th>
                        <th class="px-4 py-3 text-start">{{ __('memberships.column_reviewed_by') }}</th>
                        <th class="px-4 py-3 text-start">{{ __('memberships.column_reason') }}</th>
                        <th class="px-4 py-3 text-start">{{ __('memberships.column_actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($histories as $history)
                        @php
                            $membership = $history->membership;
                            $profile = $membership?->user?->profile;
                            $badgeClass = $history->new_status === 'approved' ? 'bg-primary-light text-primary' : 'bg-accent/10 text-accent';
                        @endphp
                        <tr class="border-b border-border last:border-0">
                            <td class="px-4 py-3 text-muted">{{ $history->created_at->format('d/m/Y H:i') }}</td>
                            <td class="px-4 py-3 text-text">{{ $profile?->full_name ?? $membership?->user?->name ?? '—' }}</td>
                            <td class="px-4 py-3">
                                <span class="rounded-full px-2.5 py-1 text-xs font-semibold {{ $badgeClass }}">
                                    {{ __('memberships.action_'.$history->new_status) }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-muted">{{ $history->changedBy?->name ?? '—' }}</td>
                            <td class="px-4 py-3 text-muted">{{ $history->comment ?? '—' }}</td>
                            <td class="px-4 py-3">
                                @if ($membership)
                                    <a href="{{ route('admin.memberships.show', $membership) }}" class="font-medium text-primary hover:underline">
                                        {{ __('memberships.view_details') }}
                                    </a>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Cartes (mobile) --}}
        <div class="mt-6 space-y-3 lg:hidden">
            @foreach ($histories as $history)
                @php
                    $membership = $history->membership;
                    $profile = $membership?->user?->profile;
                    $badgeClass = $history->new_status === 'approved' ? 'bg-primary-light text-primary' : 'bg-accent/10 text-accent';
                @endphp
                <div class="block rounded-2xl border border-border bg-surface p-4 shadow-sm">
                    <div class="flex items-center justify-between gap-3">
                        <p class="font-semibold text-text">{{ $profile?->full_name ?? $membership?->user?->name ?? '—' }}</p>
                        <span class="rounded-full px-2.5 py-1 text-xs font-semibold {{ $badgeClass }}">
                            {{ __('memberships.action_'.$history->new_status) }}
                        </span>
                    </div>
                    <p class="mt-1 text-xs text-muted">{{ $history->created_at->format('d/m/Y H:i') }}</p>
                    <p class="mt-1 text-xs text-muted">{{ __('memberships.reviewed_by') }} : {{ $history->changedBy?->name ?? '—' }}</p>
                    @if ($history->comment)
                        <p class="mt-1 text-xs text-muted">{{ __('memberships.column_reason') }} : {{ $history->comment }}</p>
                    @endif
                    @if ($membership)
                        <a href="{{ route('admin.memberships.show', $membership) }}" class="mt-2 inline-block text-xs font-medium text-primary hover:underline">
                            {{ __('memberships.view_details') }}
                        </a>
                    @endif
                </div>
            @endforeach
        </div>

        <div class="mt-6">
            {{ $histories->links() }}
        </div>
    @endif
@endsection
