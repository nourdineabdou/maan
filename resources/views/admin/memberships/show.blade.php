@extends('layouts.app')

@section('title', $membership->registration_number)

@section('content')
    @php
        $profile = $membership->user->profile;
        $badgeClass = match ($membership->status) {
            'approved' => 'bg-primary-light text-primary',
            'rejected' => 'bg-accent/10 text-accent',
            default => 'bg-secondary/20 text-secondary',
        };
        $na = __('memberships.not_provided');
    @endphp

    <div class="flex flex-wrap items-center justify-between gap-3">
        <div>
            <a href="{{ route('admin.memberships.index') }}" class="text-sm text-muted hover:text-primary">
                <i class="bi bi-arrow-left"></i> {{ __('memberships.list_title') }}
            </a>
            <div class="mt-2 flex items-center gap-3">
                <div class="flex h-14 w-14 shrink-0 items-center justify-center overflow-hidden rounded-full border border-border bg-background">
                    @if ($profile?->photo_url)
                        <a href="{{ $profile->photo_url }}" target="_blank">
                            <img src="{{ $profile->photo_url }}" alt="" class="h-full w-full object-cover">
                        </a>
                    @else
                        <i class="bi bi-person-fill text-2xl text-muted"></i>
                    @endif
                </div>
                <div>
                    <h1 class="text-xl font-semibold text-text">
                        {{ $profile?->full_name ?? $membership->user->name }}
                    </h1>
                    <p class="text-sm text-muted">{{ $membership->registration_number }}</p>
                </div>
            </div>
        </div>

        <div class="flex items-center gap-3">
            <span class="rounded-full px-3 py-1.5 text-sm font-semibold {{ $badgeClass }}">
                {{ __('dashboard.status_'.$membership->status) }}
            </span>

            @if ($membership->status === 'pending')
                @can('memberships.approve')
                    <form
                        method="POST" action="{{ route('admin.memberships.approve', $membership) }}"
                        data-confirm="{{ __('memberships.approve_confirm_text') }}"
                        data-confirm-title="{{ __('memberships.approve_confirm_title') }}"
                        data-confirm-button="{{ __('memberships.approve_confirm_button') }}"
                        data-cancel-button="{{ __('messages.cancel') }}"
                    >
                        @csrf
                        <button type="submit" class="inline-flex items-center gap-2 rounded-lg bg-primary px-4 py-2 text-sm font-semibold text-white hover:bg-primary-dark">
                            <i class="bi bi-check-circle"></i> {{ __('memberships.approve') }}
                        </button>
                    </form>
                @endcan

                @can('memberships.reject')
                    <button
                        type="button" id="reject-btn"
                        class="inline-flex items-center gap-2 rounded-lg border border-accent px-4 py-2 text-sm font-semibold text-accent hover:bg-accent/5"
                    >
                        <i class="bi bi-x-circle"></i> {{ __('memberships.reject') }}
                    </button>
                    <form id="reject-form" method="POST" action="{{ route('admin.memberships.reject', $membership) }}" class="hidden">
                        @csrf
                        <input type="hidden" name="reason" id="reject-reason-input">
                    </form>
                @endcan
            @endif

            @if ($membership->status === 'approved')
                @can('cards.print')
                    <a href="{{ route('admin.members.card', $membership) }}" class="inline-flex items-center gap-2 rounded-lg border border-primary px-4 py-2 text-sm font-semibold text-primary hover:bg-primary-light">
                        <i class="bi bi-postcard"></i> {{ __('members.nav_my_card') }}
                    </a>
                @endcan
            @endif
        </div>
    </div>

    @if ($membership->status === 'rejected' && $membership->rejection_reason)
        <div class="mt-4 rounded-lg border border-accent/30 bg-accent/5 px-4 py-3 text-sm text-accent">
            <strong>{{ __('memberships.rejection_reason') }} :</strong> {{ $membership->rejection_reason }}
        </div>
    @endif

    <div class="mt-6 grid gap-4 lg:grid-cols-3">
        <div class="lg:col-span-2 space-y-4">
            <div class="rounded-2xl border border-border bg-surface p-5">
                <h2 class="text-sm font-semibold uppercase tracking-wide text-muted">{{ __('memberships.section_personal_info') }}</h2>
                <dl class="mt-3 grid grid-cols-2 gap-3 text-sm sm:grid-cols-3">
                    <div><dt class="text-muted">{{ __('memberships.field_nni') }}</dt><dd class="font-medium text-text">{{ $profile?->nni ?? $na }}</dd></div>
                    <div><dt class="text-muted">{{ __('memberships.field_gender') }}</dt><dd class="font-medium text-text">{{ $profile ? __('forms.gender_'.$profile->gender) : $na }}</dd></div>
                    <div><dt class="text-muted">{{ __('memberships.field_birth_date') }}</dt><dd class="font-medium text-text">{{ $profile?->birth_date?->format('d/m/Y') ?? $na }}</dd></div>
                    <div><dt class="text-muted">{{ __('auth.phone') }}</dt><dd class="font-medium text-text">{{ $membership->user->phone ?? $na }}</dd></div>
                    <div><dt class="text-muted">{{ __('auth.email_optional') }}</dt><dd class="font-medium text-text">{{ $membership->user->email ?? $na }}</dd></div>
                </dl>
            </div>

            <div class="rounded-2xl border border-border bg-surface p-5">
                <h2 class="text-sm font-semibold uppercase tracking-wide text-muted">{{ __('memberships.section_geographic_info') }}</h2>
                <dl class="mt-3 grid grid-cols-2 gap-3 text-sm sm:grid-cols-3">
                    <div><dt class="text-muted">{{ __('memberships.field_region') }}</dt><dd class="font-medium text-text">{{ $profile?->region?->getTranslation('name') ?? $na }}</dd></div>
                    <div><dt class="text-muted">{{ __('memberships.field_moughataa') }}</dt><dd class="font-medium text-text">{{ $profile?->moughataaRef?->getTranslation('name') ?? $profile?->moughataa ?? $na }}</dd></div>
                    <div><dt class="text-muted">{{ __('memberships.field_commune') }}</dt><dd class="font-medium text-text">{{ $profile?->communeRef?->getTranslation('name') ?? $profile?->commune ?? $na }}</dd></div>
                    <div><dt class="text-muted">{{ __('memberships.field_locality') }}</dt><dd class="font-medium text-text">{{ $profile?->locality ?? $na }}</dd></div>
                    <div class="col-span-2"><dt class="text-muted">{{ __('memberships.field_address') }}</dt><dd class="font-medium text-text">{{ $profile?->address ?? $na }}</dd></div>
                </dl>
            </div>

            <div class="rounded-2xl border border-border bg-surface p-5">
                <h2 class="text-sm font-semibold uppercase tracking-wide text-muted">{{ __('memberships.section_professional_info') }}</h2>
                <dl class="mt-3 grid grid-cols-2 gap-3 text-sm sm:grid-cols-3">
                    <div><dt class="text-muted">{{ __('memberships.field_employment_status') }}</dt><dd class="font-medium text-text">{{ $profile ? __('forms.employment_status_'.$profile->employment_status) : $na }}</dd></div>
                    <div><dt class="text-muted">{{ __('memberships.field_employment_sector') }}</dt><dd class="font-medium text-text">{{ $profile ? __('forms.employment_sector_'.$profile->employment_sector) : $na }}</dd></div>
                    <div><dt class="text-muted">{{ __('memberships.field_function') }}</dt><dd class="font-medium text-text">{{ $profile?->function ?? $na }}</dd></div>
                    <div><dt class="text-muted">{{ __('memberships.field_position') }}</dt><dd class="font-medium text-text">{{ $profile?->position ?? $na }}</dd></div>
                    <div><dt class="text-muted">{{ __('memberships.field_employer') }}</dt><dd class="font-medium text-text">{{ $profile?->employer ?? $na }}</dd></div>
                </dl>
            </div>

            <div class="rounded-2xl border border-border bg-surface p-5">
                <div class="flex items-center justify-between gap-3">
                    <h2 class="text-sm font-semibold uppercase tracking-wide text-muted">{{ __('memberships.section_documents') }}</h2>
                    @if ($membership->documents->isNotEmpty())
                        <a href="{{ route('admin.memberships.documents.zip', $membership) }}" class="inline-flex items-center gap-1.5 text-sm font-medium text-primary hover:underline">
                            <i class="bi bi-file-earmark-zip"></i> {{ __('documents.download_all_zip') }}
                        </a>
                    @endif
                </div>

                @php
                    $uploadedTypes = $membership->documents->pluck('document_type');
                    $requiredDocTypes = ['identity_card_front', 'identity_card_back', 'member_photo'];
                    $optionalDocTypes = ['diploma', 'cv'];
                @endphp

                <ul class="mt-3 grid gap-1.5 text-sm sm:grid-cols-2">
                    @foreach (array_merge($requiredDocTypes, $optionalDocTypes) as $type)
                        <li class="flex items-center gap-2">
                            @if ($uploadedTypes->contains($type))
                                <i class="bi bi-check-circle-fill text-primary"></i>
                                <span class="text-text">{{ __('documents.type_'.$type) }}</span>
                            @elseif (in_array($type, $requiredDocTypes))
                                <i class="bi bi-exclamation-circle text-accent"></i>
                                <span class="text-accent">{{ __('documents.type_'.$type) }} — {{ __('documents.not_sent') }}</span>
                            @else
                                <i class="bi bi-dash-circle text-muted"></i>
                                <span class="text-muted">{{ __('documents.type_'.$type) }} — {{ __('documents.optional') }}</span>
                            @endif
                        </li>
                    @endforeach
                </ul>

                @if ($membership->documents->isEmpty())
                    <p class="mt-3 text-sm text-muted">{{ __('memberships.no_documents') }}</p>
                @else
                    <ul class="mt-4 space-y-2 border-t border-border pt-4 text-sm">
                        @foreach ($membership->documents as $document)
                            <li class="flex flex-wrap items-center justify-between gap-2 rounded-lg border border-border px-3 py-2">
                                <div>
                                    <p class="font-medium text-text">{{ __('documents.type_'.$document->document_type) }}</p>
                                    <p class="text-xs text-muted">{{ $document->title ?? $document->original_name }} · {{ $document->created_at->format('d/m/Y') }}</p>
                                </div>

                                <div class="flex items-center gap-3">
                                    <span class="rounded-full px-2.5 py-1 text-xs font-semibold {{ $document->is_verified ? 'bg-primary-light text-primary' : 'bg-secondary/20 text-secondary' }}">
                                        {{ $document->is_verified ? __('documents.status_verified') : __('documents.status_pending') }}
                                    </span>

                                    <a href="{{ $document->file_url }}" target="_blank" class="text-sm font-medium text-primary hover:underline">
                                        {{ __('documents.view') }}
                                    </a>

                                    <a href="{{ route('admin.documents.download', $document) }}" class="text-sm font-medium text-primary hover:underline">
                                        {{ __('documents.download') }}
                                    </a>

                                    @if (! $document->is_verified)
                                        @can('documents.verify')
                                            <form method="POST" action="{{ route('admin.documents.verify', $document) }}">
                                                @csrf
                                                <button type="submit" class="text-sm font-medium text-primary hover:underline">
                                                    {{ __('documents.verify') }}
                                                </button>
                                            </form>
                                        @endcan
                                    @endif
                                </div>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>

            <div class="rounded-2xl border border-border bg-surface p-5">
                <h2 class="text-sm font-semibold uppercase tracking-wide text-muted">{{ __('memberships.section_problematics') }}</h2>
                @if ($membership->problematics->isEmpty())
                    <p class="mt-3 text-sm text-muted">{{ __('memberships.no_problematics') }}</p>
                @else
                    <ul class="mt-3 space-y-3 text-sm">
                        @foreach ($membership->problematics as $problematic)
                            @php
                                $priorityClass = match ($problematic->pivot->priority) {
                                    'urgent' => 'bg-accent/10 text-accent',
                                    'high' => 'bg-secondary/20 text-secondary',
                                    default => 'bg-border text-muted',
                                };
                            @endphp
                            <li class="rounded-lg border border-border px-3 py-2">
                                <div class="flex items-center justify-between gap-2">
                                    <p class="font-medium text-text">{{ $problematic->getTranslation('name') }}</p>
                                    @if ($problematic->pivot->priority)
                                        <span class="shrink-0 rounded-full px-2 py-0.5 text-xs font-semibold {{ $priorityClass }}">
                                            {{ __('forms.priority_'.$problematic->pivot->priority) }}
                                        </span>
                                    @endif
                                </div>

                                @if ($problematic->pivot->description)
                                    <p class="mt-1.5 text-xs text-muted">
                                        <span class="font-medium text-text">{{ __('profile.description_label') }} :</span>
                                        {{ $problematic->pivot->description }}
                                    </p>
                                @endif

                                @if ($problematic->pivot->requested_solution)
                                    <p class="mt-1 text-xs text-muted">
                                        <span class="font-medium text-text">{{ __('profile.solution_label') }} :</span>
                                        {{ $problematic->pivot->requested_solution }}
                                    </p>
                                @endif

                                @if ($problematic->pivot->locality)
                                    <p class="mt-1 text-xs text-muted">
                                        <span class="font-medium text-text">{{ __('profile.locality_label') }} :</span>
                                        {{ $problematic->pivot->locality }}
                                    </p>
                                @endif
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>

            <div class="rounded-2xl border border-border bg-surface p-5">
                <h2 class="text-sm font-semibold uppercase tracking-wide text-muted">{{ __('memberships.section_population_need') }}</h2>

                @if ($membership->population_needs)
                    <p class="mt-3 whitespace-pre-line text-sm text-text">{{ $membership->population_needs }}</p>
                @else
                    <p class="mt-3 text-sm text-muted">{{ __('memberships.no_population_need') }}</p>
                @endif

                @php $needDocuments = $membership->documents->where('document_type', 'need_justification'); @endphp

                @if ($needDocuments->isNotEmpty())
                    <ul class="mt-3 space-y-2 text-sm">
                        @foreach ($needDocuments as $document)
                            <li class="flex flex-wrap items-center justify-between gap-2 rounded-lg border border-border px-3 py-2">
                                <span class="text-text">{{ $document->title ?: $document->original_name }}</span>
                                <div class="flex items-center gap-3">
                                    <a href="{{ $document->file_url }}" target="_blank" class="font-medium text-primary hover:underline">
                                        {{ __('documents.view') }}
                                    </a>
                                    <a href="{{ route('admin.documents.download', $document) }}" class="font-medium text-primary hover:underline">
                                        {{ __('documents.download') }}
                                    </a>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>
        </div>

        <div class="space-y-4">
            @can('support_messages.manage')
                <div class="rounded-2xl border border-border bg-surface p-5">
                    <div class="flex items-center justify-between gap-2">
                        <h2 class="text-sm font-semibold uppercase tracking-wide text-muted">{{ __('support.contact_member_title') }}</h2>
                        <button type="button" id="contact-member-toggle" class="text-sm font-medium text-primary hover:underline">
                            <i class="bi bi-chat-dots"></i> {{ __('support.contact_member_button') }}
                        </button>
                    </div>

                    <p class="mt-2 text-xs text-muted">{{ __('support.contact_member_hint') }}</p>

                    <form
                        id="contact-member-form" method="POST"
                        action="{{ route('admin.memberships.contact', $membership) }}"
                        class="mt-3 hidden space-y-3"
                    >
                        @csrf
                        <div>
                            <label class="block text-xs font-medium text-muted">{{ __('support.label_subject') }}</label>
                            <input type="text" name="subject" required maxlength="255"
                                class="mt-1 block w-full rounded-lg border border-border px-3 py-2 text-sm focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-muted">{{ __('support.label_body') }}</label>
                            <textarea name="body" rows="3" required
                                class="mt-1 block w-full rounded-lg border border-border px-3 py-2 text-sm focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary"></textarea>
                        </div>
                        <button type="submit" class="inline-flex items-center gap-2 rounded-lg bg-primary px-4 py-2 text-sm font-semibold text-white hover:bg-primary-dark">
                            <i class="bi bi-send"></i> {{ __('support.send') }}
                        </button>
                    </form>
                </div>
            @endcan

            <div class="rounded-2xl border border-border bg-surface p-5">
                <h2 class="text-sm font-semibold uppercase tracking-wide text-muted">{{ __('memberships.section_history') }}</h2>
                @if ($membership->statusHistories->isEmpty())
                    <p class="mt-3 text-sm text-muted">{{ __('memberships.no_history') }}</p>
                @else
                    <ul class="mt-3 space-y-3 text-sm">
                        @foreach ($membership->statusHistories->sortByDesc('created_at') as $history)
                            <li class="border-s-2 border-primary ps-3">
                                <p class="font-medium text-text">{{ __('dashboard.status_'.$history->new_status) }}</p>
                                <p class="text-xs text-muted">{{ $history->created_at->format('d/m/Y H:i') }}</p>
                                @if ($history->changedBy)
                                    <p class="text-xs text-muted">{{ __('memberships.reviewed_by') }} : {{ $history->changedBy->name }}</p>
                                @endif
                                @if ($history->comment)
                                    <p class="mt-1 text-xs text-text">{{ $history->comment }}</p>
                                @endif
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.getElementById('reject-btn')?.addEventListener('click', function () {
            Swal.fire({
                title: @json(__('memberships.reject_modal_title')),
                input: 'textarea',
                inputPlaceholder: @json(__('memberships.reject_modal_placeholder')),
                showCancelButton: true,
                confirmButtonColor: '#d62828',
                cancelButtonColor: '#6b7280',
                confirmButtonText: @json(__('memberships.reject_modal_confirm')),
                cancelButtonText: @json(__('memberships.reject_modal_cancel')),
                inputValidator: (value) => !value ? @json(__('memberships.reject_modal_required')) : undefined,
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('reject-reason-input').value = result.value;
                    document.getElementById('reject-form').submit();
                }
            });
        });

        document.getElementById('contact-member-toggle')?.addEventListener('click', function () {
            document.getElementById('contact-member-form')?.classList.toggle('hidden');
        });
    </script>
@endpush
