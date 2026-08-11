@extends('layouts.app')

@section('title', __('members.nav_members'))

@section('content')
    <h1 class="text-xl font-semibold text-text">{{ __('members.nav_members') }}</h1>

    <form method="GET" class="mt-4 flex flex-wrap items-end gap-3 rounded-2xl border border-border bg-surface p-4">
        <div class="flex-1 min-w-[200px]">
            <label class="block text-xs font-medium text-muted">{{ __('messages.search') }}</label>
            <input
                type="text" name="q" value="{{ $filters['q'] }}"
                placeholder="{{ __('memberships.search_placeholder') }}"
                class="mt-1 block w-full rounded-lg border border-border px-3 py-2 text-sm focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary"
            >
        </div>

        <div class="min-w-[180px]">
            <label class="block text-xs font-medium text-muted">{{ __('memberships.filter_region') }}</label>
            <select name="region" class="mt-1 block w-full rounded-lg border border-border px-3 py-2 text-sm focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary">
                <option value="">{{ __('memberships.filter_all_regions') }}</option>
                @foreach ($regions as $region)
                    <option value="{{ $region->id }}" @selected($filters['region'] == $region->id)>
                        {{ $region->getTranslation('name') }}
                    </option>
                @endforeach
            </select>
        </div>

        <button type="submit" class="rounded-lg bg-primary px-4 py-2 text-sm font-semibold text-white hover:bg-primary-dark">
            {{ __('memberships.apply_filters') }}
        </button>
    </form>

    @if ($members->isEmpty())
        <div class="mt-6 rounded-2xl border border-dashed border-border bg-surface p-10 text-center text-sm text-muted">
            {{ __('memberships.no_results') }}
        </div>
    @else
        <div class="mt-6 hidden overflow-x-auto rounded-2xl border border-border bg-surface lg:block">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-border text-start text-xs uppercase text-muted">
                        <th class="px-4 py-3 text-start">{{ __('memberships.column_photo') }}</th>
                        <th class="px-4 py-3 text-start">{{ __('memberships.column_name') }}</th>
                        <th class="px-4 py-3 text-start">{{ __('memberships.column_phone') }}</th>
                        <th class="px-4 py-3 text-start">{{ __('auth.email_optional') }}</th>
                        <th class="px-4 py-3 text-start">{{ __('memberships.column_region') }}</th>
                        <th class="px-4 py-3 text-start">{{ __('dashboard.member_registration_number') }}</th>
                        <th class="px-4 py-3 text-start">{{ __('memberships.column_status') }}</th>
                        <th class="px-4 py-3 text-start">{{ __('memberships.column_actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($members as $member)
                        @php
                            $profile = $member->profile;
                            $membership = $member->latestMembership;
                            $isDraft = $membership && $membership->isDraft();
                            $badgeClass = match (true) {
                                $isDraft => 'bg-border text-muted',
                                $membership?->status === 'approved' => 'bg-primary-light text-primary',
                                $membership?->status === 'rejected' => 'bg-accent/10 text-accent',
                                $membership?->status === 'pending' => 'bg-secondary/20 text-secondary',
                                default => 'bg-border text-muted',
                            };
                        @endphp
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
                            <td class="px-4 py-3 font-medium text-text">{{ $profile?->full_name ?? $member->name }}</td>
                            <td class="px-4 py-3 text-muted">{{ $member->phone ?? '—' }}</td>
                            <td class="px-4 py-3 text-muted">{{ $member->email ?? '—' }}</td>
                            <td class="px-4 py-3 text-muted">{{ $profile?->region?->getTranslation('name') ?? '—' }}</td>
                            <td class="px-4 py-3 text-muted">{{ $membership?->member_number ?? '—' }}</td>
                            <td class="px-4 py-3">
                                <span class="rounded-full px-2.5 py-1 text-xs font-semibold {{ $badgeClass }}">
                                    {{ $isDraft ? __('memberships.status_draft') : ($membership ? __('dashboard.status_'.$membership->status) : __('memberships.not_provided')) }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                @if ($membership)
                                    <a href="{{ route('admin.memberships.show', $membership) }}" class="font-medium text-primary hover:underline">
                                        {{ __('memberships.view_details') }}
                                    </a>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-6 space-y-3 lg:hidden">
            @foreach ($members as $member)
                @php $profile = $member->profile; $membership = $member->latestMembership; @endphp
                <div class="rounded-2xl border border-border bg-surface p-4 shadow-sm">
                    <div class="flex items-center gap-3">
                        <div class="flex h-10 w-10 shrink-0 items-center justify-center overflow-hidden rounded-full border border-border bg-background">
                            @if ($profile?->photo_url)
                                <a href="{{ $profile->photo_url }}" target="_blank">
                                    <img src="{{ $profile->photo_url }}" alt="" class="h-full w-full object-cover">
                                </a>
                            @else
                                <i class="bi bi-person-fill text-muted"></i>
                            @endif
                        </div>
                        <div>
                            <p class="font-semibold text-text">{{ $profile?->full_name ?? $member->name }}</p>
                            <p class="text-xs text-muted">{{ $member->phone }} · {{ $profile?->region?->getTranslation('name') ?? '—' }}</p>
                        </div>
                    </div>
                    @if ($membership)
                        <a href="{{ route('admin.memberships.show', $membership) }}" class="mt-2 inline-block text-sm font-medium text-primary hover:underline">
                            {{ __('memberships.view_details') }}
                        </a>
                    @endif
                </div>
            @endforeach
        </div>

        <div class="mt-6">
            {{ $members->links() }}
        </div>
    @endif
@endsection
