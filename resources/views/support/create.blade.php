@extends('layouts.app')

@section('title', __('support.new_message'))

@section('content')
    <a href="{{ route('support.index') }}" class="text-sm text-muted hover:text-primary">
        <i class="bi bi-arrow-left"></i> {{ __('support.back_to_list') }}
    </a>

    <h1 class="mt-1 text-xl font-semibold text-text">{{ __('support.new_message') }}</h1>

    @if ($errors->any())
        <div class="mt-4 rounded-lg border border-accent/30 bg-accent/5 px-4 py-3 text-sm text-accent">
            <ul class="list-inside list-disc space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('support.store') }}" class="mt-6 max-w-2xl space-y-4 rounded-2xl border border-border bg-surface p-6">
        @csrf

        @if ($relatedType)
            <input type="hidden" name="related_type" value="{{ $relatedType }}">
            <input type="hidden" name="related_id" value="{{ $relatedId }}">
            <p class="text-xs font-medium text-primary">
                {{ __('support.related_to_label') }}
                {{ $relatedType === 'problematic' ? __('memberships.section_problematics') : __('memberships.section_population_need') }}
            </p>
        @endif

        <div>
            <label class="block text-sm font-medium text-text">{{ __('support.label_subject') }}</label>
            <input type="text" name="subject" value="{{ old('subject') }}" required
                class="mt-1 block w-full rounded-lg border border-border px-3 py-2 text-sm focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary">
        </div>

        <div>
            <label class="block text-sm font-medium text-text">{{ __('support.label_body') }}</label>
            <textarea name="body" rows="6" required
                class="mt-1 block w-full rounded-lg border border-border px-3 py-2 text-sm focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary">{{ old('body') }}</textarea>
        </div>

        <button type="submit" class="inline-flex items-center gap-2 rounded-lg bg-primary px-4 py-2 text-sm font-semibold text-white hover:bg-primary-dark">
            <i class="bi bi-send"></i> {{ __('support.send') }}
        </button>
    </form>
@endsection
