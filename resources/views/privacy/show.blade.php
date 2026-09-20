@extends('layouts.guest')

@section('card_width', 'max-w-2xl')

@section('content')
    <h1 class="text-xl font-semibold text-primary">{{ __('privacy.title') }}</h1>
    <p class="mt-1 text-xs text-muted">{{ __('privacy.updated_at', ['date' => now()->translatedFormat('d/m/Y')]) }}</p>

    <div class="mt-6 space-y-6 text-sm text-text">
        <section>
            <h2 class="font-semibold text-text">{{ __('privacy.intro_title') }}</h2>
            <p class="mt-1 text-muted">{{ __('privacy.intro_text') }}</p>
        </section>

        <section>
            <h2 class="font-semibold text-text">{{ __('privacy.data_title') }}</h2>
            <p class="mt-1 text-muted">{{ __('privacy.data_intro') }}</p>
            <ul class="mt-2 list-inside list-disc space-y-1 text-muted">
                <li>{{ __('privacy.data_identity') }}</li>
                <li>{{ __('privacy.data_contact') }}</li>
                <li>{{ __('privacy.data_professional') }}</li>
                <li>{{ __('privacy.data_geo') }}</li>
                <li>{{ __('privacy.data_documents') }}</li>
                <li>{{ __('privacy.data_membership') }}</li>
                <li>{{ __('privacy.data_technical') }}</li>
            </ul>
        </section>

        <section>
            <h2 class="font-semibold text-text">{{ __('privacy.purpose_title') }}</h2>
            <p class="mt-1 text-muted">{{ __('privacy.purpose_text') }}</p>
        </section>

        <section>
            <h2 class="font-semibold text-text">{{ __('privacy.sharing_title') }}</h2>
            <p class="mt-1 text-muted">{{ __('privacy.sharing_text') }}</p>
            <ul class="mt-2 list-inside list-disc space-y-1 text-muted">
                <li>{{ __('privacy.sharing_push') }}</li>
                <li>{{ __('privacy.sharing_hosting') }}</li>
            </ul>
        </section>

        <section>
            <h2 class="font-semibold text-text">{{ __('privacy.retention_title') }}</h2>
            <p class="mt-1 text-muted">{{ __('privacy.retention_text') }}</p>
        </section>

        <section>
            <h2 class="font-semibold text-text">{{ __('privacy.rights_title') }}</h2>
            <p class="mt-1 text-muted">
                {{ __('privacy.rights_text') }}
                <a href="{{ route('account-deletion.create') }}" class="font-medium text-primary hover:underline">
                    {{ __('privacy.rights_link') }}
                </a>
            </p>
            <p class="mt-2 text-muted">
                {{ __('privacy.rights_contact_text') }}
                <a href="mailto:contact@maanrep.com" class="font-medium text-primary hover:underline">contact@maanrep.com</a>
            </p>
        </section>

        <section>
            <h2 class="font-semibold text-text">{{ __('privacy.security_title') }}</h2>
            <p class="mt-1 text-muted">{{ __('privacy.security_text') }}</p>
        </section>

        <section>
            <h2 class="font-semibold text-text">{{ __('privacy.minors_title') }}</h2>
            <p class="mt-1 text-muted">{{ __('privacy.minors_text') }}</p>
        </section>

        <section>
            <h2 class="font-semibold text-text">{{ __('privacy.changes_title') }}</h2>
            <p class="mt-1 text-muted">{{ __('privacy.changes_text') }}</p>
        </section>

        <section>
            <h2 class="font-semibold text-text">{{ __('privacy.contact_title') }}</h2>
            <p class="mt-1 text-muted">
                {{ __('privacy.contact_text') }}
                <a href="mailto:contact@maanrep.com" class="font-medium text-primary hover:underline">contact@maanrep.com</a>
            </p>
        </section>
    </div>
@endsection
