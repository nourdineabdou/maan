@extends('layouts.app')

@section('title', __('profile.need_edit_title'))

@section('content')
    <a href="{{ route('profile.overview') }}" class="text-sm text-muted hover:text-primary">
        <i class="bi bi-arrow-left"></i> {{ __('profile.overview_title') }}
    </a>

    <h1 class="mt-1 text-xl font-semibold text-text">{{ __('profile.need_edit_title') }}</h1>

    @if ($errors->any())
        <div class="mt-4 rounded-lg border border-accent/30 bg-accent/5 px-4 py-3 text-sm text-accent">
            <ul class="list-inside list-disc space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('profile.need.store') }}" class="mt-6 max-w-2xl space-y-4 rounded-2xl border border-border bg-surface p-6">
        @csrf

        <div>
            <label class="block text-sm font-medium text-text">{{ __('profile.need_description_label') }}</label>
            <textarea
                name="description" rows="5"
                class="mt-1 block w-full rounded-lg border border-border px-3 py-2 text-sm focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary"
            >{{ old('description') }}</textarea>
            <p class="mt-1 text-xs text-muted">{{ __('profile.need_description_hint') }}</p>
        </div>

        <button type="submit" class="rounded-lg bg-primary px-4 py-2 text-sm font-semibold text-white hover:bg-primary-dark">
            {{ __('profile.save') }}
        </button>
    </form>

    <div class="mt-8 max-w-2xl">
        <h2 class="text-sm font-semibold uppercase tracking-wide text-muted">{{ __('profile.need_list_title') }}</h2>

        @if ($membership->needs->isEmpty())
            <p class="mt-3 text-sm text-muted">{{ __('profile.need_list_empty') }}</p>
        @else
            <ul class="mt-3 space-y-3 text-sm">
                @foreach ($membership->needs as $need)
                    @php
                        $statusClass = match ($need->status) {
                            'resolved' => 'bg-primary-light text-primary',
                            'in_progress' => 'bg-secondary/20 text-secondary',
                            default => 'bg-border text-muted',
                        };
                    @endphp
                    <li class="rounded-2xl border border-border bg-surface p-4">
                        <div class="flex items-start justify-between gap-2">
                            <p class="whitespace-pre-line text-text">{{ $need->description }}</p>
                            <span class="shrink-0 rounded-full px-2 py-0.5 text-xs font-semibold {{ $statusClass }}">
                                {{ __('forms.status_'.$need->status) }}
                            </span>
                        </div>

                        <a
                            href="{{ route('support.create', ['related_type' => 'need', 'related_id' => $need->id]) }}"
                            class="mt-2 inline-block text-xs font-medium text-primary hover:underline"
                        >
                            <i class="bi bi-chat-dots"></i> {{ __('profile.discuss_declaration_button') }}
                        </a>

                        <div class="mt-3 border-t border-border pt-3">
                            <p class="text-xs font-semibold uppercase tracking-wide text-muted">{{ __('profile.need_justifications_title') }}</p>

                            @if ($need->documents->isEmpty())
                                <p class="mt-1.5 text-xs text-muted">{{ __('profile.need_no_justifications') }}</p>
                            @else
                                <ul class="mt-1.5 space-y-1.5">
                                    @foreach ($need->documents as $document)
                                        <li class="flex items-center justify-between gap-2 text-xs">
                                            <a href="{{ $document->file_url }}" target="_blank" class="font-medium text-primary hover:underline">
                                                {{ $document->title ?: $document->original_name }}
                                            </a>
                                            <form
                                                method="POST" action="{{ route('profile.documents.destroy', $document) }}"
                                                data-confirm="{{ __('profile.delete_document_confirm') }}"
                                                data-confirm-button="{{ __('messages.delete') }}"
                                                data-cancel-button="{{ __('messages.cancel') }}"
                                            >
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="font-medium text-accent hover:underline">
                                                    {{ __('messages.delete') }}
                                                </button>
                                            </form>
                                        </li>
                                    @endforeach
                                </ul>
                            @endif

                            <form
                                method="POST" action="{{ route('profile.documents.store') }}" enctype="multipart/form-data"
                                class="mt-2 flex flex-wrap items-center gap-2"
                            >
                                @csrf
                                <input type="hidden" name="document_type" value="need_justification">
                                <input type="hidden" name="related_type" value="need">
                                <input type="hidden" name="related_id" value="{{ $need->id }}">
                                <input type="file" name="file" accept=".pdf,.jpg,.jpeg,.png" required class="text-xs">
                                <button type="submit" class="rounded-lg border border-border px-2.5 py-1 text-xs font-medium text-primary hover:bg-primary-light">
                                    <i class="bi bi-upload"></i> {{ __('profile.upload_document') }}
                                </button>
                            </form>
                        </div>
                    </li>
                @endforeach
            </ul>
        @endif
    </div>
@endsection
