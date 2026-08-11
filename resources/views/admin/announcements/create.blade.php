@extends('layouts.app')

@section('title', __('announcements.create_title'))

@section('content')
    <a href="{{ route('admin.announcements.index') }}" class="text-sm text-muted hover:text-primary">
        <i class="bi bi-arrow-left"></i> {{ __('announcements.title') }}
    </a>

    <h1 class="mt-1 text-xl font-semibold text-text">{{ __('announcements.create_title') }}</h1>

    @if ($errors->any())
        <div class="mt-4 rounded-lg border border-accent/30 bg-accent/5 px-4 py-3 text-sm text-accent">
            <ul class="list-inside list-disc space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('admin.announcements.store') }}" enctype="multipart/form-data" class="mt-6 max-w-2xl space-y-4 rounded-2xl border border-border bg-surface p-6">
        @csrf
        @include('admin.announcements._form')

        <button type="submit" class="rounded-lg bg-primary px-4 py-2 text-sm font-semibold text-white hover:bg-primary-dark">
            {{ __('announcements.save') }}
        </button>
    </form>
@endsection
