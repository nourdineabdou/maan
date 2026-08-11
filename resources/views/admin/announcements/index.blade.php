@extends('layouts.app')

@section('title', __('announcements.title'))

@section('content')
    <div class="flex flex-wrap items-center justify-between gap-3">
        <div>
            <h1 class="text-xl font-semibold text-text">{{ __('announcements.title') }}</h1>
            <p class="mt-1 text-sm text-muted">{{ __('announcements.subtitle') }}</p>
        </div>
        <a href="{{ route('admin.announcements.create') }}" class="inline-flex items-center gap-2 rounded-lg bg-primary px-4 py-2 text-sm font-semibold text-white hover:bg-primary-dark">
            <i class="bi bi-plus-circle"></i> {{ __('announcements.add') }}
        </a>
    </div>

    @if ($announcements->isEmpty())
        <div class="mt-6 rounded-2xl border border-dashed border-border bg-surface p-10 text-center text-sm text-muted">
            {{ __('announcements.no_results') }}
        </div>
    @else
        {{-- Table (desktop) --}}
        <div class="mt-6 hidden overflow-x-auto rounded-2xl border border-border bg-surface lg:block">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-border text-start text-xs uppercase text-muted">
                        <th class="px-4 py-3 text-start">{{ __('announcements.column_title_fr') }}</th>
                        <th class="px-4 py-3 text-start">{{ __('announcements.column_title_ar') }}</th>
                        <th class="px-4 py-3 text-start">{{ __('announcements.column_active') }}</th>
                        <th class="px-4 py-3 text-start">{{ __('announcements.column_date') }}</th>
                        <th class="px-4 py-3 text-start">{{ __('announcements.column_actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($announcements as $announcement)
                        <tr class="border-b border-border last:border-0">
                            <td class="px-4 py-3 font-medium text-text">{{ $announcement->getTranslation('title', 'fr') }}</td>
                            <td class="px-4 py-3 text-text" dir="rtl">{{ $announcement->getTranslation('title', 'ar') }}</td>
                            <td class="px-4 py-3">
                                <span class="rounded-full px-2.5 py-1 text-xs font-semibold {{ $announcement->is_active ? 'bg-primary-light text-primary' : 'bg-border text-muted' }}">
                                    {{ $announcement->is_active ? __('messages.yes') : __('messages.no') }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-muted">{{ $announcement->created_at->format('d/m/Y') }}</td>
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-3">
                                    <a href="{{ route('admin.announcements.edit', $announcement) }}" class="font-medium text-primary hover:underline">
                                        {{ __('announcements.edit') }}
                                    </a>
                                    <form
                                        method="POST" action="{{ route('admin.announcements.destroy', $announcement) }}"
                                        data-confirm="{{ __('announcements.delete_confirm') }}"
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
            @foreach ($announcements as $announcement)
                <div class="rounded-2xl border border-border bg-surface p-4 shadow-sm">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <p class="font-semibold text-text">{{ $announcement->getTranslation('title', 'fr') }}</p>
                            <p class="text-sm text-muted" dir="rtl">{{ $announcement->getTranslation('title', 'ar') }}</p>
                        </div>
                        <span class="shrink-0 rounded-full px-2.5 py-1 text-xs font-semibold {{ $announcement->is_active ? 'bg-primary-light text-primary' : 'bg-border text-muted' }}">
                            {{ $announcement->is_active ? __('messages.yes') : __('messages.no') }}
                        </span>
                    </div>
                    <p class="mt-1 text-xs text-muted">{{ $announcement->created_at->format('d/m/Y') }}</p>
                    <div class="mt-3 flex items-center gap-4 border-t border-border pt-3">
                        <a href="{{ route('admin.announcements.edit', $announcement) }}" class="text-sm font-medium text-primary hover:underline">
                            {{ __('announcements.edit') }}
                        </a>
                        <form
                            method="POST" action="{{ route('admin.announcements.destroy', $announcement) }}"
                            data-confirm="{{ __('announcements.delete_confirm') }}"
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

        <div class="mt-6">
            {{ $announcements->links() }}
        </div>
    @endif
@endsection
