@extends('layouts.app')

@section('title', __('documents.title'))

@section('content')
    <h1 class="text-xl font-semibold text-text">{{ __('documents.title') }}</h1>

    <form method="GET" class="mt-4 flex flex-wrap items-end gap-3 rounded-2xl border border-border bg-surface p-4">
        <div class="min-w-[180px]">
            <label class="block text-xs font-medium text-muted">{{ __('documents.filter_type') }}</label>
            <select name="type" class="mt-1 block w-full rounded-lg border border-border px-3 py-2 text-sm focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary">
                <option value="">{{ __('documents.filter_all_types') }}</option>
                @foreach (['identity_card_front', 'identity_card_back', 'diploma', 'member_photo', 'cv', 'problematic_justification', 'need_justification', 'other'] as $type)
                    <option value="{{ $type }}" @selected($filters['type'] === $type)>{{ __('documents.type_'.$type) }}</option>
                @endforeach
            </select>
        </div>

        <div class="min-w-[160px]">
            <label class="block text-xs font-medium text-muted">{{ __('documents.filter_verified') }}</label>
            <select name="verified" class="mt-1 block w-full rounded-lg border border-border px-3 py-2 text-sm focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary">
                <option value="">{{ __('documents.filter_all') }}</option>
                <option value="1" @selected($filters['verified'] === '1')>{{ __('documents.filter_verified_yes') }}</option>
                <option value="0" @selected($filters['verified'] === '0')>{{ __('documents.filter_verified_no') }}</option>
            </select>
        </div>

        <button type="submit" class="rounded-lg bg-primary px-4 py-2 text-sm font-semibold text-white hover:bg-primary-dark">
            {{ __('memberships.apply_filters') }}
        </button>
    </form>

    @if ($memberships->isEmpty())
        <div class="mt-6 rounded-2xl border border-dashed border-border bg-surface p-10 text-center text-sm text-muted">
            {{ __('documents.no_results') }}
        </div>
    @else
        <div class="mt-6 space-y-4">
            @foreach ($memberships as $membership)
                @php $profile = $membership->user->profile; @endphp
                <div class="rounded-2xl border border-border bg-surface">
                    <div class="flex items-center justify-between gap-3 border-b border-border px-4 py-3">
                        <div>
                            <p class="font-semibold text-text">{{ $profile?->full_name ?? $membership->user->name }}</p>
                            <p class="text-xs text-muted">{{ $membership->user->phone }} · {{ $membership->registration_number }}</p>
                        </div>
                        <div class="flex shrink-0 items-center gap-3">
                            <a href="{{ route('admin.memberships.documents.zip', $membership) }}" class="inline-flex items-center gap-1.5 text-sm font-medium text-primary hover:underline">
                                <i class="bi bi-file-earmark-zip"></i> {{ __('documents.download_all_zip') }}
                            </a>
                            <a href="{{ route('admin.memberships.show', $membership) }}" class="text-sm font-medium text-primary hover:underline">
                                {{ __('memberships.view_details') }}
                            </a>
                        </div>
                    </div>

                    <ul class="divide-y divide-border">
                        @foreach ($membership->documents as $document)
                            <li class="flex flex-wrap items-center justify-between gap-3 px-4 py-3">
                                <div>
                                    <p class="text-sm font-medium text-text">{{ __('documents.type_'.$document->document_type) }}</p>
                                    <p class="text-xs text-muted">{{ $document->original_name }} · {{ $document->created_at->format('d/m/Y') }}</p>
                                </div>

                                <div class="flex items-center gap-3">
                                    <span class="rounded-full px-2.5 py-1 text-xs font-semibold {{ $document->is_verified ? 'bg-primary-light text-primary' : 'bg-secondary/20 text-secondary' }}">
                                        {{ $document->is_verified ? __('documents.status_verified') : __('documents.status_pending') }}
                                    </span>

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
                </div>
            @endforeach
        </div>

        <div class="mt-6">
            {{ $memberships->links() }}
        </div>
    @endif
@endsection
