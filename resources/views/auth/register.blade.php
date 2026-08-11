@extends('layouts.guest')

@section('card_width', 'max-w-2xl')

@section('content')
    <h1 class="text-xl font-semibold text-primary">{{ __('auth.register_title') }}</h1>
    <p class="mt-1 text-sm text-muted">{{ __('auth.register_subtitle') }}</p>

    <div id="register-errors">
        @if ($errors->any())
            <div class="mt-4 rounded-lg border border-accent/30 bg-accent/5 px-4 py-3 text-sm text-accent">
                <ul class="list-inside list-disc space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
    </div>

    <form id="register-form" method="POST" action="{{ route('register') }}" enctype="multipart/form-data" class="mt-6 space-y-8">
        @csrf

        <div class="space-y-4">
            <p class="text-sm font-semibold uppercase tracking-wide text-muted">{{ __('auth.account_section_title') }}</p>

            <div>
                <label for="name" class="block text-sm font-medium text-text">{{ __('auth.name') }} <span class="text-accent">*</span></label>
                <input
                    type="text" id="name" name="name" value="{{ old('name') }}" required autofocus
                    class="mt-1 block w-full rounded-lg border border-border px-3 py-2 text-sm focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary"
                >
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label for="phone" class="block text-sm font-medium text-text">{{ __('auth.phone') }} <span class="text-accent">*</span></label>
                    <input
                        type="tel" id="phone" name="phone" value="{{ old('phone') }}" required
                        placeholder="{{ __('auth.phone_placeholder') }}" maxlength="8" pattern="[234][0-9]{7}"
                        class="mt-1 block w-full rounded-lg border border-border px-3 py-2 text-sm focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary"
                    >
                    <p class="mt-1 text-xs text-muted">{{ __('auth.phone_hint') }}</p>
                </div>

                <div>
                    <label for="email" class="block text-sm font-medium text-text">{{ __('auth.email_optional') }}</label>
                    <input
                        type="email" id="email" name="email" value="{{ old('email') }}"
                        class="mt-1 block w-full rounded-lg border border-border px-3 py-2 text-sm focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary"
                    >
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label for="password" class="block text-sm font-medium text-text">{{ __('auth.password_label') }} <span class="text-accent">*</span></label>
                    <input
                        type="password" id="password" name="password" required minlength="4"
                        class="mt-1 block w-full rounded-lg border border-border px-3 py-2 text-sm focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary"
                    >
                    <p class="mt-1 text-xs text-muted">{{ __('auth.password_hint') }}</p>
                </div>

                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-text">
                        {{ __('auth.password_confirmation') }} <span class="text-accent">*</span>
                    </label>
                    <input
                        type="password" id="password_confirmation" name="password_confirmation" required
                        class="mt-1 block w-full rounded-lg border border-border px-3 py-2 text-sm focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary"
                    >
                </div>
            </div>

            <div>
                <label for="preferred_locale" class="block text-sm font-medium text-text">
                    {{ __('auth.preferred_locale') }}
                </label>
                <select
                    id="preferred_locale" name="preferred_locale"
                    class="mt-1 block w-full rounded-lg border border-border px-3 py-2 text-sm focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary"
                >
                    <option value="fr" @selected(old('preferred_locale', app()->getLocale()) === 'fr')>{{ __('messages.french') }}</option>
                    <option value="ar" @selected(old('preferred_locale', app()->getLocale()) === 'ar')>{{ __('messages.arabic') }}</option>
                </select>
            </div>
        </div>

        <div class="space-y-4 border-t border-border pt-6">
            <div>
                <p class="text-sm font-semibold uppercase tracking-wide text-muted">{{ __('auth.membership_section_title') }}</p>
                <p class="mt-1 text-xs text-muted">{{ __('auth.membership_section_hint') }}</p>
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label class="block text-sm font-medium text-text">{{ __('memberships.field_gender') }} <span class="text-accent">*</span></label>
                    <select name="gender" required class="mt-1 block w-full rounded-lg border border-border px-3 py-2 text-sm focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary">
                        <option value="" disabled @selected(! old('gender'))>{{ __('forms.select_placeholder') }}</option>
                        <option value="male" @selected(old('gender') === 'male')>{{ __('forms.gender_male') }}</option>
                        <option value="female" @selected(old('gender') === 'female')>{{ __('forms.gender_female') }}</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-text">{{ __('memberships.field_nni') }} <span class="text-accent">*</span></label>
                    <input type="text" name="nni" value="{{ old('nni') }}" required
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
                            <option value="{{ $region->id }}" @selected(old('region_id') == $region->id)>
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

            <div class="grid gap-3 sm:grid-cols-3">
                @foreach ([['name' => 'photo', 'label' => 'documents.type_member_photo', 'accept' => 'image/png,image/jpeg'], ['name' => 'identity_card_front', 'label' => 'documents.type_identity_card_front', 'accept' => '.pdf,.jpg,.jpeg,.png'], ['name' => 'identity_card_back', 'label' => 'documents.type_identity_card_back', 'accept' => '.pdf,.jpg,.jpeg,.png']] as $upload)
                    <div>
                        <label class="block text-sm font-medium text-text">{{ __($upload['label']) }} <span class="text-accent">*</span></label>
                        <label
                            for="upload-{{ $upload['name'] }}"
                            data-upload-picker
                            class="mt-1 flex cursor-pointer flex-col items-center justify-center gap-1.5 rounded-xl border-2 border-dashed border-border bg-background px-3 py-5 text-center transition-colors hover:border-primary hover:bg-primary-light/40"
                        >
                            <i class="bi bi-upload text-2xl text-muted" data-upload-icon></i>
                            <span class="text-xs font-medium text-primary" data-upload-text>{{ __('documents.upload_button') }}</span>
                        </label>
                        <input
                            type="file" id="upload-{{ $upload['name'] }}" name="{{ $upload['name'] }}"
                            accept="{{ $upload['accept'] }}" required class="hidden" data-upload-input
                        >
                    </div>
                @endforeach
            </div>
        </div>

        <label class="flex items-start gap-2 text-sm text-muted">
            <input type="checkbox" name="terms" value="1" required class="mt-0.5 rounded border-border text-primary focus:ring-primary">
            {{ __('auth.accept_terms') }}
        </label>

        <button
            type="submit"
            id="register-submit"
            class="flex w-full items-center justify-center gap-2 rounded-lg bg-primary px-4 py-2.5 text-sm font-semibold text-white transition-colors hover:bg-primary-dark disabled:cursor-not-allowed disabled:opacity-70"
        >
            <i class="bi bi-arrow-repeat hidden animate-spin" id="register-submit-spinner"></i>
            <span id="register-submit-label">{{ __('auth.register_button') }}</span>
        </button>
    </form>

    <p class="mt-6 text-center text-sm text-muted">
        {{ __('auth.have_account') }}
        <a href="{{ route('login') }}" class="font-medium text-primary hover:underline">
            {{ __('auth.login_link') }}
        </a>
    </p>
@endsection

@push('scripts')
    <script>
        (function () {
            const regionSelect = document.getElementById('region_id');
            const moughataaSelect = document.getElementById('moughataa_id');
            const initialMoughataaId = {{ old('moughataa_id') ?? 'null' }};

            function loadMoughataas(regionId, selectedMoughataaId) {
                if (! regionId) {
                    moughataaSelect.innerHTML = '<option value="">{{ __('forms.select_placeholder') }}</option>';
                    return;
                }

                fetch(`{{ route('geo.moughataas') }}?region_id=${regionId}`)
                    .then((res) => res.json())
                    .then((data) => {
                        moughataaSelect.innerHTML = '<option value="">{{ __('forms.select_placeholder') }}</option>';
                        data.forEach((item) => {
                            const option = document.createElement('option');
                            option.value = item.id;
                            option.textContent = item.name;
                            if (selectedMoughataaId && Number(selectedMoughataaId) === item.id) {
                                option.selected = true;
                            }
                            moughataaSelect.appendChild(option);
                        });
                    });
            }

            regionSelect.addEventListener('change', () => loadMoughataas(regionSelect.value, null));

            if (regionSelect.value) {
                loadMoughataas(regionSelect.value, initialMoughataaId);
            }

            document.querySelectorAll('[data-upload-input]').forEach((input) => {
                input.addEventListener('change', () => {
                    const label = document.querySelector(`label[for="${input.id}"]`);
                    const icon = label.querySelector('[data-upload-icon]');
                    const text = label.querySelector('[data-upload-text]');

                    if (! input.files.length) {
                        return;
                    }

                    label.classList.remove('border-border');
                    label.classList.add('border-primary', 'bg-primary-light/40');
                    icon.classList.remove('bi-upload', 'text-muted');
                    icon.classList.add('bi-check-circle-fill', 'text-primary');
                    text.textContent = input.files[0].name;
                    text.classList.add('truncate', 'max-w-full');
                });
            });

            // Envoi en arrière-plan (sans recharger la page) : si une erreur
            // survient (NNI déjà pris, champ oublié...), tout ce que le
            // membre a déjà saisi ou sélectionné - y compris les 3 fichiers,
            // qu'un rechargement de page effacerait sinon - reste en place.
            const form = document.getElementById('register-form');
            const errorsBox = document.getElementById('register-errors');
            const submitButton = document.getElementById('register-submit');
            const submitSpinner = document.getElementById('register-submit-spinner');
            const submitLabel = document.getElementById('register-submit-label');

            form.addEventListener('submit', function (event) {
                event.preventDefault();

                submitButton.disabled = true;
                submitSpinner.classList.remove('hidden');

                fetch(form.action, {
                    method: 'POST',
                    body: new FormData(form),
                    headers: { Accept: 'application/json' },
                })
                    .then((response) => {
                        if (response.redirected) {
                            window.location.href = response.url;
                            return;
                        }

                        if (response.status === 422) {
                            return response.json().then((data) => showErrors(Object.values(data.errors || {}).flat()));
                        }

                        return showErrors(['{{ __('messages.unexpected_error') }}']);
                    })
                    .catch(() => showErrors(['{{ __('messages.unexpected_error') }}']))
                    .finally(() => {
                        submitButton.disabled = false;
                        submitSpinner.classList.add('hidden');
                    });
            });

            function showErrors(messages) {
                if (! messages.length) {
                    return;
                }

                const items = messages.map((message) => `<li>${message}</li>`).join('');
                errorsBox.innerHTML = `
                    <div class="mt-4 rounded-lg border border-accent/30 bg-accent/5 px-4 py-3 text-sm text-accent">
                        <ul class="list-inside list-disc space-y-1">${items}</ul>
                    </div>
                `;
                errorsBox.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        })();
    </script>
@endpush
