@extends('layouts.app')

@section('title', __('profile.overview_title'))

@section('content')
    <h1 class="text-xl font-semibold text-text">{{ __('profile.overview_title') }}</h1>
    <p class="mt-1 text-sm text-muted">{{ __('profile.overview_subtitle') }}</p>

    @if ($errors->any())
        <div class="mt-4 rounded-lg border border-accent/30 bg-accent/5 px-4 py-3 text-sm text-accent">
            <ul class="list-inside list-disc space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="mt-6 grid gap-4 md:grid-cols-2">
        <div class="rounded-2xl border border-border bg-surface p-5">
            <div class="flex items-center gap-4">
                <form method="POST" action="{{ route('profile.photo.update') }}" enctype="multipart/form-data" class="relative shrink-0">
                    @csrf
                    <div class="flex h-16 w-16 items-center justify-center overflow-hidden rounded-full border border-border bg-background">
                        @if ($profile?->photo_url)
                            <img src="{{ $profile->photo_url }}" alt="{{ $profile->full_name }}" class="h-full w-full object-cover">
                        @else
                            <i class="bi bi-person-fill text-3xl text-muted"></i>
                        @endif
                    </div>
                    <label
                        for="photo-input"
                        title="{{ __('profile.change_photo') }}"
                        class="absolute -bottom-1 -end-1 flex h-6 w-6 cursor-pointer items-center justify-center rounded-full bg-primary text-white shadow-sm hover:bg-primary-dark"
                    >
                        <i class="bi bi-camera-fill text-xs"></i>
                    </label>
                    <input
                        type="file" name="photo" id="photo-input" accept="image/png,image/jpeg" class="hidden"
                        onchange="this.form.submit()"
                    >
                </form>
                <div>
                    <p class="font-semibold text-text">{{ $profile?->full_name ?? $user->name }}</p>
                    <p class="text-sm text-muted">{{ $user->phone }}</p>
                    <p class="text-xs text-muted">{{ __('profile.change_photo') }}</p>
                </div>
            </div>

            <dl class="mt-4 space-y-2 text-sm">
                <div class="flex justify-between border-b border-border pb-2">
                    <dt class="text-muted">{{ __('memberships.field_nni') }}</dt>
                    <dd class="text-text">{{ $profile?->nni ?? __('memberships.not_provided') }}</dd>
                </div>
                <div class="flex justify-between border-b border-border pb-2">
                    <dt class="text-muted">{{ __('memberships.field_region') }}</dt>
                    <dd class="text-text">{{ $profile?->region?->getTranslation('name') ?? __('memberships.not_provided') }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-muted">{{ __('memberships.field_employment_status') }}</dt>
                    <dd class="text-text">{{ $profile ? __('forms.employment_status_'.$profile->employment_status) : __('memberships.not_provided') }}</dd>
                </div>
            </dl>

            <a href="{{ route('profile.personal.edit') }}" class="mt-4 inline-flex items-center gap-2 text-sm font-medium text-primary hover:underline">
                <i class="bi bi-pencil"></i> {{ __('profile.edit') }}
            </a>
        </div>

        <div class="rounded-2xl border border-border bg-surface p-5">
            <p class="text-sm font-semibold uppercase tracking-wide text-muted">{{ __('profile.card_membership') }}</p>

            @php
                $badgeClass = match (true) {
                    $membership->isDraft() => 'bg-border text-muted',
                    $membership->status === 'approved' => 'bg-primary-light text-primary',
                    $membership->status === 'rejected' => 'bg-accent/10 text-accent',
                    default => 'bg-secondary/20 text-secondary',
                };
            @endphp

            <span class="mt-2 inline-block rounded-full px-3 py-1 text-sm font-semibold {{ $badgeClass }}">
                {{ $membership->isDraft() ? __('memberships.status_draft') : __('dashboard.status_'.$membership->status) }}
            </span>

            @if ($membership->member_number)
                <p class="mt-2 text-sm text-text">{{ __('dashboard.member_registration_number') }} : <strong>{{ $membership->member_number }}</strong></p>
            @endif

            <a href="{{ route('profile.membership') }}" class="mt-4 inline-flex items-center gap-2 text-sm font-medium text-primary hover:underline">
                <i class="bi bi-arrow-right-circle"></i> {{ __('profile.membership_title') }}
            </a>
        </div>

        <div class="rounded-2xl border border-border bg-surface p-5">
            <p class="text-sm font-semibold uppercase tracking-wide text-muted">{{ __('profile.card_problematics') }}</p>
            <p class="mt-2 text-2xl font-semibold text-primary">{{ $membership->problematics->count() }}</p>
            <a href="{{ route('profile.problematics.edit') }}" class="mt-4 inline-flex items-center gap-2 text-sm font-medium text-primary hover:underline">
                <i class="bi bi-pencil"></i> {{ __('profile.edit') }}
            </a>
        </div>

        <div class="rounded-2xl border border-border bg-surface p-5">
            <p class="text-sm font-semibold uppercase tracking-wide text-muted">{{ __('profile.card_population_need') }}</p>
            <p class="mt-2 text-sm text-text">{{ $membership->needs->isNotEmpty() ? __('memberships.filled') : __('memberships.not_provided') }}</p>
            <a href="{{ route('profile.need.edit') }}" class="mt-4 inline-flex items-center gap-2 text-sm font-medium text-primary hover:underline">
                <i class="bi bi-pencil"></i> {{ __('profile.edit') }}
            </a>
        </div>

        <div class="rounded-2xl border border-border bg-surface p-5">
            <p class="text-sm font-semibold uppercase tracking-wide text-muted">{{ __('profile.card_documents') }}</p>
            <p class="mt-2 text-2xl font-semibold text-primary">{{ $membership->documents->count() }}</p>
            <a href="{{ route('profile.documents.index') }}" class="mt-4 inline-flex items-center gap-2 text-sm font-medium text-primary hover:underline">
                <i class="bi bi-pencil"></i> {{ __('profile.edit') }}
            </a>
        </div>

        <div class="rounded-2xl border border-border bg-surface p-5">
            <p class="text-sm font-semibold uppercase tracking-wide text-muted">{{ __('profile.card_professional') }}</p>
            <p class="mt-2 text-sm text-text">{{ $profile?->function ?? __('memberships.not_provided') }}</p>
            <a href="{{ route('profile.professional.edit') }}" class="mt-4 inline-flex items-center gap-2 text-sm font-medium text-primary hover:underline">
                <i class="bi bi-pencil"></i> {{ __('profile.edit') }}
            </a>
        </div>
    </div>
@endsection
