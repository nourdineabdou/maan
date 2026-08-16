@php
    $isAdmin = auth()->user()->hasRole('administrateur');

    $memberLinks = [
        ['label' => __('members.nav_dashboard'), 'icon' => 'bi-speedometer2', 'route' => 'dashboard'],
        ['label' => __('members.nav_my_profile'), 'icon' => 'bi-person', 'route' => 'profile.overview'],
        ['label' => __('members.nav_my_membership'), 'icon' => 'bi-card-checklist', 'route' => 'profile.membership'],
        ['label' => __('members.nav_personal_info'), 'icon' => 'bi-person-vcard', 'route' => 'profile.personal.edit'],
        ['label' => __('members.nav_professional_info'), 'icon' => 'bi-briefcase', 'route' => 'profile.professional.edit'],
        ['label' => __('members.nav_my_problematics'), 'icon' => 'bi-flag', 'route' => 'profile.problematics.edit'],
        ['label' => __('members.nav_population_need'), 'icon' => 'bi-megaphone', 'route' => 'profile.need.edit'],
        ['label' => __('members.nav_my_declared_needs'), 'icon' => 'bi-list-check', 'route' => 'profile.declarations.needs'],
        ['label' => __('members.nav_my_declared_problematics'), 'icon' => 'bi-list-check', 'route' => 'profile.declarations.problematics'],
        ['label' => __('members.nav_my_documents'), 'icon' => 'bi-file-earmark-text', 'route' => 'profile.documents.index'],
        ['label' => __('members.nav_my_card'), 'icon' => 'bi-postcard', 'route' => 'card.show'],
        ['label' => __('members.nav_my_notifications'), 'icon' => 'bi-bell', 'route' => 'notifications.index'],
        ['label' => __('members.nav_support'), 'icon' => 'bi-chat-dots', 'route' => 'support.index'],
        ['label' => __('members.nav_settings'), 'icon' => 'bi-gear', 'route' => 'settings.edit'],
    ];

    $adminLinks = [
        ['label' => __('members.nav_admin_dashboard'), 'icon' => 'bi-speedometer2', 'route' => 'dashboard'],
        ['label' => __('members.nav_memberships'), 'icon' => 'bi-card-checklist', 'route' => 'admin.memberships.index', 'permission' => 'members.view'],
        ['label' => __('members.nav_members'), 'icon' => 'bi-people', 'route' => 'admin.members.index', 'permission' => 'members.view'],
        ['label' => __('members.nav_validations'), 'icon' => 'bi-patch-check', 'route' => 'admin.memberships.index', 'params' => ['status' => 'pending'], 'permission' => 'memberships.approve'],
        ['label' => __('members.nav_documents'), 'icon' => 'bi-file-earmark-text', 'route' => 'admin.documents.index', 'permission' => 'documents.view'],
        ['label' => __('members.nav_regions'), 'icon' => 'bi-map', 'route' => 'admin.regions.index', 'permission' => 'regions.manage'],
        ['label' => __('members.nav_declared_needs'), 'icon' => 'bi-megaphone', 'route' => 'admin.declarations.needs', 'permission' => 'problematics.manage'],
        ['label' => __('members.nav_declared_problematics'), 'icon' => 'bi-flag', 'route' => 'admin.declarations.problematics', 'permission' => 'problematics.manage'],
        ['label' => __('members.nav_problematics'), 'icon' => 'bi-tags', 'route' => 'admin.problematics.index', 'permission' => 'problematics.manage'],
        ['label' => __('members.nav_notifications'), 'icon' => 'bi-bell', 'route' => 'admin.notifications.index', 'permission' => 'notifications.send'],
        ['label' => __('members.nav_announcements'), 'icon' => 'bi-megaphone', 'route' => 'admin.announcements.index', 'permission' => 'announcements.manage'],
        ['label' => __('members.nav_admin_support'), 'icon' => 'bi-chat-dots', 'route' => 'admin.support.index', 'permission' => 'support_messages.manage'],
        ['label' => __('members.nav_statistics'), 'icon' => 'bi-graph-up', 'href' => '#', 'permission' => 'statistics.view'],
        ['label' => __('members.nav_exports'), 'icon' => 'bi-download', 'route' => 'admin.exports.index', 'permission' => 'members.export'],
        ['label' => __('members.nav_users'), 'icon' => 'bi-person-gear', 'href' => '#', 'permission' => 'users.manage'],
        ['label' => __('members.nav_roles'), 'icon' => 'bi-shield-lock', 'href' => '#', 'permission' => 'roles.manage'],
        ['label' => __('members.nav_admin_settings'), 'icon' => 'bi-gear', 'route' => 'settings.edit', 'permission' => 'settings.manage'],
    ];

    $links = $isAdmin ? $adminLinks : $memberLinks;
@endphp

<aside
    id="app-sidebar"
    class="fixed inset-y-0 start-0 z-40 flex w-64 flex-col border-e border-border bg-surface lg:static lg:translate-x-0"
>
    <div class="flex items-center gap-3 border-b border-border px-5 py-4">
        <img
            src="{{ asset(app()->getLocale() === 'ar' ? 'logo_ar.png' : 'logo_fr.png') }}"
            alt="{{ __('messages.platform_name') }}"
            class="animate-logo-fall h-10 w-auto"
        >
        <span class="text-sm font-semibold text-primary">{{ __('messages.platform_name') }}</span>
    </div>

    <nav class="flex-1 overflow-y-auto px-3 py-4">
        <ul class="space-y-1">
            @foreach ($links as $link)
                @if (! isset($link['permission']) || auth()->user()->can($link['permission']))
                    @php
                        $isActive = isset($link['route'])
                            && request()->routeIs($link['route'])
                            && request()->query('status') == ($link['params']['status'] ?? null);
                    @endphp
                    <li>
                        <a
                            href="{{ isset($link['route']) ? route($link['route'], $link['params'] ?? []) : $link['href'] }}"
                            class="flex items-start gap-3 rounded-lg px-3 py-2 text-sm font-medium text-text transition-colors hover:bg-primary-light hover:text-primary
                                {{ $isActive ? 'bg-primary-light text-primary' : '' }}"
                        >
                            <i class="bi {{ $link['icon'] }} mt-0.5 shrink-0 text-base"></i>
                            <span class="leading-snug">{{ $link['label'] }}</span>
                        </a>
                    </li>
                @endif
            @endforeach
        </ul>
    </nav>
</aside>

<div id="sidebar-overlay" class="fixed inset-0 z-30 hidden bg-black/40 lg:hidden"></div>
