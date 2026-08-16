@extends('layouts.app')

@section('title', __('support.admin_title'))

@section('content')
    <h1 class="text-xl font-semibold text-text">{{ __('support.admin_title') }}</h1>

    <form method="GET" class="mt-4 flex flex-wrap items-end gap-3 rounded-2xl border border-border bg-surface p-4">
        <div class="min-w-[180px]">
            <label class="block text-xs font-medium text-muted">{{ __('memberships.filter_status') }}</label>
            <select name="status" class="mt-1 block w-full rounded-lg border border-border px-3 py-2 text-sm focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary">
                <option value="">{{ __('support.filter_all_statuses') }}</option>
                @foreach (['open', 'answered', 'closed'] as $statusOption)
                    <option value="{{ $statusOption }}" @selected($filters['status'] === $statusOption)>{{ __('support.status_'.$statusOption) }}</option>
                @endforeach
            </select>
        </div>

        <button type="submit" class="rounded-lg bg-primary px-4 py-2 text-sm font-semibold text-white hover:bg-primary-dark">
            {{ __('memberships.apply_filters') }}
        </button>
    </form>

    @if ($messages->isEmpty())
        <div class="mt-6 rounded-2xl border border-dashed border-border bg-surface p-10 text-center text-sm text-muted">
            {{ __('support.no_results') }}
        </div>
    @else
        {{-- Table (desktop) --}}
        <div class="mt-6 hidden overflow-x-auto rounded-2xl border border-border bg-surface lg:block">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-border text-start text-xs uppercase text-muted">
                        <th class="px-4 py-3 text-start">{{ __('support.column_subject') }}</th>
                        <th class="px-4 py-3 text-start">{{ __('support.column_member') }}</th>
                        <th class="px-4 py-3 text-start">{{ __('support.column_status') }}</th>
                        <th class="px-4 py-3 text-start">{{ __('support.column_date') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($messages as $message)
                        @php
                            $badgeClass = match ($message->status) {
                                'answered' => 'bg-primary-light text-primary',
                                'closed' => 'bg-border text-muted',
                                default => 'bg-secondary/20 text-secondary',
                            };
                        @endphp
                        <tr class="border-b border-border last:border-0">
                            <td class="px-4 py-3">
                                <a href="{{ route('admin.support.show', $message) }}" class="font-medium text-primary hover:underline">
                                    {{ $message->subject }}
                                </a>
                                @if ($message->relatedLabel())
                                    <p class="text-xs text-muted">{{ __('support.related_to_label') }} {{ $message->relatedLabel() }}</p>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-muted">{{ $message->user->profile?->full_name ?? $message->user->name }}</td>
                            <td class="px-4 py-3">
                                <span class="rounded-full px-2.5 py-1 text-xs font-semibold {{ $badgeClass }}">
                                    {{ __('support.status_'.$message->status) }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-muted">{{ $message->created_at->format('d/m/Y H:i') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Cartes (mobile) --}}
        <div class="mt-6 space-y-3 lg:hidden">
            @foreach ($messages as $message)
                @php
                    $badgeClass = match ($message->status) {
                        'answered' => 'bg-primary-light text-primary',
                        'closed' => 'bg-border text-muted',
                        default => 'bg-secondary/20 text-secondary',
                    };
                @endphp
                <a href="{{ route('admin.support.show', $message) }}" class="block rounded-2xl border border-border bg-surface p-4 shadow-sm">
                    <div class="flex items-start justify-between gap-3">
                        <p class="font-semibold text-text">{{ $message->subject }}</p>
                        <span class="shrink-0 rounded-full px-2.5 py-1 text-xs font-semibold {{ $badgeClass }}">
                            {{ __('support.status_'.$message->status) }}
                        </span>
                    </div>
                    <p class="mt-1 text-xs text-muted">{{ $message->user->profile?->full_name ?? $message->user->name }}</p>
                    @if ($message->relatedLabel())
                        <p class="mt-1 text-xs font-medium text-primary">{{ __('support.related_to_label') }} {{ $message->relatedLabel() }}</p>
                    @endif
                    <p class="mt-1 text-xs text-muted">{{ $message->created_at->format('d/m/Y H:i') }}</p>
                </a>
            @endforeach
        </div>

        <div class="mt-6">
            {{ $messages->links() }}
        </div>
    @endif
@endsection
