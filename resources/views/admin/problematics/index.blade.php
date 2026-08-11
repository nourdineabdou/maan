@extends('layouts.app')

@section('title', __('problematics.title'))

@section('content')
    <div class="flex flex-wrap items-center justify-between gap-3">
        <h1 class="text-xl font-semibold text-text">{{ __('problematics.title') }}</h1>
        <a href="{{ route('admin.problematics.create') }}" class="inline-flex items-center gap-2 rounded-lg bg-primary px-4 py-2 text-sm font-semibold text-white hover:bg-primary-dark">
            <i class="bi bi-plus-circle"></i> {{ __('problematics.add') }}
        </a>
    </div>

    {{-- Table (desktop) --}}
    <div class="mt-6 hidden overflow-x-auto rounded-2xl border border-border bg-surface lg:block">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-border text-start text-xs uppercase text-muted">
                    <th class="px-4 py-3 text-start">{{ __('problematics.column_code') }}</th>
                    <th class="px-4 py-3 text-start">{{ __('problematics.column_name_fr') }}</th>
                    <th class="px-4 py-3 text-start">{{ __('problematics.column_name_ar') }}</th>
                    <th class="px-4 py-3 text-start">{{ __('problematics.column_justification') }}</th>
                    <th class="px-4 py-3 text-start">{{ __('problematics.column_active') }}</th>
                    <th class="px-4 py-3 text-start">{{ __('problematics.column_actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($problematics as $problematic)
                    <tr class="border-b border-border last:border-0">
                        <td class="px-4 py-3 font-medium text-text">
                            @if ($problematic->icon)<i class="bi {{ $problematic->icon }} me-1 text-primary"></i>@endif
                            {{ $problematic->code }}
                        </td>
                        <td class="px-4 py-3 text-text">{{ $problematic->getTranslation('name', 'fr') }}</td>
                        <td class="px-4 py-3 text-text" dir="rtl">{{ $problematic->getTranslation('name', 'ar') }}</td>
                        <td class="px-4 py-3 text-muted">{{ $problematic->requires_justification ? __('messages.yes') : __('messages.no') }}</td>
                        <td class="px-4 py-3">
                            <span class="rounded-full px-2.5 py-1 text-xs font-semibold {{ $problematic->is_active ? 'bg-primary-light text-primary' : 'bg-border text-muted' }}">
                                {{ $problematic->is_active ? __('messages.yes') : __('messages.no') }}
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-3">
                                <a href="{{ route('admin.problematics.edit', $problematic) }}" class="font-medium text-primary hover:underline">
                                    {{ __('problematics.edit') }}
                                </a>
                                <form
                                    method="POST" action="{{ route('admin.problematics.destroy', $problematic) }}"
                                    data-confirm="{{ __('problematics.delete_confirm') }}"
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
        @foreach ($problematics as $problematic)
            <div class="rounded-2xl border border-border bg-surface p-4 shadow-sm">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <p class="font-semibold text-text">
                            @if ($problematic->icon)<i class="bi {{ $problematic->icon }} me-1 text-primary"></i>@endif
                            {{ $problematic->getTranslation('name', 'fr') }}
                        </p>
                        <p class="text-sm text-muted" dir="rtl">{{ $problematic->getTranslation('name', 'ar') }}</p>
                    </div>
                    <span class="shrink-0 rounded-full px-2.5 py-1 text-xs font-semibold {{ $problematic->is_active ? 'bg-primary-light text-primary' : 'bg-border text-muted' }}">
                        {{ $problematic->is_active ? __('messages.yes') : __('messages.no') }}
                    </span>
                </div>
                <p class="mt-1 text-xs text-muted">{{ $problematic->code }}</p>
                <div class="mt-3 flex items-center gap-4 border-t border-border pt-3">
                    <a href="{{ route('admin.problematics.edit', $problematic) }}" class="text-sm font-medium text-primary hover:underline">
                        {{ __('problematics.edit') }}
                    </a>
                    <form
                        method="POST" action="{{ route('admin.problematics.destroy', $problematic) }}"
                        data-confirm="{{ __('problematics.delete_confirm') }}"
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
@endsection
