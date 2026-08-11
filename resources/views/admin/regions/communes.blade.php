@extends('layouts.app')

@section('title', $moughataa->getTranslation('name'))

@section('content')
    <a href="{{ route('admin.regions.moughataas', $moughataa->region) }}" class="text-sm text-muted hover:text-primary">
        <i class="bi bi-arrow-left"></i> {{ __('regions.back_to_moughataas') }}
    </a>

    <h1 class="mt-1 text-xl font-semibold text-text">{{ $moughataa->getTranslation('name') }}</h1>

    @if ($communes->isEmpty())
        <div class="mt-6 rounded-2xl border border-dashed border-border bg-surface p-8 text-center text-sm text-muted">
            {{ __('regions.no_communes') }}
        </div>
    @else
        {{-- Table (desktop) --}}
        <div class="mt-6 hidden overflow-x-auto rounded-2xl border border-border bg-surface lg:block">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-border text-start text-xs uppercase text-muted">
                        <th class="px-4 py-3 text-start">{{ __('regions.column_code') }}</th>
                        <th class="px-4 py-3 text-start">{{ __('regions.column_name_fr') }}</th>
                        <th class="px-4 py-3 text-start">{{ __('regions.column_name_ar') }}</th>
                        <th class="px-4 py-3 text-start">{{ __('regions.column_active') }}</th>
                        <th class="px-4 py-3 text-start">{{ __('regions.column_actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($communes as $commune)
                        <tr class="border-b border-border last:border-0">
                            <td class="px-4 py-3 font-medium text-text">{{ $commune->code }}</td>
                            <td class="px-4 py-3 text-text">{{ $commune->getTranslation('name', 'fr') }}</td>
                            <td class="px-4 py-3 text-text" dir="rtl">{{ $commune->getTranslation('name', 'ar') }}</td>
                            <td class="px-4 py-3">
                                <span class="rounded-full px-2.5 py-1 text-xs font-semibold {{ $commune->is_active ? 'bg-primary-light text-primary' : 'bg-border text-muted' }}">
                                    {{ $commune->is_active ? __('messages.yes') : __('messages.no') }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-3">
                                    <a href="{{ route('admin.communes.edit', $commune) }}" class="font-medium text-primary hover:underline">
                                        {{ __('regions.edit') }}
                                    </a>
                                    <form
                                        method="POST" action="{{ route('admin.communes.destroy', $commune) }}"
                                        data-confirm="{{ __('regions.delete_commune_confirm') }}"
                                        data-confirm-button="{{ __('messages.delete') }}"
                                        data-cancel-button="{{ __('messages.cancel') }}"
                                    >
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="font-medium text-accent hover:underline">
                                            {{ __('messages.delete') }}
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Cartes (mobile) --}}
        <div class="mt-6 space-y-3 lg:hidden">
            @foreach ($communes as $commune)
                <div class="rounded-2xl border border-border bg-surface p-4 shadow-sm">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <p class="font-semibold text-text">{{ $commune->getTranslation('name', 'fr') }}</p>
                            <p class="text-sm text-muted" dir="rtl">{{ $commune->getTranslation('name', 'ar') }}</p>
                        </div>
                        <span class="shrink-0 rounded-full px-2.5 py-1 text-xs font-semibold {{ $commune->is_active ? 'bg-primary-light text-primary' : 'bg-border text-muted' }}">
                            {{ $commune->is_active ? __('messages.yes') : __('messages.no') }}
                        </span>
                    </div>
                    <p class="mt-1 text-xs text-muted">{{ $commune->code }}</p>
                    <div class="mt-3 flex items-center gap-4 border-t border-border pt-3">
                        <a href="{{ route('admin.communes.edit', $commune) }}" class="text-sm font-medium text-primary hover:underline">
                            {{ __('regions.edit') }}
                        </a>
                        <form
                            method="POST" action="{{ route('admin.communes.destroy', $commune) }}"
                            data-confirm="{{ __('regions.delete_commune_confirm') }}"
                            data-confirm-button="{{ __('messages.delete') }}"
                            data-cancel-button="{{ __('messages.cancel') }}"
                        >
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-sm font-medium text-accent hover:underline">
                                {{ __('messages.delete') }}
                            </button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    <div class="mt-6 max-w-md rounded-2xl border border-border bg-surface p-6">
        <h2 class="text-sm font-semibold uppercase tracking-wide text-muted">{{ __('regions.add_commune_title') }}</h2>

        <form method="POST" action="{{ route('admin.moughataas.communes.store', $moughataa) }}" class="mt-4 space-y-4">
            @csrf

            <div>
                <label class="block text-sm font-medium text-text">{{ __('regions.label_code') }}</label>
                <input type="text" name="code" value="{{ old('code') }}" required
                    class="mt-1 block w-full rounded-lg border border-border px-3 py-2 text-sm focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary">
            </div>

            <div>
                <label class="block text-sm font-medium text-text">{{ __('regions.label_name_fr') }}</label>
                <input type="text" name="name_fr" value="{{ old('name_fr') }}" required
                    class="mt-1 block w-full rounded-lg border border-border px-3 py-2 text-sm focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary">
            </div>

            <div>
                <label class="block text-sm font-medium text-text">{{ __('regions.label_name_ar') }}</label>
                <input type="text" name="name_ar" dir="rtl" value="{{ old('name_ar') }}" required
                    class="mt-1 block w-full rounded-lg border border-border px-3 py-2 text-sm focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary">
            </div>

            <button type="submit" class="rounded-lg bg-primary px-4 py-2 text-sm font-semibold text-white hover:bg-primary-dark">
                {{ __('regions.save') }}
            </button>
        </form>
    </div>
@endsection
