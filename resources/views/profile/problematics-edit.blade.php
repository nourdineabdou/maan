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
                </label>

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
