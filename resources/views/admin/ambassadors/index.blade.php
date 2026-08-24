@extends('layouts.app')

@section('title', __('ambassadors.list_title'))

@section('content')
    <h1 class="text-xl font-semibold text-text">{{ __('ambassadors.list_title') }}</h1>

    <form method="GET" class="mt-4 flex flex-wrap items-end gap-3 rounded-2xl border border-border bg-surface p-4">
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

        <button type="submit" class="rounded-lg bg-primary px-4 py-2 text-sm font-semibold text-white hover:bg-primary-dark">
            {{ __('memberships.apply_filters') }}
        </button>
    </form>

    @if ($ambassadorRequests->isEmpty())
        <div class="mt-6 rounded-2xl border border-dashed border-border bg-surface p-10 text-center text-sm text-muted">
            {{ __('ambassadors.empty') }}
        </div>
    @else
        <div class="mt-6 hidden overflow-x-auto rounded-2xl border border-border bg-surface lg:block">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-border text-start text-xs uppercase text-muted">
                        <th class="px-4 py-3 text-start">{{ __('memberships.column_name') }}</th>
                        <th class="px-4 py-3 text-start">{{ __('memberships.column_phone') }}</th>
                        <th class="px-4 py-3 text-start">{{ __('memberships.column_submitted_at') }}</th>
                        <th class="px-4 py-3 text-start">{{ __('memberships.column_status') }}</th>
                        <th class="px-4 py-3 text-start">{{ __('memberships.column_actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($ambassadorRequests as $ambassadorRequest)
                        @php $profile = $ambassadorRequest->user->profile; @endphp
                        <tr class="border-b border-border last:border-0">
                            <td class="px-4 py-3 font-medium text-text">{{ $profile?->full_name ?? $ambassadorRequest->user->name }}</td>
                            <td class="px-4 py-3 text-muted">{{ $ambassadorRequest->user->phone ?? '—' }}</td>
                            <td class="px-4 py-3 text-muted">{{ $ambassadorRequest->submitted_at?->format('d/m/Y') ?? '—' }}</td>
                            <td class="px-4 py-3">
                                @php
                                    $badgeClass = match ($ambassadorRequest->status) {
                                        'approved' => 'bg-primary-light text-primary',
                                        'rejected' => 'bg-accent/10 text-accent',
                                        default => 'bg-secondary/20 text-secondary',
                                    };
                                @endphp
                                <span class="rounded-full px-2.5 py-1 text-xs font-semibold {{ $badgeClass }}">
                                    {{ __('dashboard.status_'.$ambassadorRequest->status) }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <a href="{{ route('admin.ambassadors.show', $ambassadorRequest) }}" class="font-medium text-primary hover:underline">
                                    {{ __('memberships.view_details') }}
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-6 space-y-3 lg:hidden">
            @foreach ($ambassadorRequests as $ambassadorRequest)
                @php $profile = $ambassadorRequest->user->profile; @endphp
                <a href="{{ route('admin.ambassadors.show', $ambassadorRequest) }}" class="block rounded-2xl border border-border bg-surface p-4 shadow-sm">
                    <div class="flex items-center justify-between gap-3">
                        <p class="font-semibold text-text">{{ $profile?->full_name ?? $ambassadorRequest->user->name }}</p>
                        @php
                            $badgeClass = match ($ambassadorRequest->status) {
                                'approved' => 'bg-primary-light text-primary',
                                'rejected' => 'bg-accent/10 text-accent',
                                default => 'bg-secondary/20 text-secondary',
                            };
                        @endphp
                        <span class="rounded-full px-2.5 py-1 text-xs font-semibold {{ $badgeClass }}">
                            {{ __('dashboard.status_'.$ambassadorRequest->status) }}
                        </span>
                    </div>
                    <p class="mt-1 text-xs text-muted">{{ $ambassadorRequest->user->phone }}</p>
                </a>
            @endforeach
        </div>

        <div class="mt-6">
            {{ $ambassadorRequests->links() }}
        </div>
    @endif
@endsection
