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

    <form method="POST" action="{{ route('profile.need.update') }}" class="mt-6 max-w-2xl space-y-4 rounded-2xl border border-border bg-surface p-6">
        @csrf
        @method('PUT')

        <div>
            <label class="block text-sm font-medium text-text">{{ __('profile.need_description_label') }}</label>
            <textarea
                name="population_needs" rows="5"
                class="mt-1 block w-full rounded-lg border border-border px-3 py-2 text-sm focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary"
            >{{ old('population_needs', $membership->population_needs) }}</textarea>
            <p class="mt-1 text-xs text-muted">{{ __('profile.need_description_hint') }}</p>
        </div>

        <button type="submit" class="rounded-lg bg-primary px-4 py-2 text-sm font-semibold text-white hover:bg-primary-dark">
            {{ __('profile.save') }}
        </button>
    </form>

    <div class="mt-8 max-w-2xl">
        <h2 class="text-sm font-semibold uppercase tracking-wide text-muted">{{ __('profile.need_justifications_title') }}</h2>

        @php $needDocuments = $membership->documents->where('document_type', 'need_justification'); @endphp

        @if ($needDocuments->isEmpty())
            <p class="mt-3 text-sm text-muted">{{ __('profile.need_no_justifications') }}</p>
        @else
            <div class="mt-3 space-y-2">
                @foreach ($needDocuments as $document)
                    <div class="flex items-center justify-between rounded-2xl border border-border bg-surface p-4">
                        <div>
                            <p class="font-medium text-text">{{ $document->title ?: $document->original_name }}</p>
                            <p class="text-xs text-muted">{{ $document->created_at->format('d/m/Y') }}</p>
                        </div>
                        <div class="flex items-center gap-3">
                            <a href="{{ $document->file_url }}" target="_blank" class="text-sm font-medium text-primary hover:underline">
                                {{ __('documents.view') }}
                            </a>
                            <form
                                method="POST" action="{{ route('profile.documents.destroy', $document) }}"
                                data-confirm="{{ __('profile.delete_document_confirm') }}"
                                data-confirm-button="{{ __('messages.delete') }}"
                                data-cancel-button="{{ __('messages.cancel') }}"
                            >
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-sm font-medium text-accent hover:underline">
                                    {{ __('messages.delete') }}
                                </button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('profile.documents.store') }}" enctype="multipart/form-data" class="mt-4 space-y-4 rounded-2xl border border-border bg-surface p-6">
            @csrf
            <input type="hidden" name="document_type" value="need_justification">

            <div>
                <label class="block text-sm font-medium text-text">{{ __('profile.document_title_label') }}</label>
                <input type="text" name="title" class="mt-1 block w-full rounded-lg border border-border px-3 py-2 text-sm focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary">
            </div>

            <div>
                <label class="block text-sm font-medium text-text">{{ __('profile.document_file_label') }}</label>
                <input type="file" name="file" accept=".pdf,.jpg,.jpeg,.png" required class="mt-1 block w-full text-sm">
            </div>

            <button type="submit" class="inline-flex items-center gap-2 rounded-lg bg-primary px-4 py-2 text-sm font-semibold text-white hover:bg-primary-dark">
                <i class="bi bi-upload"></i> {{ __('profile.upload_document') }}
            </button>
        </form>
    </div>
@endsection
