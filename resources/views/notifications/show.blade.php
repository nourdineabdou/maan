@extends('layouts.app')

@section('title', $recipient->notification->getTranslation('title'))

@section('content')
    <a href="{{ route('notifications.index') }}" class="text-sm text-muted hover:text-primary">
        <i class="bi bi-arrow-left"></i> {{ __('notifications.back_to_inbox') }}
    </a>

    <div class="mt-4 max-w-2xl rounded-2xl border border-border bg-surface p-6">
        <h1 class="text-xl font-semibold text-text">{{ $recipient->notification->getTranslation('title') }}</h1>
        <p class="mt-1 text-xs text-muted">{{ $recipient->created_at->format('d/m/Y H:i') }}</p>
        <p class="mt-4 whitespace-pre-line text-sm text-text">{{ $recipient->notification->getTranslation('message') }}</p>

        @if ($recipient->notification->action_url)
            <a href="{{ $recipient->notification->action_url }}" class="mt-4 inline-flex items-center gap-2 rounded-lg bg-primary px-4 py-2 text-sm font-semibold text-white hover:bg-primary-dark">
                {{ __('messages.view') }}
            </a>
        @endif
    </div>
@endsection
