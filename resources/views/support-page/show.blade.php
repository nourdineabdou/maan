@extends('layouts.guest')

@section('card_width', 'max-w-lg')

@section('content')
    <h1 class="text-xl font-semibold text-primary">{{ __('support_page.title') }}</h1>
    <p class="mt-2 text-sm text-muted">{{ __('support_page.intro') }}</p>

    <div class="mt-4 rounded-lg border border-border bg-background px-4 py-3 text-sm text-text">
        {{ __('support_page.logged_in_note') }}
    </div>

    <div class="mt-6">
        <h2 class="text-sm font-semibold text-text">{{ __('support_page.contact_title') }}</h2>
        <p class="mt-1 text-sm">
            <span class="text-muted">{{ __('support_page.email_label') }} :</span>
            <a href="mailto:contact@maanrep.com" class="font-medium text-primary hover:underline">contact@maanrep.com</a>
        </p>
    </div>

    <div class="mt-6">
        <h2 class="text-sm font-semibold text-text">{{ __('support_page.faq_title') }}</h2>

        <div class="mt-3 space-y-4">
            <div>
                <p class="text-sm font-medium text-text">{{ __('support_page.faq_1_q') }}</p>
                <p class="mt-1 text-sm text-muted">{{ __('support_page.faq_1_a') }}</p>
            </div>

            <div>
                <p class="text-sm font-medium text-text">{{ __('support_page.faq_2_q') }}</p>
                <p class="mt-1 text-sm text-muted">{{ __('support_page.faq_2_a') }}</p>
            </div>

            <div>
                <p class="text-sm font-medium text-text">{{ __('support_page.faq_3_q') }}</p>
                <p class="mt-1 text-sm text-muted">
                    {{ __('support_page.faq_3_a') }}
                    <a href="{{ route('account-deletion.create') }}" class="font-medium text-primary hover:underline">
                        {{ __('support_page.faq_3_link') }}
                    </a>
                </p>
            </div>
        </div>
    </div>
@endsection
