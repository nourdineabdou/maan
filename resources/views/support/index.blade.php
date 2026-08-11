@extends('layouts.app')

@section('title', __('support.title'))

@section('content')
    <div class="flex flex-wrap items-center justify-between gap-3">
        <div>
            <h1 class="text-xl font-semibold text-text">{{ __('support.title') }}</h1>
            <p class="mt-1 text-sm text-muted">{{ __('support.subtitle') }}</p>
        </div>
        <a href="{{ route('support.create') }}" class="inline-flex items-center gap-2 rounded-lg bg-primary px-4 py-2 text-sm font-semibold text-white hover:bg-primary-dark">
            <i class="bi bi-plus-circle"></i> {{ __('support.new_message') }}
        </a>
    </div>

    @if ($messages->isEmpty())
        <div class="mt-6 rounded-2xl border border-dashed border-border bg-surface p-10 text-center text-sm text-muted">
            {{ __('support.no_messages') }}
        </div>
    @else
        <div class="mt-6 space-y-3">
            @foreach ($messages as $message)
                @php
                    $badgeClass = match ($message->status) {
                        'answered' => 'bg-primary-light text-primary',
                        'closed' => 'bg-border text-muted',
                        default => 'bg-secondary/20 text-secondary',
                    };
                @endphp
                <a href="{{ route('support.show', $message) }}" class="block rounded-2xl border border-border bg-surface p-4 shadow-sm">
                    <div class="flex items-start justify-between gap-3">
                        <p class="font-semibold text-text">{{ $message->subject }}</p>
                        <span class="shrink-0 rounded-full px-2.5 py-1 text-xs font-semibold {{ $badgeClass }}">
                            {{ __('support.status_'.$message->status) }}
                        </span>
                    </div>
                    <p class="mt-1 text-sm text-muted line-clamp-1">{{ $message->body }}</p>
                    <p class="mt-2 text-xs text-muted">{{ $message->created_at->format('d/m/Y H:i') }}</p>
                </a>
            @endforeach
        </div>

        <div class="mt-6">
            {{ $messages->links() }}
        </div>
    @endif
@endsection
