@extends('layouts.app')

@section('title', __('card.title'))

@section('content')
    @php
        $profile = $membership->user->profile;
    @endphp

    <h1 class="text-xl font-semibold text-text">{{ __('card.title') }}</h1>

    <div class="mt-6 flex flex-col items-center gap-6">
        <div class="w-full max-w-sm overflow-hidden rounded-2xl border-2 border-primary bg-surface shadow-sm">
            <div class="flex items-center gap-3 border-b-2 border-primary p-4">
                <img
                    src="{{ asset(app()->getLocale() === 'ar' ? 'logo_ar.png' : 'logo_fr.png') }}"
                    alt="{{ __('messages.platform_name') }}"
                    class="h-10 w-10 rounded-full border border-border object-cover"
                >
                <div>
                    <p class="text-sm font-bold uppercase leading-tight text-primary">
                        {{ __('messages.platform_name') }}
                    </p>
                </div>
            </div>

            <div class="flex gap-4 p-4">
                <div class="flex h-24 w-20 shrink-0 items-center justify-center overflow-hidden rounded-lg border border-border bg-background">
                    @if ($profile?->photo_url)
                        <img src="{{ $profile->photo_url }}" alt="{{ $profile->full_name }}" class="h-full w-full object-cover">
                    @else
                        <i class="bi bi-person-fill text-4xl text-muted"></i>
                    @endif
                </div>

                <div class="flex-1">
                    <p class="text-lg font-bold uppercase tracking-wide text-primary">{{ __('card.member_label') }}</p>
                    <p class="mt-0.5 text-sm font-semibold text-text">{{ $profile?->full_name ?? $membership->user->name }}</p>

                    <p class="mt-3 text-[10px] font-medium uppercase tracking-wide text-muted">
                        {{ __('card.matricule_label') }}
                    </p>
                    <p class="text-xl font-bold tracking-wider text-primary">{{ $membership->member_number }}</p>
                </div>
            </div>

            <div class="flex items-center justify-between border-t-2 border-primary p-4">
                <img src="{{ $qrDataUri }}" alt="QR" class="h-16 w-16">

                <div class="text-end">
                    <div class="flex h-6 w-24 items-end justify-end border-b border-muted">
                        @if (file_exists(public_path('signature.png')))
                            <img src="{{ asset('signature.png') }}" alt="{{ __('card.signature_label') }}" class="h-8 w-auto max-w-full object-contain">
                        @endif
                    </div>
                    <p class="mt-1 text-[10px] text-muted">{{ __('card.signature_label') }}</p>
                </div>
            </div>
        </div>

        <div class="flex gap-3">
            <a
                href="{{ $downloadUrl }}"
                class="inline-flex items-center gap-2 rounded-lg bg-primary px-4 py-2 text-sm font-semibold text-white hover:bg-primary-dark"
            >
                <i class="bi bi-download"></i>
                {{ __('card.download_pdf') }}
            </a>
        </div>
    </div>
@endsection
