@extends('layouts.app')

@section('title', $region->getTranslation('name'))

@section('content')
    <a href="{{ route('admin.regions.index') }}" class="text-sm text-muted hover:text-primary">
        <i class="bi bi-arrow-left"></i> {{ __('regions.back_to_regions') }}
    </a>

    <h1 class="mt-1 text-xl font-semibold text-text">{{ $region->getTranslation('name') }}</h1>

    {{-- Table (desktop) --}}
    <div class="mt-6 hidden overflow-x-auto rounded-2xl border border-border bg-surface lg:block">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-border text-start text-xs uppercase text-muted">
                    <th class="px-4 py-3 text-start">{{ __('regions.column_code') }}</th>
                    <th class="px-4 py-3 text-start">{{ __('regions.column_name_fr') }}</th>
                    <th class="px-4 py-3 text-start">{{ __('regions.column_name_ar') }}</th>
                    <th class="px-4 py-3 text-start">{{ __('regions.column_communes_count') }}</th>
                    <th class="px-4 py-3 text-start">{{ __('regions.column_active') }}</th>
                    <th class="px-4 py-3 text-start">{{ __('regions.column_actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($moughataas as $moughataa)
                    <tr class="border-b border-border last:border-0">
                        <td class="px-4 py-3 font-medium text-text">{{ $moughataa->code }}</td>
                        <td class="px-4 py-3 text-text">{{ $moughataa->getTranslation('name', 'fr') }}</td>
                        <td class="px-4 py-3 text-text" dir="rtl">{{ $moughataa->getTranslation('name', 'ar') }}</td>
                        <td class="px-4 py-3 text-muted">{{ $moughataa->communes_count }}</td>
                        <td class="px-4 py-3">
                            <span class="rounded-full px-2.5 py-1 text-xs font-semibold {{ $moughataa->is_active ? 'bg-primary-light text-primary' : 'bg-border text-muted' }}">
                                {{ $moughataa->is_active ? __('messages.yes') : __('messages.no') }}
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-3">
                                <a href="{{ route('admin.moughataas.communes', $moughataa) }}" class="font-medium text-primary hover:underline">
                                    {{ __('regions.view_communes') }}
                                </a>
                                <a href="{{ route('admin.moughataas.edit', $moughataa) }}" class="font-medium text-muted hover:text-primary hover:underline">
                                    {{ __('regions.edit') }}
                                </a>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- Cartes (mobile) --}}
    <div class="mt-6 space-y-3 lg:hidden">
        @foreach ($moughataas as $moughataa)
            <div class="rounded-2xl border border-border bg-surface p-4 shadow-sm">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <p class="font-semibold text-text">{{ $moughataa->getTranslation('name', 'fr') }}</p>
                        <p class="text-sm text-muted" dir="rtl">{{ $moughataa->getTranslation('name', 'ar') }}</p>
                    </div>
                    <span class="shrink-0 rounded-full px-2.5 py-1 text-xs font-semibold {{ $moughataa->is_active ? 'bg-primary-light text-primary' : 'bg-border text-muted' }}">
                        {{ $moughataa->is_active ? __('messages.yes') : __('messages.no') }}
                    </span>
                </div>
                <p class="mt-1 text-xs text-muted">{{ $moughataa->code }} · {{ $moughataa->communes_count }} {{ __('regions.column_communes_count') }}</p>
                <div class="mt-3 flex items-center gap-4 border-t border-border pt-3">
                    <a href="{{ route('admin.moughataas.communes', $moughataa) }}" class="text-sm font-medium text-primary hover:underline">
                        {{ __('regions.view_communes') }}
                    </a>
                    <a href="{{ route('admin.moughataas.edit', $moughataa) }}" class="text-sm font-medium text-muted hover:text-primary hover:underline">
                        {{ __('regions.edit') }}
                    </a>
                </div>
            </div>
        @endforeach
    </div>
@endsection
