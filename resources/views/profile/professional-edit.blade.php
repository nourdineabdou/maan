@extends('layouts.app')

@section('title', __('profile.professional_edit_title'))

@section('content')
    <a href="{{ route('profile.overview') }}" class="text-sm text-muted hover:text-primary">
        <i class="bi bi-arrow-left"></i> {{ __('profile.overview_title') }}
    </a>

    <h1 class="mt-1 text-xl font-semibold text-text">{{ __('profile.professional_edit_title') }}</h1>

    @if ($errors->any())
        <div class="mt-4 rounded-lg border border-accent/30 bg-accent/5 px-4 py-3 text-sm text-accent">
            <ul class="list-inside list-disc space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('profile.professional.update') }}" class="mt-6 max-w-2xl space-y-6 rounded-2xl border border-border bg-surface p-6">
        @csrf
        @method('PUT')

        @php
            $currentStatus = old('employment_status', $profile?->employment_status);
            $currentSector = old('employment_sector', $profile?->employment_sector);
        @endphp

        <div class="grid gap-4 sm:grid-cols-2">
            <div>
                <label class="block text-sm font-medium text-text">{{ __('memberships.field_employment_status') }}</label>
                <select id="employment_status" name="employment_status" class="mt-1 block w-full rounded-lg border border-border px-3 py-2 text-sm focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary">
                    <option value="" @selected(! $currentStatus || $currentStatus === 'unspecified')>{{ __('forms.select_placeholder') }}</option>
                    @foreach (['employed', 'unemployed', 'student', 'retired', 'other'] as $status)
                        <option value="{{ $status }}" @selected($currentStatus === $status)>
                            {{ __('forms.employment_status_'.$status) }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-text">{{ __('memberships.field_employment_sector') }}</label>
                <select name="employment_sector" class="mt-1 block w-full rounded-lg border border-border px-3 py-2 text-sm focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary">
                    <option value="" @selected(! $currentSector || $currentSector === 'unspecified')>{{ __('forms.select_placeholder') }}</option>
                    @foreach (['public', 'private', 'informal', 'self_employed', 'ngo', 'other'] as $sector)
                        <option value="{{ $sector }}" @selected($currentSector === $sector)>
                            {{ __('forms.employment_sector_'.$sector) }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        <div id="other-details-wrapper" class="hidden">
            <label class="block text-sm font-medium text-text">{{ __('forms.employment_status_other') }}</label>
            <textarea name="other_employment_details" rows="2" class="mt-1 block w-full rounded-lg border border-border px-3 py-2 text-sm focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary">{{ old('other_employment_details', $profile?->other_employment_details) }}</textarea>
        </div>

        <div class="grid gap-4 sm:grid-cols-2">
            <div>
                <label class="block text-sm font-medium text-text">{{ __('memberships.field_function') }}</label>
                <input type="text" name="function" value="{{ old('function', $profile?->function) }}"
                    class="mt-1 block w-full rounded-lg border border-border px-3 py-2 text-sm focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary">
            </div>
            <div>
                <label class="block text-sm font-medium text-text">{{ __('memberships.field_position') }}</label>
                <input type="text" name="position" value="{{ old('position', $profile?->position) }}"
                    class="mt-1 block w-full rounded-lg border border-border px-3 py-2 text-sm focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary">
            </div>
        </div>

        <div>
            <label class="block text-sm font-medium text-text">{{ __('memberships.field_employer') }}</label>
            <input type="text" name="employer" value="{{ old('employer', $profile?->employer) }}"
                class="mt-1 block w-full rounded-lg border border-border px-3 py-2 text-sm focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary">
        </div>

        <button type="submit" class="rounded-lg bg-primary px-4 py-2 text-sm font-semibold text-white hover:bg-primary-dark">
            {{ __('profile.save') }}
        </button>
    </form>
@endsection

@push('scripts')
    <script>
        (function () {
            const statusSelect = document.getElementById('employment_status');
            const otherWrapper = document.getElementById('other-details-wrapper');

            function refresh() {
                otherWrapper.classList.toggle('hidden', statusSelect.value !== 'other');
            }

            statusSelect.addEventListener('change', refresh);
            refresh();
        })();
    </script>
@endpush
