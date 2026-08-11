@extends('layouts.app')

@section('title', __('problematics.create_title'))

@section('content')
    <a href="{{ route('admin.problematics.index') }}" class="text-sm text-muted hover:text-primary">
        <i class="bi bi-arrow-left"></i> {{ __('problematics.title') }}
    </a>

    <h1 class="mt-1 text-xl font-semibold text-text">{{ __('problematics.create_title') }}</h1>

    <form method="POST" action="{{ route('admin.problematics.store') }}" class="mt-6 max-w-2xl space-y-4 rounded-2xl border border-border bg-surface p-6">
        @csrf
        @include('admin.problematics._form')

        <button type="submit" class="rounded-lg bg-primary px-4 py-2 text-sm font-semibold text-white hover:bg-primary-dark">
            {{ __('problematics.save') }}
        </button>
    </form>
@endsection
