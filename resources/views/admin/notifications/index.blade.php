@extends('layouts.app')

@section('title', __('notifications.title'))

@section('content')
    <div class="flex flex-wrap items-center justify-between gap-3">
        <h1 class="text-xl font-semibold text-text">{{ __('notifications.title') }}</h1>
        <a href="{{ route('admin.notifications.create') }}" class="inline-flex items-center gap-2 rounded-lg bg-primary px-4 py-2 text-sm font-semibold text-white hover:bg-primary-dark">
            <i class="bi bi-send"></i> {{ __('notifications.send_new') }}
        </a>
    </div>

    @if ($notifications->isEmpty())
        <div class="mt-6 rounded-2xl border border-dashed border-border bg-surface p-10 text-center text-sm text-muted">
            {{ __('notifications.no_results') }}
        </div>
    @else
        {{-- Table (desktop) --}}
        <div class="mt-6 hidden overflow-x-auto rounded-2xl border border-border bg-surface lg:block">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-border text-start text-xs uppercase text-muted">
                        <th class="px-4 py-3 text-start">{{ __('notifications.column_title') }}</th>
                        <th class="px-4 py-3 text-start">{{ __('notifications.column_target') }}</th>
                        <th class="px-4 py-3 text-start">{{ __('notifications.column_recipients') }}</th>
                        <th class="px-4 py-3 text-start">{{ __('notifications.column_sent_by') }}</th>
                        <th class="px-4 py-3 text-start">{{ __('notifications.column_sent_at') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($notifications as $campaign)
                        <tr class="border-b border-border last:border-0">
                            <td class="px-4 py-3 font-medium text-text">{{ $campaign->getTranslation('title') }}</td>
                            <td class="px-4 py-3 text-muted">{{ __('notifications.target_'.$campaign->target_type) }}</td>
                            <td class="px-4 py-3 text-muted">{{ $campaign->notifications->first()?->recipients->count() ?? 0 }}</td>
                            <td class="px-4 py-3 text-muted">{{ $campaign->creator?->name }}</td>
                            <td class="px-4 py-3 text-muted">{{ $campaign->sent_at?->format('d/m/Y H:i') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Cartes (mobile) --}}
        <div class="mt-6 space-y-3 lg:hidden">
            @foreach ($notifications as $campaign)
                <div class="rounded-2xl border border-border bg-surface p-4 shadow-sm">
                    <p class="font-semibold text-text">{{ $campaign->getTranslation('title') }}</p>
                    <p class="mt-1 text-xs text-muted">{{ __('notifications.target_'.$campaign->target_type) }} · {{ $campaign->notifications->first()?->recipients->count() ?? 0 }} {{ __('notifications.column_recipients') }}</p>
                    <p class="mt-1 text-xs text-muted">{{ $campaign->creator?->name }} · {{ $campaign->sent_at?->format('d/m/Y H:i') }}</p>
                </div>
            @endforeach
        </div>

        <div class="mt-6">
            {{ $notifications->links() }}
        </div>
    @endif
@endsection
