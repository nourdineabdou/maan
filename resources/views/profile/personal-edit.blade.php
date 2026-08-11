@extends('layouts.app')

@section('title', __('profile.personal_edit_title'))

@section('content')
    <a href="{{ route('profile.overview') }}" class="text-sm text-muted hover:text-primary">
        <i class="bi bi-arrow-left"></i> {{ __('profile.overview_title') }}
    </a>

    <h1 class="mt-1 text-xl font-semibold text-text">{{ __('profile.personal_edit_title') }}</h1>

    @if ($errors->any())
        <div class="mt-4 rounded-lg border border-accent/30 bg-accent/5 px-4 py-3 text-sm text-accent">
            <ul class="list-inside list-disc space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <p class="mt-4 text-sm text-muted">
        {{ __('profile.photo_moved_hint') }}
        <a href="{{ route('profile.overview') }}" class="font-medium text-primary hover:underline">{{ __('profile.overview_title') }}</a>.
    </p>

    <form method="POST" action="{{ route('profile.personal.update') }}" class="mt-6 max-w-2xl space-y-6 rounded-2xl border border-border bg-surface p-6">
        @csrf
        @method('PUT')

        <div class="grid gap-4 sm:grid-cols-2">
            <div>
                <label class="block text-sm font-medium text-text">{{ __('profile.label_first_name') }} <span class="text-accent">*</span></label>
                <input type="text" name="first_name" value="{{ old('first_name', $profile?->first_name) }}" required
                    class="mt-1 block w-full rounded-lg border border-border px-3 py-2 text-sm focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary">
            </div>
            <div>
                <label class="block text-sm font-medium text-text">{{ __('profile.label_last_name') }} <span class="text-accent">*</span></label>
                <input type="text" name="last_name" value="{{ old('last_name', $profile?->last_name) }}" required
                    class="mt-1 block w-full rounded-lg border border-border px-3 py-2 text-sm focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary">
            </div>
        </div>

        <div class="grid gap-4 sm:grid-cols-2">
            <div>
                <label class="block text-sm font-medium text-text">{{ __('memberships.field_gender') }} <span class="text-accent">*</span></label>
                <select name="gender" required class="mt-1 block w-full rounded-lg border border-border px-3 py-2 text-sm focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary">
                    <option value="" disabled @selected(! old('gender', $profile?->gender) || old('gender', $profile?->gender) === 'unspecified')>{{ __('forms.select_placeholder') }}</option>
                    <option value="male" @selected(old('gender', $profile?->gender) === 'male')>{{ __('forms.gender_male') }}</option>
                    <option value="female" @selected(old('gender', $profile?->gender) === 'female')>{{ __('forms.gender_female') }}</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-text">{{ __('memberships.field_nni') }} <span class="text-accent">*</span></label>
                <input type="text" name="nni" value="{{ old('nni', $profile?->nni) }}" required
                    inputmode="numeric" maxlength="10" pattern="[0-9]{10}"
                    class="mt-1 block w-full rounded-lg border border-border px-3 py-2 text-sm focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary">
            </div>
        </div>

        <div class="grid gap-4 sm:grid-cols-2">
            <div>
                <label class="block text-sm font-medium text-text">{{ __('memberships.field_region') }} <span class="text-accent">*</span></label>
                <select id="region_id" name="region_id" required class="mt-1 block w-full rounded-lg border border-border px-3 py-2 text-sm focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary">
                    <option value="">{{ __('forms.select_placeholder') }}</option>
                    @foreach ($regions as $region)
                        <option value="{{ $region->id }}" @selected(old('region_id', $profile?->region_id) == $region->id)>
                            {{ $region->getTranslation('name') }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-text">{{ __('memberships.field_moughataa') }} <span class="text-accent">*</span></label>
                <select id="moughataa_id" name="moughataa_id" required class="mt-1 block w-full rounded-lg border border-border px-3 py-2 text-sm focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary">
                    <option value="">{{ __('forms.select_placeholder') }}</option>
                </select>
            </div>
        </div>

        <div class="border-t border-dashed border-border pt-5">
            <p class="text-sm font-semibold text-text">{{ __('profile.optional_section_title') }}</p>
            <p class="text-xs text-muted">{{ __('profile.optional_section_hint') }}</p>

            <div class="mt-3 grid gap-4 sm:grid-cols-2">
                <div>
                    <label class="block text-sm font-medium text-text">{{ __('memberships.field_birth_date') }}</label>
                    <input type="date" name="birth_date" value="{{ old('birth_date', $profile?->birth_date?->format('Y-m-d')) }}"
                        class="mt-1 block w-full rounded-lg border border-border px-3 py-2 text-sm focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary">
                </div>

                <div>
                    <label class="block text-sm font-medium text-text">{{ __('memberships.field_commune') }}</label>
                    <select id="commune_id" name="commune_id" class="mt-1 block w-full rounded-lg border border-border px-3 py-2 text-sm focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary">
                        <option value="">{{ __('forms.select_placeholder') }}</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-text">{{ __('memberships.field_locality') }}</label>
                    <input type="text" name="locality" value="{{ old('locality', $profile?->locality) }}"
                        class="mt-1 block w-full rounded-lg border border-border px-3 py-2 text-sm focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary">
                </div>

                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium text-text">{{ __('memberships.field_address') }}</label>
                    <textarea name="address" rows="2" class="mt-1 block w-full rounded-lg border border-border px-3 py-2 text-sm focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary">{{ old('address', $profile?->address) }}</textarea>
                </div>
            </div>
        </div>

        <button type="submit" class="rounded-lg bg-primary px-4 py-2 text-sm font-semibold text-white hover:bg-primary-dark">
            {{ __('profile.save') }}
        </button>
    </form>
@endsection

@push('scripts')
    <script>
        (function () {
            const regionSelect = document.getElementById('region_id');
            const moughataaSelect = document.getElementById('moughataa_id');
            const communeSelect = document.getElementById('commune_id');

            const initialMoughataaId = {{ old('moughataa_id', $profile?->moughataa_id) ?? 'null' }};
            const initialCommuneId = {{ old('commune_id', $profile?->commune_id) ?? 'null' }};

            function fillSelect(select, items, selectedId) {
                select.innerHTML = '<option value="">{{ __('forms.select_placeholder') }}</option>';
                items.forEach((item) => {
                    const option = document.createElement('option');
                    option.value = item.id;
                    option.textContent = item.name;
                    if (selectedId && Number(selectedId) === item.id) {
                        option.selected = true;
                    }
                    select.appendChild(option);
                });
            }

            function loadMoughataas(regionId, selectedMoughataaId) {
                if (! regionId) {
                    moughataaSelect.innerHTML = '<option value="">{{ __('forms.select_placeholder') }}</option>';
                    communeSelect.innerHTML = '<option value="">{{ __('forms.select_placeholder') }}</option>';
                    return;
                }

                fetch(`{{ route('geo.moughataas') }}?region_id=${regionId}`)
                    .then((res) => res.json())
                    .then((data) => {
                        fillSelect(moughataaSelect, data, selectedMoughataaId);
                        if (selectedMoughataaId) {
                            loadCommunes(selectedMoughataaId, initialCommuneId);
                        }
                    });
            }

            function loadCommunes(moughataaId, selectedCommuneId) {
                if (! moughataaId) {
                    communeSelect.innerHTML = '<option value="">{{ __('forms.select_placeholder') }}</option>';
                    return;
                }

                fetch(`{{ route('geo.communes') }}?moughataa_id=${moughataaId}`)
                    .then((res) => res.json())
                    .then((data) => fillSelect(communeSelect, data, selectedCommuneId));
            }

            regionSelect.addEventListener('change', () => loadMoughataas(regionSelect.value, null));
            moughataaSelect.addEventListener('change', () => loadCommunes(moughataaSelect.value, null));

            if (regionSelect.value) {
                loadMoughataas(regionSelect.value, initialMoughataaId);
            }
        })();
    </script>
@endpush
