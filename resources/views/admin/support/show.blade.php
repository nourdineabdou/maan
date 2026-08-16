@extends('layouts.app')

@section('title', $message->subject)

@section('content')
    @php
        $badgeClass = match ($message->status) {
            'answered' => 'bg-primary-light text-primary',
            'closed' => 'bg-border text-muted',
            default => 'bg-secondary/20 text-secondary',
        };
    @endphp

    <a href="{{ route('admin.support.index') }}" class="text-sm text-muted hover:text-primary">
        <i class="bi bi-arrow-left"></i> {{ __('support.admin_title') }}
    </a>

    <div class="mt-1 flex flex-wrap items-center justify-between gap-3">
        <div>
            <h1 class="text-xl font-semibold text-text">{{ $message->subject }}</h1>
            <p class="mt-1 text-sm text-muted">{{ $message->user->profile?->full_name ?? $message->user->name }} · {{ $message->user->phone }}</p>
        </div>

        <div class="flex items-center gap-3">
            <span class="rounded-full px-3 py-1.5 text-sm font-semibold {{ $badgeClass }}">
                {{ __('support.status_'.$message->status) }}
            </span>

            @if ($message->status !== 'closed')
                <form
                    method="POST" action="{{ route('admin.support.close', $message) }}"
                    data-confirm="{{ __('support.close_confirm') }}"
                    data-confirm-button="{{ __('support.close_thread') }}"
                    data-cancel-button="{{ __('messages.cancel') }}"
                >
                    @csrf
                    <button type="submit" class="rounded-lg border border-border px-3 py-1.5 text-sm font-medium text-muted hover:bg-background">
                        {{ __('support.close_thread') }}
                    </button>
                </form>
            @endif
        </div>
    </div>

    @if ($message->relatedLabel())
        <p class="mt-1 text-xs font-medium text-primary">{{ __('support.related_to_label') }} {{ $message->relatedLabel() }}</p>
    @endif

    <div class="mt-6 max-w-2xl space-y-4">
        <div class="rounded-2xl border p-4 {{ $message->wasStartedByAdmin() ? 'border-primary bg-primary-light' : 'border-border bg-surface' }}">
            <div class="flex items-center justify-between">
                <p class="text-sm font-semibold {{ $message->wasStartedByAdmin() ? 'text-primary' : 'text-text' }}">
                    {{ $message->wasStartedByAdmin() ? __('support.from_admin') : __('support.from_member') }}
                </p>
                <p class="text-xs text-muted">{{ $message->created_at->format('d/m/Y H:i') }}</p>
            </div>
            <p class="mt-2 whitespace-pre-line text-sm text-text">{{ $message->body }}</p>
        </div>

        @forelse ($message->replies as $reply)
            @php $isAdmin = $reply->author->hasRole('administrateur'); @endphp
            <div class="rounded-2xl border p-4 {{ $isAdmin ? 'border-primary bg-primary-light' : 'border-border bg-surface' }}">
                <div class="flex items-center justify-between">
                    <p class="text-sm font-semibold {{ $isAdmin ? 'text-primary' : 'text-text' }}">
                        {{ $isAdmin ? __('support.from_admin') : __('support.from_member') }}
                    </p>
                    <p class="text-xs text-muted">{{ $reply->created_at->format('d/m/Y H:i') }}</p>
                </div>
                <p class="mt-2 whitespace-pre-line text-sm text-text">{{ $reply->body }}</p>
            </div>
        @empty
            <p class="text-sm text-muted">{{ __('support.no_replies') }}</p>
        @endforelse

        @if ($message->status !== 'closed')
            <form method="POST" action="{{ route('admin.support.reply', $message) }}" class="rounded-2xl border border-border bg-surface p-4">
                @csrf
                <textarea name="body" rows="3" required placeholder="{{ __('support.reply_placeholder') }}"
                    class="block w-full rounded-lg border border-border px-3 py-2 text-sm focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary"></textarea>
                <button type="submit" class="mt-3 inline-flex items-center gap-2 rounded-lg bg-primary px-4 py-2 text-sm font-semibold text-white hover:bg-primary-dark">
                    <i class="bi bi-send"></i> {{ __('support.reply') }}
                </button>
            </form>
        @endif
    </div>
@endsection
