@extends('layouts.app')

@section('title', __('notifications.create_title'))

@section('content')
    <a href="{{ route('admin.notifications.index') }}" class="text-sm text-muted hover:text-primary">
        <i class="bi bi-arrow-left"></i> {{ __('notifications.title') }}
    </a>

    <h1 class="mt-1 text-xl font-semibold text-text">{{ __('notifications.create_title') }}</h1>

    <form method="POST" action="{{ route('admin.notifications.store') }}" class="mt-6 max-w-2xl space-y-4 rounded-2xl border border-border bg-surface p-6">
        @csrf

        <div class="grid gap-4 sm:grid-cols-2">
            <div>
                <label class="block text-sm font-medium text-text">{{ __('notifications.label_title_fr') }}</label>
                <input type="text" name="title_fr" value="{{ old('title_fr') }}" required
                    class="mt-1 block w-full rounded-lg border border-border px-3 py-2 text-sm focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary">
            </div>
            <div>
                <label class="block text-sm font-medium text-text">{{ __('notifications.label_title_ar') }}</label>
                <input type="text" name="title_ar" dir="rtl" value="{{ old('title_ar') }}" required
                    class="mt-1 block w-full rounded-lg border border-border px-3 py-2 text-sm focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary">
            </div>
        </div>

        <div class="grid gap-4 sm:grid-cols-2">
            <div>
                <label class="block text-sm font-medium text-text">{{ __('notifications.label_message_fr') }}</label>
                <textarea name="message_fr" rows="4" required
                    class="mt-1 block w-full rounded-lg border border-border px-3 py-2 text-sm focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary">{{ old('message_fr') }}</textarea>
            </div>
            <div>
                <label class="block text-sm font-medium text-text">{{ __('notifications.label_message_ar') }}</label>
                <textarea name="message_ar" dir="rtl" rows="4" required
                    class="mt-1 block w-full rounded-lg border border-border px-3 py-2 text-sm focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary">{{ old('message_ar') }}</textarea>
            </div>
        </div>

        <div>
            <label class="block text-sm font-medium text-text">{{ __('notifications.label_target_type') }}</label>
            <select id="target_type" name="target_type" required
                class="mt-1 block w-full rounded-lg border border-border px-3 py-2 text-sm focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary">
                <option value="all" @selected(old('target_type', 'all') === 'all')>{{ __('notifications.target_all') }}</option>
                <option value="region" @selected(old('target_type') === 'region')>{{ __('notifications.target_region') }}</option>
                <option value="employment_status" @selected(old('target_type') === 'employment_status')>{{ __('notifications.target_employment_status') }}</option>
                <option value="membership_status" @selected(old('target_type') === 'membership_status')>{{ __('notifications.target_membership_status') }}</option>
            </select>
        </div>

        <div id="target-value-wrapper" class="hidden">
            <label class="block text-sm font-medium text-text">{{ __('notifications.label_target_value') }}</label>

            <select data-target="region" name="target_value" class="target-value-select mt-1 hidden w-full rounded-lg border border-border px-3 py-2 text-sm focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary">
                <option value="">{{ __('notifications.select_placeholder') }}</option>
                @foreach ($regions as $region)
                    <option value="{{ $region->id }}">{{ $region->getTranslation('name') }}</option>
                @endforeach
            </select>

            <select data-target="employment_status" name="target_value" class="target-value-select mt-1 hidden w-full rounded-lg border border-border px-3 py-2 text-sm focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary">
                <option value="">{{ __('notifications.select_placeholder') }}</option>
                @foreach (['employed', 'unemployed', 'student', 'retired', 'other'] as $status)
                    <option value="{{ $status }}">{{ __('forms.employment_status_'.$status) }}</option>
                @endforeach
            </select>

            <select data-target="membership_status" name="target_value" class="target-value-select mt-1 hidden w-full rounded-lg border border-border px-3 py-2 text-sm focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary">
                <option value="">{{ __('notifications.select_placeholder') }}</option>
                @foreach (['pending', 'approved', 'rejected'] as $status)
                    <option value="{{ $status }}">{{ __('dashboard.status_'.$status) }}</option>
                @endforeach
            </select>
        </div>

        <button type="submit" class="inline-flex items-center gap-2 rounded-lg bg-primary px-4 py-2 text-sm font-semibold text-white hover:bg-primary-dark">
            <i class="bi bi-send"></i> {{ __('notifications.send') }}
        </button>
    </form>
@endsection

@push('scripts')
    <script>
        (function () {
            const typeSelect = document.getElementById('target_type');
            const wrapper = document.getElementById('target-value-wrapper');
            const valueSelects = document.querySelectorAll('.target-value-select');

            function refresh() {
                const type = typeSelect.value;
                wrapper.classList.toggle('hidden', type === 'all');

                valueSelects.forEach((select) => {
                    const isMatch = select.dataset.target === type;
                    select.classList.toggle('hidden', !isMatch);
                    select.disabled = ! isMatch;
                });
            }

            typeSelect.addEventListener('change', refresh);
            refresh();
        })();
    </script>
@endpush
