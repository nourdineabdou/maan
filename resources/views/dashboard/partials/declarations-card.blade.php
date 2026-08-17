@php
    $total = array_sum($counts);
    $dotClass = fn ($status) => match ($status) {
        'resolved' => 'bg-primary',
        'in_progress' => 'bg-secondary',
        default => 'bg-border',
    };
@endphp

<div class="rounded-2xl border border-border bg-surface p-5 shadow-sm transition hover:shadow-md">
    <div class="flex items-center gap-3">
        <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl {{ $badgeClass }} shadow-sm">
            <i class="bi {{ $icon }} text-xl"></i>
        </span>
        <div>
            <p class="text-sm font-semibold text-text">{{ $title }}</p>
            <p class="text-xs text-muted">{{ __('dashboard.declarations_total', ['count' => $total]) }}</p>
        </div>
    </div>

    <ul class="mt-4 space-y-1">
        @foreach (['submitted', 'in_progress', 'resolved'] as $status)
            <li>
                <a
                    href="{{ route($route, array_merge($routeParams ?? [], ['status' => $status])) }}"
                    class="flex items-center justify-between rounded-lg px-2 py-1.5 text-sm transition hover:bg-background"
                >
                    <span class="flex items-center gap-2 text-text">
                        <span class="h-2 w-2 rounded-full {{ $dotClass($status) }}"></span>
                        {{ __('forms.status_'.$status) }}
                    </span>
                    <span class="font-semibold text-text">{{ $counts[$status] }}</span>
                </a>
            </li>
        @endforeach
    </ul>
</div>
