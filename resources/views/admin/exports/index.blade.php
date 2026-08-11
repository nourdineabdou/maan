@extends('layouts.app')

@section('title', __('exports.title'))

@section('content')
    <h1 class="text-xl font-semibold text-text">{{ __('exports.title') }}</h1>
    <p class="mt-1 text-sm text-muted">{{ __('exports.subtitle') }}</p>

    <form method="GET" id="export-form" class="mt-6 max-w-xl space-y-4 rounded-2xl border border-border bg-surface p-6">
        <div>
            <label class="block text-xs font-medium text-muted">{{ __('memberships.filter_status') }}</label>
            <select name="status" class="mt-1 block w-full rounded-lg border border-border px-3 py-2 text-sm focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary">
                <option value="">{{ __('memberships.filter_all_statuses') }}</option>
                @foreach (['pending', 'approved', 'rejected'] as $statusOption)
                    <option value="{{ $statusOption }}">{{ __('dashboard.status_'.$statusOption) }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="block text-xs font-medium text-muted">{{ __('memberships.filter_region') }}</label>
            <select name="region" class="mt-1 block w-full rounded-lg border border-border px-3 py-2 text-sm focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary">
                <option value="">{{ __('memberships.filter_all_regions') }}</option>
                @foreach ($regions as $region)
                    <option value="{{ $region->id }}">{{ $region->getTranslation('name') }}</option>
                @endforeach
            </select>
        </div>

        <div class="flex flex-wrap gap-3">
            <button type="submit" formaction="{{ route('admin.exports.excel') }}" class="inline-flex items-center gap-2 rounded-lg bg-primary px-4 py-2 text-sm font-semibold text-white hover:bg-primary-dark">
                <i class="bi bi-file-earmark-excel"></i> {{ __('exports.export_excel') }}
            </button>
            <button type="submit" formaction="{{ route('admin.exports.pdf') }}" class="inline-flex items-center gap-2 rounded-lg border border-primary px-4 py-2 text-sm font-semibold text-primary hover:bg-primary-light">
                <i class="bi bi-file-earmark-pdf"></i> {{ __('exports.export_pdf') }}
            </button>
        </div>
    </form>
@endsection
