@extends('layouts.app')

@section('title', __('profile.my_declared_needs_title'))

@section('content')
    <a href="{{ route('profile.overview') }}" class="text-sm text-muted hover:text-primary">
        <i class="bi bi-arrow-left"></i> {{ __('profile.overview_title') }}
    </a>

    <div class="mt-1 flex flex-wrap items-center justify-between gap-3">
        <h1 class="text-xl font-semibold text-text">{{ __('profile.my_declared_needs_title') }}</h1>
        <a href="{{ route('profile.need.edit') }}" class="inline-flex items-center gap-2 rounded-lg bg-primary px-4 py-2 text-sm font-semibold text-white hover:bg-primary-dark">
            <i class="bi bi-plus-circle"></i> {{ __('profile.need_add_button') }}
        </a>
    </div>

    <form method="GET" class="mt-4 flex flex-wrap items-end gap-3 rounded-2xl border border-border bg-surface p-4">
        <div class="min-w-[160px]">
            <label class="block text-xs font-medium text-muted">{{ __('memberships.filter_declaration_status') }}</label>
            <select name="status" class="mt-1 block w-full rounded-lg border border-border px-3 py-2 text-sm focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary">
                <option value="">{{ __('memberships.filter_all_declaration_statuses') }}</option>
                @foreach (['submitted', 'in_progress', 'resolved'] as $statusOption)
                    <option value="{{ $statusOption }}" @selected($filters['status'] === $statusOption)>{{ __('forms.status_'.$statusOption) }}</option>
                @endforeach
            </select>
        </div>

        <div class="min-w-[150px]">
            <label class="block text-xs font-medium text-muted">{{ __('memberships.filter_date_from') }}</label>
            <input type="date" name="date_from" value="{{ $filters['date_from'] }}"
                class="mt-1 block w-full rounded-lg border border-border px-3 py-2 text-sm focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary">
        </div>

        <div class="min-w-[150px]">
            <label class="block text-xs font-medium text-muted">{{ __('memberships.filter_date_to') }}</label>
            <input type="date" name="date_to" value="{{ $filters['date_to'] }}"
                class="mt-1 block w-full rounded-lg border border-border px-3 py-2 text-sm focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary">
        </div>

        <button type="submit" class="rounded-lg bg-primary px-4 py-2 text-sm font-semibold text-white hover:bg-primary-dark">
            {{ __('memberships.apply_filters') }}
        </button>
    </form>

    @if ($needs->isEmpty())
        <div class="mt-6 rounded-2xl border border-dashed border-border bg-surface p-10 text-center text-sm text-muted">
            {{ __('profile.need_list_empty') }}
        </div>
    @else
        <ul class="mt-6 space-y-3 text-sm">
            @foreach ($needs as $need)
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
                    <p class="mt-1 text-xs text-muted">{{ $need->created_at->format('d/m/Y') }}</p>

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

        <div class="mt-6">
            {{ $needs->links() }}
        </div>
    @endif
@endsection
