@extends('layouts.app')

@section('title', __('notifications.inbox_title'))

@section('content')
    <div class="flex flex-wrap items-center justify-between gap-3">
        <h1 class="text-xl font-semibold text-text">{{ __('notifications.inbox_title') }}</h1>

        @if ($recipients->contains(fn ($r) => ! $r->isRead()))
            <form method="POST" action="{{ route('notifications.mark-all-read') }}">
                @csrf
                <button type="submit" class="text-sm font-medium text-primary hover:underline">
                    {{ __('notifications.mark_all_read') }}
                </button>
            </form>
        @endif
    </div>

    @if ($recipients->isEmpty())
        <div class="mt-6 rounded-2xl border border-dashed border-border bg-surface p-10 text-center text-sm text-muted">
            {{ __('notifications.no_notifications') }}
        </div>
    @else
        <div class="mt-6 space-y-3">
            @foreach ($recipients as $recipient)
                <a
                    href="{{ route('notifications.show', $recipient) }}"
                    class="block rounded-2xl border border-border bg-surface p-4 shadow-sm {{ ! $recipient->isRead() ? 'border-primary' : '' }}"
                >
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <p class="font-semibold text-text">{{ $recipient->notification->getTranslation('title') }}</p>
                            <p class="mt-1 text-sm text-muted line-clamp-2">{{ $recipient->notification->getTranslation('message') }}</p>
                        </div>
                        @if (! $recipient->isRead())
                            <span class="shrink-0 rounded-full bg-accent px-2 py-0.5 text-[10px] font-semibold text-white">
                                {{ __('notifications.unread') }}
                            </span>
                        @endif
                    </div>
                    <p class="mt-2 text-xs text-muted">{{ $recipient->created_at->format('d/m/Y H:i') }}</p>
                </a>
            @endforeach
        </div>

        <div class="mt-6">
            {{ $recipients->links() }}
        </div>
    @endif
@endsection
