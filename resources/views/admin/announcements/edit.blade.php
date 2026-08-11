@extends('layouts.app')

@section('title', __('announcements.edit_title'))

@section('content')
    <a href="{{ route('admin.announcements.index') }}" class="text-sm text-muted hover:text-primary">
        <i class="bi bi-arrow-left"></i> {{ __('announcements.title') }}
    </a>

    <h1 class="mt-1 text-xl font-semibold text-text">{{ __('announcements.edit_title') }}</h1>

    @if ($errors->any())
        <div class="mt-4 rounded-lg border border-accent/30 bg-accent/5 px-4 py-3 text-sm text-accent">
            <ul class="list-inside list-disc space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if ($announcement->images->isNotEmpty())
        <div class="mt-6 max-w-2xl rounded-2xl border border-border bg-surface p-6">
            <p class="text-sm font-medium text-text">{{ __('announcements.current_images') }}</p>

            <div class="mt-3 flex flex-wrap gap-3">
                @foreach ($announcement->images as $image)
                    <div class="group relative h-[100px] w-[100px] shrink-0 overflow-hidden rounded-xl border border-border">
                        <img src="{{ $image->url }}" alt="" class="h-full w-full object-cover">

                        <form
                            method="POST" action="{{ route('admin.announcements.images.destroy', [$announcement, $image]) }}"
                            data-confirm="{{ __('announcements.delete_image_confirm') }}"
                            data-confirm-button="{{ __('messages.delete') }}"
                            data-cancel-button="{{ __('messages.cancel') }}"
                            class="absolute end-1.5 top-1.5"
                        >
                            @csrf
                            @method('DELETE')
                            <button
                                type="submit"
                                class="flex h-6 w-6 items-center justify-center rounded-full bg-black/60 text-white transition-colors hover:bg-accent"
                                aria-label="{{ __('messages.delete') }}"
                            >
                                <i class="bi bi-trash3 text-[10px]"></i>
                            </button>
                        </form>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <form method="POST" action="{{ route('admin.announcements.update', $announcement) }}" enctype="multipart/form-data" class="mt-6 max-w-2xl space-y-4 rounded-2xl border border-border bg-surface p-6">
        @csrf
        @method('PUT')
        @include('admin.announcements._form')

        <button type="submit" class="rounded-lg bg-primary px-4 py-2 text-sm font-semibold text-white hover:bg-primary-dark">
            {{ __('announcements.save') }}
        </button>
    </form>
@endsection
