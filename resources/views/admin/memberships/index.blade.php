@extends('layouts.app')

@section('title', __('memberships.list_title'))

@section('content')
    <div class="flex flex-wrap items-center justify-between gap-3">
        <h1 class="text-xl font-semibold text-text">
            {{ $filters['status'] === 'pending' ? __('memberships.pending_title') : __('memberships.list_title') }}
        </h1>
    </div>

    <form method="GET" class="mt-4 flex flex-wrap items-end gap-3 rounded-2xl border border-border bg-surface p-4">
        <div class="flex-1 min-w-[200px]">
            <label class="block text-xs font-medium text-muted">{{ __('messages.search') }}</label>
            <input
                type="text" name="q" value="{{ $filters['q'] ?? '' }}"
                placeholder="{{ __('memberships.search_placeholder') }}"
                class="mt-1 block w-full rounded-lg border border-border px-3 py-2 text-sm focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary"
            >
        </div>

        <div class="min-w-[160px]">
            <label class="block text-xs font-medium text-muted">{{ __('memberships.filter_status') }}</label>
            <select name="status" class="mt-1 block w-full rounded-lg border border-border px-3 py-2 text-sm focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary">
                <option value="">{{ __('memberships.filter_all_statuses') }}</option>
                @foreach (['pending', 'approved', 'rejected'] as $statusOption)
                    <option value="{{ $statusOption }}" @selected(($filters['status'] ?? '') === $statusOption)>
                        {{ __('dashboard.status_'.$statusOption) }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="min-w-[180px]">
            <label class="block text-xs font-medium text-muted">{{ __('memberships.filter_region') }}</label>
            <select name="region" class="mt-1 block w-full rounded-lg border border-border px-3 py-2 text-sm focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary">
                <option value="">{{ __('memberships.filter_all_regions') }}</option>
                @foreach ($regions as $region)
                    <option value="{{ $region->id }}" @selected(($filters['region'] ?? '') == $region->id)>
                        {{ $region->getTranslation('name') }}
                    </option>
                @endforeach
            </select>
        </div>

        <button type="submit" class="rounded-lg bg-primary px-4 py-2 text-sm font-semibold text-white hover:bg-primary-dark">
            {{ __('memberships.apply_filters') }}
        </button>
    </form>

    @if ($memberships->isEmpty())
        <div class="mt-6 rounded-2xl border border-dashed border-border bg-surface p-10 text-center text-sm text-muted">
            {{ __('memberships.no_results') }}
        </div>
    @else
        {{-- Table (desktop) --}}
        <div class="mt-6 hidden overflow-x-auto rounded-2xl border border-border bg-surface lg:block">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-border text-start text-xs uppercase text-muted">
                        <th class="px-4 py-3 text-start">{{ __('memberships.column_photo') }}</th>
                        <th class="px-4 py-3 text-start">{{ __('memberships.column_registration_number') }}</th>
                        <th class="px-4 py-3 text-start">{{ __('memberships.column_name') }}</th>
                        <th class="px-4 py-3 text-start">{{ __('memberships.column_phone') }}</th>
                        <th class="px-4 py-3 text-start">{{ __('memberships.column_region') }}</th>
                        <th class="px-4 py-3 text-start">{{ __('memberships.column_submitted_at') }}</th>
                        <th class="px-4 py-3 text-start">{{ __('memberships.column_status') }}</th>
                        <th class="px-4 py-3 text-start">{{ __('memberships.column_actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($memberships as $membership)
                        @php $profile = $membership->user->profile; @endphp
                        <tr class="border-b border-border last:border-0">
                            <td class="px-4 py-3">
                                <div class="flex h-9 w-9 items-center justify-center overflow-hidden rounded-full border border-border bg-background">
                                    @if ($profile?->photo_url)
                                        <a href="{{ $profile->photo_url }}" target="_blank">
                                            <img src="{{ $profile->photo_url }}" alt="" class="h-full w-full object-cover">
                                        </a>
                                    @else
                                        <i class="bi bi-person-fill text-muted"></i>
                                    @endif
                                </div>
                            </td>
                            <td class="px-4 py-3 font-medium text-text">{{ $membership->registration_number }}</td>
                            <td class="px-4 py-3 text-text">{{ $profile?->full_name ?? $membership->user->name }}</td>
                            <td class="px-4 py-3 text-muted">{{ $membership->user->phone ?? '—' }}</td>
                            <td class="px-4 py-3 text-muted">{{ $profile?->region?->getTranslation('name') ?? '—' }}</td>
                            <td class="px-4 py-3 text-muted">{{ $membership->created_at->format('d/m/Y') }}</td>
                            <td class="px-4 py-3">
                                @php
                                    $badgeClass = match ($membership->status) {
                                        'approved' => 'bg-primary-light text-primary',
                                        'rejected' => 'bg-accent/10 text-accent',
                                        default => 'bg-secondary/20 text-secondary',
                                    };
                                @endphp
                                <span class="rounded-full px-2.5 py-1 text-xs font-semibold {{ $badgeClass }}">
                                    {{ __('dashboard.status_'.$membership->status) }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <a href="{{ route('admin.memberships.show', $membership) }}" class="font-medium text-primary hover:underline">
                                    {{ __('memberships.view_details') }}
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Cartes (mobile) --}}
        <div class="mt-6 space-y-3 lg:hidden">
            @foreach ($memberships as $membership)
                @php $profile = $membership->user->profile; @endphp
                <a href="{{ route('admin.memberships.show', $membership) }}" class="block rounded-2xl border border-border bg-surface p-4 shadow-sm">
                    <div class="flex items-center justify-between gap-3">
                        <div class="flex items-center gap-3">
                            <div class="flex h-10 w-10 shrink-0 items-center justify-center overflow-hidden rounded-full border border-border bg-background">
                                @if ($profile?->photo_url)
                                    <img src="{{ $profile->photo_url }}" alt="" class="h-full w-full object-cover">
                                @else
                                    <i class="bi bi-person-fill text-muted"></i>
                                @endif
                            </div>
                            <p class="font-semibold text-text">{{ $profile?->full_name ?? $membership->user->name }}</p>
                        </div>
                        @php
                            $badgeClass = match ($membership->status) {
                                'approved' => 'bg-primary-light text-primary',
                                'rejected' => 'bg-accent/10 text-accent',
                                default => 'bg-secondary/20 text-secondary',
                            };
                        @endphp
                        <span class="rounded-full px-2.5 py-1 text-xs font-semibold {{ $badgeClass }}">
                            {{ __('dashboard.status_'.$membership->status) }}
                        </span>
                    </div>
                    <p class="mt-1 text-xs text-muted">{{ $membership->registration_number }} · {{ $membership->user->phone }}</p>
                    <p class="mt-1 text-xs text-muted">{{ $profile?->region?->getTranslation('name') ?? '—' }}</p>
                </a>
            @endforeach
        </div>

        <div class="mt-6">
            {{ $memberships->links() }}
        </div>
    @endif
@endsection
