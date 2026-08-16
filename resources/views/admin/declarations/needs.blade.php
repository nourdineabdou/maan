@extends('layouts.app')

@section('title', __('memberships.declared_needs_title'))

@section('content')
    <h1 class="text-xl font-semibold text-text">{{ __('memberships.declared_needs_title') }}</h1>

    <form method="GET" class="mt-4 flex flex-wrap items-end gap-3 rounded-2xl border border-border bg-surface p-4">
        <div class="min-w-[160px]">
            <label class="block text-xs font-medium text-muted">{{ __('memberships.filter_declaration_status') }}</label>
            <select name="status" class="mt-1 block w-full rounded-lg border border-border px-3 py-2 text-sm focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary">
                <option value="">{{ __('memberships.filter_all_declaration_statuses') }}</option>
                @foreach (['submitted', 'in_progress', 'resolved'] as $statusOption)
                    <option value="{{ $statusOption }}" @selected($filters['status'] === $statusOption)>{{ __('forms.status_'.$statusOption) }}</option>
                @endforeach
            </select>
        </div>

        <div class="min-w-[150px]">
            <label class="block text-xs font-medium text-muted">{{ __('memberships.filter_date_from') }}</label>
            <input type="date" name="date_from" value="{{ $filters['date_from'] }}"
                class="mt-1 block w-full rounded-lg border border-border px-3 py-2 text-sm focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary">
        </div>

        <div class="min-w-[150px]">
            <label class="block text-xs font-medium text-muted">{{ __('memberships.filter_date_to') }}</label>
            <input type="date" name="date_to" value="{{ $filters['date_to'] }}"
                class="mt-1 block w-full rounded-lg border border-border px-3 py-2 text-sm focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary">
        </div>

        <button type="submit" class="rounded-lg bg-primary px-4 py-2 text-sm font-semibold text-white hover:bg-primary-dark">
            {{ __('memberships.apply_filters') }}
        </button>
    </form>

    @if ($needs->isEmpty())
        <div class="mt-6 rounded-2xl border border-dashed border-border bg-surface p-10 text-center text-sm text-muted">
            {{ __('memberships.no_declared_needs') }}
        </div>
    @else
        {{-- Table (desktop) --}}
        <div class="mt-6 hidden overflow-x-auto rounded-2xl border border-border bg-surface lg:block">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-border text-start text-xs uppercase text-muted">
                        <th class="px-4 py-3 text-start">{{ __('memberships.column_member') }}</th>
                        <th class="px-4 py-3 text-start">{{ __('memberships.column_description') }}</th>
                        <th class="px-4 py-3 text-start">{{ __('memberships.column_date') }}</th>
                        <th class="px-4 py-3 text-start">{{ __('memberships.column_status') }}</th>
                        <th class="px-4 py-3 text-start">{{ __('memberships.column_actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($needs as $need)
                        @php $profile = $need->membership->user->profile; @endphp
                        <tr class="border-b border-border last:border-0">
                            <td class="px-4 py-3">
                                <a href="{{ route('admin.memberships.show', $need->membership) }}" class="font-medium text-primary hover:underline">
                                    {{ $profile?->full_name ?? $need->membership->user->name }}
                                </a>
                            </td>
                            <td class="px-4 py-3 max-w-md text-text">{{ str($need->description)->limit(120) }}</td>
                            <td class="px-4 py-3 text-muted">{{ $need->created_at->format('d/m/Y') }}</td>
                            <td class="px-4 py-3">
                                @can('problematics.manage')
                                    <form method="POST" action="{{ route('admin.memberships.needs.status', [$need->membership, $need]) }}">
                                        @csrf
                                        <select name="status" onchange="this.form.submit()" class="rounded-lg border border-border px-2 py-1 text-xs focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary">
                                            @foreach (['submitted', 'in_progress', 'resolved'] as $statusOption)
                                                <option value="{{ $statusOption }}" @selected($need->status === $statusOption)>{{ __('forms.status_'.$statusOption) }}</option>
                                            @endforeach
                                        </select>
                                    </form>
                                @endcan
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex flex-wrap items-center gap-3">
                                    <a href="{{ route('admin.memberships.show', $need->membership) }}" class="font-medium text-primary hover:underline">
                                        {{ __('memberships.view_details') }}
                                    </a>
                                    @can('support_messages.manage')
                                        <button
                                            type="button" class="declaration-message-btn font-medium text-primary hover:underline"
                                            data-contact-url="{{ route('admin.memberships.contact', $need->membership) }}"
                                            data-related-type="need" data-related-id="{{ $need->id }}"
                                            data-related-label="{{ str($need->description)->limit(60) }}"
                                        >
                                            {{ __('support.contact_about_button') }}
                                        </button>
                                    @endcan
                                    @can('documents.view')
                                        @if ($need->documents->isNotEmpty())
                                            <a href="{{ route('admin.memberships.needs.documents.zip', [$need->membership, $need]) }}" class="font-medium text-primary hover:underline">
                                                <i class="bi bi-file-earmark-zip"></i> {{ __('documents.download_all_zip') }}
                                            </a>
                                        @endif
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Cartes (mobile) --}}
        <div class="mt-6 space-y-3 lg:hidden">
            @foreach ($needs as $need)
                @php $profile = $need->membership->user->profile; @endphp
                <div class="rounded-2xl border border-border bg-surface p-4 shadow-sm">
                    <a href="{{ route('admin.memberships.show', $need->membership) }}" class="font-semibold text-primary hover:underline">
                        {{ $profile?->full_name ?? $need->membership->user->name }}
                    </a>
                    <p class="mt-1 text-sm text-text">{{ str($need->description)->limit(120) }}</p>
                    <p class="mt-1 text-xs text-muted">{{ $need->created_at->format('d/m/Y') }}</p>
                    <div class="mt-2 flex flex-wrap items-center gap-3">
                        @can('problematics.manage')
                            <form method="POST" action="{{ route('admin.memberships.needs.status', [$need->membership, $need]) }}">
                                @csrf
                                <select name="status" onchange="this.form.submit()" class="rounded-lg border border-border px-2 py-1 text-xs focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary">
                                    @foreach (['submitted', 'in_progress', 'resolved'] as $statusOption)
                                        <option value="{{ $statusOption }}" @selected($need->status === $statusOption)>{{ __('forms.status_'.$statusOption) }}</option>
                                    @endforeach
                                </select>
                            </form>
                        @endcan
                        @can('support_messages.manage')
                            <button
                                type="button" class="declaration-message-btn text-xs font-medium text-primary hover:underline"
                                data-contact-url="{{ route('admin.memberships.contact', $need->membership) }}"
                                data-related-type="need" data-related-id="{{ $need->id }}"
                                data-related-label="{{ str($need->description)->limit(60) }}"
                            >
                                {{ __('support.contact_about_button') }}
                            </button>
                        @endcan
                        @can('documents.view')
                            @if ($need->documents->isNotEmpty())
                                <a href="{{ route('admin.memberships.needs.documents.zip', [$need->membership, $need]) }}" class="text-xs font-medium text-primary hover:underline">
                                    <i class="bi bi-file-earmark-zip"></i> {{ __('documents.download_all_zip') }}
                                </a>
                            @endif
                        @endcan
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-6">
            {{ $needs->links() }}
        </div>
    @endif

    @can('support_messages.manage')
        <form id="declaration-message-form" method="POST" action="">
            @csrf
            <input type="hidden" name="related_type">
            <input type="hidden" name="related_id">
            <input type="hidden" name="subject">
            <input type="hidden" name="body">
        </form>
    @endcan
@endsection

@push('scripts')
    <script>
        document.querySelectorAll('.declaration-message-btn').forEach((button) => {
            button.addEventListener('click', () => {
                Swal.fire({
                    title: @json(__('support.new_message')),
                    html:
                        '<p style="text-align:start;font-size:12px;color:#6b7280;margin-bottom:8px;">'
                        + @json(__('support.related_to_label')) + ' ' + button.dataset.relatedLabel + '</p>'
                        + '<input id="swal-subject" class="swal2-input" placeholder="' + @json(__('support.label_subject')) + '">'
                        + '<textarea id="swal-body" class="swal2-textarea" placeholder="' + @json(__('support.label_body')) + '"></textarea>',
                    focusConfirm: false,
                    showCancelButton: true,
                    confirmButtonText: @json(__('support.send')),
                    cancelButtonText: @json(__('messages.cancel')),
                    confirmButtonColor: '#1b5e3a',
                    preConfirm: () => {
                        const subject = document.getElementById('swal-subject').value.trim();
                        const body = document.getElementById('swal-body').value.trim();

                        if (!subject || !body) {
                            Swal.showValidationMessage(@json(__('forms.required_field')));
                            return false;
                        }

                        return { subject, body };
                    },
                }).then((result) => {
                    if (!result.isConfirmed) {
                        return;
                    }

                    const form = document.getElementById('declaration-message-form');
                    form.action = button.dataset.contactUrl;
                    form.querySelector('[name=related_type]').value = button.dataset.relatedType;
                    form.querySelector('[name=related_id]').value = button.dataset.relatedId;
                    form.querySelector('[name=subject]').value = result.value.subject;
                    form.querySelector('[name=body]').value = result.value.body;
                    form.submit();
                });
            });
        });
    </script>
@endpush
