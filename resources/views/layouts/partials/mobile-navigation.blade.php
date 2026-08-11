@php
    $isAdmin = auth()->user()->hasRole('administrateur');

    $tabs = [
        [
            'route' => 'dashboard',
            'active' => request()->routeIs('dashboard'),
            'icon' => 'bi-speedometer2',
            'label' => __('members.nav_dashboard_mobile'),
        ],
        [
            'route' => $isAdmin ? 'admin.members.index' : 'profile.membership',
            'active' => $isAdmin ? request()->routeIs('admin.members.*') : request()->routeIs('profile.membership'),
            'icon' => $isAdmin ? 'bi-people' : 'bi-card-checklist',
            'label' => $isAdmin ? __('members.nav_members') : __('members.nav_my_membership_mobile'),
        ],
        [
            'route' => 'notifications.index',
            'active' => request()->routeIs('notifications.*'),
            'icon' => 'bi-bell',
            'label' => __('members.nav_my_notifications_mobile'),
        ],
        [
            'route' => 'settings.edit',
            'active' => request()->routeIs('settings.edit'),
            'icon' => 'bi-gear',
            'label' => __('members.nav_settings'),
        ],
    ];
@endphp

<nav class="fixed inset-x-3 bottom-3 z-30 lg:hidden">
    <div class="flex items-center justify-around gap-0.5 rounded-full border border-border bg-surface/95 p-1.5 shadow-lg backdrop-blur-sm">
        @foreach ($tabs as $tab)
            <a
                href="{{ route($tab['route']) }}"
                class="flex min-w-0 flex-1 flex-col items-center gap-0.5 rounded-full px-1 py-1.5 text-[10px] font-medium leading-none transition-colors
                    {{ $tab['active'] ? 'bg-primary-light text-primary' : 'text-muted hover:text-primary' }}"
            >
                <i class="bi {{ $tab['icon'] }} text-lg"></i>
                <span class="w-full truncate text-center">{{ $tab['label'] }}</span>
            </a>
        @endforeach
    </div>
</nav>
