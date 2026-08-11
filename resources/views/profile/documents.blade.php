@extends('layouts.app')

@section('title', __('profile.documents_title'))

@section('content')
    <a href="{{ route('profile.overview') }}" class="text-sm text-muted hover:text-primary">
        <i class="bi bi-arrow-left"></i> {{ __('profile.overview_title') }}
    </a>

    <h1 class="mt-1 text-xl font-semibold text-text">{{ __('profile.documents_title') }}</h1>

    @if ($errors->any())
        <div class="mt-4 rounded-lg border border-accent/30 bg-accent/5 px-4 py-3 text-sm text-accent">
            <ul class="list-inside list-disc space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @php
        $documentsByType = $membership->documents->groupBy('document_type');
        $tileTypes = [
            ['type' => 'identity_card_front', 'required' => true],
            ['type' => 'identity_card_back', 'required' => true],
            ['type' => 'member_photo', 'required' => true],
            ['type' => 'diploma', 'required' => false],
            ['type' => 'cv', 'required' => false],
        ];
        $otherDocuments = $membership->documents->reject(
            fn ($document) => in_array($document->document_type, array_column($tileTypes, 'type'), true)
        );
    @endphp

    <div class="mt-6 grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
        @foreach ($tileTypes as $tile)
            @php $existing = $documentsByType->get($tile['type'])?->first(); @endphp

            <div class="rounded-2xl border p-4 {{ $existing ? 'border-primary/30 bg-primary-light/40' : 'border-dashed border-border bg-surface' }}">
                <div class="flex items-start justify-between gap-2">
                    <div class="flex items-center gap-2 text-sm font-medium text-text">
                        <i class="bi {{ $existing ? 'bi-check-circle-fill text-primary' : ($tile['required'] ? 'bi-exclamation-circle text-accent' : 'bi-file-earmark') }}"></i>
                        {{ __('documents.type_'.$tile['type']) }}
                    </div>
                </div>
                <p class="mt-0.5 text-xs text-muted">{{ $tile['required'] ? __('documents.required') : __('documents.optional') }}</p>

                @if ($existing)
                    <p class="mt-3 truncate text-xs text-muted">{{ $existing->title ?: $existing->original_name }}</p>
                    <div class="mt-2 flex items-center gap-3 text-sm">
                        <a href="{{ $existing->file_url }}" target="_blank" class="font-medium text-primary hover:underline">
                            {{ __('documents.view') }}
                        </a>
                        <form
                            method="POST" action="{{ route('profile.documents.destroy', $existing) }}"
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
                    </div>
                @else
                    <form method="POST" action="{{ route('profile.documents.store') }}" enctype="multipart/form-data" class="mt-3">
                        @csrf
                        <input type="hidden" name="document_type" value="{{ $tile['type'] }}">
                        <label class="flex cursor-pointer items-center justify-center gap-2 rounded-lg border border-primary/40 bg-primary-light px-3 py-2 text-sm font-medium text-primary hover:bg-primary/10">
                            <i class="bi bi-upload"></i> {{ __('documents.upload_button') }}
                            <input type="file" name="file" accept=".pdf,.jpg,.jpeg,.png" class="hidden" onchange="this.form.submit()">
                        </label>
                    </form>
                @endif
            </div>
        @endforeach
    </div>

    <div class="mt-8">
        <h2 class="text-sm font-semibold uppercase tracking-wide text-muted">{{ __('documents.add_other_document') }}</h2>

        @if ($otherDocuments->isNotEmpty())
            <div class="mt-3 space-y-2">
                @foreach ($otherDocuments as $document)
                    <div class="flex items-center justify-between rounded-2xl border border-border bg-surface p-4">
                        <div>
                            <p class="font-medium text-text">{{ $document->title ?: $document->original_name }}</p>
                            <p class="text-xs text-muted">{{ __('documents.type_'.$document->document_type) }}</p>
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

        <form method="POST" action="{{ route('profile.documents.store') }}" enctype="multipart/form-data" class="mt-4 max-w-xl space-y-4 rounded-2xl border border-border bg-surface p-6">
            @csrf

            <div>
                <label class="block text-sm font-medium text-text">{{ __('profile.document_type_label') }}</label>
                <select name="document_type" required class="mt-1 block w-full rounded-lg border border-border px-3 py-2 text-sm focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary">
                    @foreach (['problematic_justification', 'other'] as $type)
                        <option value="{{ $type }}">{{ __('documents.type_'.$type) }}</option>
                    @endforeach
                </select>
            </div>

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
