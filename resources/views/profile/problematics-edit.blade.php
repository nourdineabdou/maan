@extends('layouts.app')

@section('title', __('profile.problematics_edit_title'))

@section('content')
    <a href="{{ route('profile.overview') }}" class="text-sm text-muted hover:text-primary">
        <i class="bi bi-arrow-left"></i> {{ __('profile.overview_title') }}
    </a>

    <h1 class="mt-1 text-xl font-semibold text-text">{{ __('profile.problematics_edit_title') }}</h1>
    <p class="mt-1 text-sm text-muted">{{ __('profile.select_problematics_hint') }}</p>

    @if ($errors->any())
        <div class="mt-4 rounded-lg border border-accent/30 bg-accent/5 px-4 py-3 text-sm text-accent">
            <ul class="list-inside list-disc space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('profile.problematics.update') }}" class="mt-6 max-w-2xl space-y-3">
        @csrf
        @method('PUT')

        @foreach ($problematics as $problematic)
            @php
                $pivot = $membership->problematics->firstWhere('id', $problematic->id)?->pivot;
                $checked = old('problematics') ? in_array($problematic->id, old('problematics')) : (bool) $pivot;
            @endphp
            <div class="rounded-2xl border border-border bg-surface p-4">
                <label class="flex items-center gap-3">
                    <input
                        type="checkbox" name="problematics[]" value="{{ $problematic->id }}"
                        class="problematic-toggle rounded border-border text-primary focus:ring-primary"
                        @checked($checked)
                    >
                    @if ($problematic->icon)<i class="bi {{ $problematic->icon }} text-primary"></i>@endif
                    <span class="font-medium text-text">{{ $problematic->getTranslation('name') }}</span>

                    @if ($pivot)
                        @php
                            $statusClass = match ($pivot->status) {
                                'resolved' => 'bg-primary-light text-primary',
                                'in_progress' => 'bg-secondary/20 text-secondary',
                                default => 'bg-border text-muted',
                            };
                        @endphp
                        <span class="ms-auto shrink-0 rounded-full px-2 py-0.5 text-xs font-semibold {{ $statusClass }}">
                            {{ __('forms.status_'.$pivot->status) }}
                        </span>
                    @endif
                </label>

                @if ($pivot)
                    <a
                        href="{{ route('support.create', ['related_type' => 'problematic', 'related_id' => $pivot->id]) }}"
                        class="mt-2 inline-block text-xs font-medium text-primary hover:underline"
                    >
                        <i class="bi bi-chat-dots"></i> {{ __('profile.discuss_declaration_button') }}
                    </a>
                @endif

                <div class="problematic-details mt-3 grid gap-3 sm:grid-cols-2 {{ $checked ? '' : 'hidden' }}">
                    <div class="sm:col-span-2">
                        <label class="block text-xs font-medium text-muted">
                            {{ __('profile.description_label') }}
                            @if ($problematic->code === 'other')<span class="text-accent">*</span>@endif
                        </label>
                        <textarea name="details[{{ $problematic->id }}][description]" rows="2"
                            class="mt-1 block w-full rounded-lg border border-border px-3 py-2 text-sm focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary"
                        >{{ old("details.{$problematic->id}.description", $pivot?->description) }}</textarea>
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-muted">{{ __('profile.solution_label') }}</label>
                        <input type="text" name="details[{{ $problematic->id }}][requested_solution]" value="{{ old("details.{$problematic->id}.requested_solution", $pivot?->requested_solution) }}"
                            class="mt-1 block w-full rounded-lg border border-border px-3 py-2 text-sm focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary">
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-muted">{{ __('profile.locality_label') }}</label>
                        <input type="text" name="details[{{ $problematic->id }}][locality]" value="{{ old("details.{$problematic->id}.locality", $pivot?->locality) }}"
                            class="mt-1 block w-full rounded-lg border border-border px-3 py-2 text-sm focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary">
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-muted">{{ __('profile.priority_label') }}</label>
                        <select name="details[{{ $problematic->id }}][priority]" class="mt-1 block w-full rounded-lg border border-border px-3 py-2 text-sm focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary">
                            @foreach (['low', 'normal', 'high', 'urgent'] as $priority)
                                <option value="{{ $priority }}" @selected(old("details.{$problematic->id}.priority", $pivot?->priority ?? 'normal') === $priority)>
                                    {{ __('forms.priority_'.$priority) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        @endforeach

        <button type="submit" class="rounded-lg bg-primary px-4 py-2 text-sm font-semibold text-white hover:bg-primary-dark">
            {{ __('profile.save') }}
        </button>
    </form>

    @if ($membership->problematics->isNotEmpty())
        <div class="mt-8 max-w-2xl space-y-3">
            <h2 class="text-sm font-semibold uppercase tracking-wide text-muted">{{ __('profile.problematic_justifications_title') }}</h2>

            @foreach ($membership->problematics as $problematic)
                <div class="rounded-2xl border border-border bg-surface p-4">
                    <p class="text-sm font-medium text-text">{{ $problematic->getTranslation('name') }}</p>

                    @if ($problematic->pivot->documents->isEmpty())
                        <p class="mt-1.5 text-xs text-muted">{{ __('profile.need_no_justifications') }}</p>
                    @else
                        <ul class="mt-1.5 space-y-1.5">
                            @foreach ($problematic->pivot->documents as $document)
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
                        <input type="hidden" name="document_type" value="problematic_justification">
                        <input type="hidden" name="related_type" value="problematic">
                        <input type="hidden" name="related_id" value="{{ $problematic->pivot->id }}">
                        <input type="file" name="file" accept=".pdf,.jpg,.jpeg,.png" required class="text-xs">
                        <button type="submit" class="rounded-lg border border-border px-2.5 py-1 text-xs font-medium text-primary hover:bg-primary-light">
                            <i class="bi bi-upload"></i> {{ __('profile.upload_document') }}
                        </button>
                    </form>
                </div>
            @endforeach
        </div>
    @endif
@endsection

@push('scripts')
    <script>
        document.querySelectorAll('.problematic-toggle').forEach((checkbox) => {
            checkbox.addEventListener('change', () => {
                const details = checkbox.closest('div').nextElementSibling;
                details.classList.toggle('hidden', ! checkbox.checked);
            });
        });
    </script>
@endpush
