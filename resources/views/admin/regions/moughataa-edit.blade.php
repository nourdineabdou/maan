@extends('layouts.app')

@section('title', __('regions.edit_moughataa_title'))

@section('content')
    <a href="{{ route('admin.regions.moughataas', $moughataa->region) }}" class="text-sm text-muted hover:text-primary">
        <i class="bi bi-arrow-left"></i> {{ __('regions.back_to_moughataas') }}
    </a>

    <h1 class="mt-1 text-xl font-semibold text-text">{{ __('regions.edit_moughataa_title') }} — {{ $moughataa->code }}</h1>

    <form method="POST" action="{{ route('admin.moughataas.update', $moughataa) }}" class="mt-6 max-w-md space-y-4 rounded-2xl border border-border bg-surface p-6">
        @csrf
        @method('PUT')

        <div>
            <label class="block text-sm font-medium text-text">{{ __('regions.label_name_fr') }}</label>
            <input
                type="text" name="name_fr" value="{{ old('name_fr', $moughataa->getTranslation('name', 'fr')) }}" required
                class="mt-1 block w-full rounded-lg border border-border px-3 py-2 text-sm focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary"
            >
        </div>

        <div>
            <label class="block text-sm font-medium text-text">{{ __('regions.label_name_ar') }}</label>
            <input
                type="text" name="name_ar" dir="rtl" value="{{ old('name_ar', $moughataa->getTranslation('name', 'ar')) }}" required
                class="mt-1 block w-full rounded-lg border border-border px-3 py-2 text-sm focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary"
            >
        </div>

        <label class="flex items-center gap-2 text-sm text-text">
            <input type="checkbox" name="is_active" value="1" @checked($moughataa->is_active) class="rounded border-border text-primary focus:ring-primary">
            {{ __('regions.label_active') }}
        </label>

        <button type="submit" class="rounded-lg bg-primary px-4 py-2 text-sm font-semibold text-white hover:bg-primary-dark">
            {{ __('regions.save') }}
        </button>
    </form>
@endsection
