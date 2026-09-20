@extends('layouts.guest')

@section('content')
    <h1 class="text-xl font-semibold text-primary">{{ __('account_deletion.title') }}</h1>
    <p class="mt-2 text-sm text-muted">{{ __('account_deletion.intro') }}</p>

    @if (session('status'))
        <div class="mt-4 rounded-lg border border-primary/30 bg-primary-light px-4 py-3 text-sm text-primary">
            {{ session('status') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="mt-4 rounded-lg border border-accent/30 bg-accent/5 px-4 py-3 text-sm text-accent">
            <ul class="list-inside list-disc space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('account-deletion.store') }}" class="mt-6 space-y-4">
        @csrf

        <div>
            <label for="full_name" class="block text-sm font-medium text-text">
                {{ __('account_deletion.full_name') }}
            </label>
            <input
                type="text"
                id="full_name"
                name="full_name"
                value="{{ old('full_name') }}"
                required
                autofocus
                class="mt-1 block w-full rounded-lg border border-border px-3 py-2 text-sm focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary"
            >
        </div>

        <div>
            <label for="contact" class="block text-sm font-medium text-text">
                {{ __('account_deletion.contact') }}
            </label>
            <input
                type="text"
                id="contact"
                name="contact"
                value="{{ old('contact') }}"
                required
                class="mt-1 block w-full rounded-lg border border-border px-3 py-2 text-sm focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary"
            >
        </div>

        <div>
            <label for="member_number" class="block text-sm font-medium text-text">
                {{ __('account_deletion.member_number') }}
                <span class="text-xs text-muted">({{ __('account_deletion.member_number_optional') }})</span>
            </label>
            <input
                type="text"
                id="member_number"
                name="member_number"
                value="{{ old('member_number') }}"
                class="mt-1 block w-full rounded-lg border border-border px-3 py-2 text-sm focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary"
            >
        </div>

        <div>
            <label for="reason" class="block text-sm font-medium text-text">
                {{ __('account_deletion.reason') }}
            </label>
            <textarea
                id="reason"
                name="reason"
                rows="3"
                class="mt-1 block w-full rounded-lg border border-border px-3 py-2 text-sm focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary"
            >{{ old('reason') }}</textarea>
        </div>

        <p class="text-xs text-muted">{{ __('account_deletion.processing_note') }}</p>

        <button
            type="submit"
            class="w-full rounded-lg bg-primary px-4 py-2.5 text-sm font-semibold text-white transition-colors hover:bg-primary-dark"
        >
            {{ __('account_deletion.submit') }}
        </button>
    </form>
@endsection
