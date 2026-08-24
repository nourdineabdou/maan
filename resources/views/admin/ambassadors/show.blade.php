@extends('layouts.app')

@section('title', $ambassadorRequest->user->name)

@section('content')
    @php
        $profile = $ambassadorRequest->user->profile;
        $badgeClass = match ($ambassadorRequest->status) {
            'approved' => 'bg-primary-light text-primary',
            'rejected' => 'bg-accent/10 text-accent',
            default => 'bg-secondary/20 text-secondary',
        };
        $na = __('ambassadors.not_provided');
    @endphp

    <div class="flex flex-wrap items-center justify-between gap-3">
        <div>
            <a href="{{ route('admin.ambassadors.index') }}" class="text-sm text-muted hover:text-primary">
                <i class="bi bi-arrow-left"></i> {{ __('ambassadors.list_title') }}
            </a>
            <div class="mt-2 flex items-center gap-3">
                <div class="flex h-14 w-14 shrink-0 items-center justify-center overflow-hidden rounded-full border border-border bg-background">
                    @if ($profile?->photo_url)
                        <img src="{{ $profile->photo_url }}" alt="" class="h-full w-full object-cover">
                    @else
                        <i class="bi bi-person-fill text-2xl text-muted"></i>
                    @endif
                </div>
                <div>
                    <h1 class="text-xl font-semibold text-text">
                        {{ $profile?->full_name ?? $ambassadorRequest->user->name }}
                    </h1>
                    <p class="text-sm text-muted">{{ $ambassadorRequest->user->phone ?? $ambassadorRequest->user->email ?? $na }}</p>
                </div>
            </div>
        </div>

        <div class="flex items-center gap-3">
            <span class="rounded-full px-3 py-1.5 text-sm font-semibold {{ $badgeClass }}">
                {{ __('dashboard.status_'.$ambassadorRequest->status) }}
            </span>

            @if ($ambassadorRequest->status === 'pending')
                @can('ambassadors.approve')
                    <form
                        method="POST" action="{{ route('admin.ambassadors.approve', $ambassadorRequest) }}"
                        data-confirm="{{ __('ambassadors.approve_confirm_text') }}"
                        data-confirm-title="{{ __('ambassadors.approve_confirm_title') }}"
                        data-confirm-button="{{ __('ambassadors.approve_confirm_button') }}"
                        data-cancel-button="{{ __('messages.cancel') }}"
                    >
                        @csrf
                        <button type="submit" class="inline-flex items-center gap-2 rounded-lg bg-primary px-4 py-2 text-sm font-semibold text-white hover:bg-primary-dark">
                            <i class="bi bi-check-circle"></i> {{ __('ambassadors.approve') }}
                        </button>
                    </form>
                @endcan
            @endif

            @if (in_array($ambassadorRequest->status, ['pending', 'approved'], true))
                @can('ambassadors.reject')
                    <button
                        type="button" id="reject-btn"
                        class="inline-flex items-center gap-2 rounded-lg border border-accent px-4 py-2 text-sm font-semibold text-accent hover:bg-accent/5"
                    >
                        <i class="bi bi-x-circle"></i> {{ __('ambassadors.reject') }}
                    </button>
                    <form id="reject-form" method="POST" action="{{ route('admin.ambassadors.reject', $ambassadorRequest) }}" class="hidden">
                        @csrf
                        <input type="hidden" name="reason" id="reject-reason-input">
                    </form>
                @endcan
            @endif
        </div>
    </div>

    @if ($ambassadorRequest->status === 'rejected' && $ambassadorRequest->rejection_reason)
        <div class="mt-4 rounded-lg border border-accent/30 bg-accent/5 px-4 py-3 text-sm text-accent">
            <strong>{{ __('ambassadors.rejection_reason') }} :</strong> {{ $ambassadorRequest->rejection_reason }}
        </div>
    @endif

    <div class="mt-6 max-w-2xl rounded-2xl border border-border bg-surface p-5">
        <h2 class="text-sm font-semibold uppercase tracking-wide text-muted">{{ __('memberships.section_geographic_info') }}</h2>
        <dl class="mt-3 grid grid-cols-2 gap-3 text-sm">
            <div><dt class="text-muted">{{ __('memberships.field_region') }}</dt><dd class="font-medium text-text">{{ $profile?->region?->getTranslation('name') ?? $na }}</dd></div>
            <div><dt class="text-muted">{{ __('auth.email_optional') }}</dt><dd class="font-medium text-text">{{ $ambassadorRequest->user->email ?? $na }}</dd></div>
        </dl>

        @if ($ambassadorRequest->reviewer)
            <p class="mt-4 text-xs text-muted">{{ __('ambassadors.reviewed_by') }} : {{ $ambassadorRequest->reviewer->name }}</p>
        @endif
    </div>
@endsection

@push('scripts')
    <script>
        document.getElementById('reject-btn')?.addEventListener('click', function () {
            Swal.fire({
                title: @json(__('ambassadors.reject_modal_title')),
                input: 'textarea',
                inputPlaceholder: @json(__('ambassadors.reject_modal_placeholder')),
                showCancelButton: true,
                confirmButtonColor: '#d62828',
                cancelButtonColor: '#6b7280',
                confirmButtonText: @json(__('ambassadors.reject_modal_confirm')),
                cancelButtonText: @json(__('ambassadors.reject_modal_cancel')),
                inputValidator: (value) => !value ? @json(__('ambassadors.reject_modal_required')) : undefined,
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('reject-reason-input').value = result.value;
                    document.getElementById('reject-form').submit();
                }
            });
        });
    </script>
@endpush
