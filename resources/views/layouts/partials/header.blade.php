@php
    $user = auth()->user();
    $unreadCount = $user->unreadNotificationsCount();
@endphp

<header class="relative flex items-center justify-between gap-4 overflow-hidden bg-linear-to-r from-primary-dark via-primary to-primary-dark px-4 py-3 shadow-sm sm:px-6">
    <div class="pointer-events-none absolute -end-10 -top-10 h-32 w-32 rounded-full bg-secondary/20 blur-2xl"></div>

    <div class="relative z-10 flex items-center gap-3">
        <button
            type="button"
            data-toggle="sidebar"
            class="rounded-lg p-2 text-white hover:bg-white/15 lg:hidden"
            aria-label="{{ __('members.nav_dashboard') }}"
        >
            <i class="bi bi-list text-xl"></i>
        </button>

        @hasSection('breadcrumb')
            <div class="text-sm text-white/80">
                @yield('breadcrumb')
            </div>
        @endif
    </div>

    <div class="relative z-10 flex items-center gap-2 sm:gap-3">
        <div class="flex items-center gap-1 rounded-full border border-white/20 bg-white/10 p-1 text-xs">
            <a
                href="{{ route('locale.switch', 'fr') }}"
                class="rounded-full px-2 py-1 {{ app()->getLocale() === 'fr' ? 'bg-white text-primary' : 'text-white/75' }}"
            >
                FR
            </a>
            <a
                href="{{ route('locale.switch', 'ar') }}"
                class="rounded-full px-2 py-1 {{ app()->getLocale() === 'ar' ? 'bg-white text-primary' : 'text-white/75' }}"
            >
                عربي
            </a>
        </div>

        <a
            href="{{ route('notifications.index') }}"
            class="relative rounded-lg p-2 text-white hover:bg-white/15"
            aria-label="{{ __('members.nav_my_notifications') }}"
        >
            <i class="bi bi-bell text-lg"></i>
            @if ($unreadCount > 0)
                <span class="absolute -top-1 -end-1 flex h-4 min-w-4 items-center justify-center rounded-full bg-accent px-1 text-[10px] font-semibold text-white ring-2 ring-primary">
                    {{ $unreadCount > 9 ? '9+' : $unreadCount }}
                </span>
            @endif
        </a>

        <div class="flex items-center gap-2 border-s border-white/20 ps-2 sm:ps-3">
            <div class="hidden text-end sm:block">
                <p class="text-sm font-medium text-white">{{ $user->display_name }}</p>
                <p class="text-xs text-white/70">
                    {{ $user->hasRole('administrateur') ? __('members.role_administrateur') : __('members.role_membre') }}
                </p>
            </div>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button
                    type="submit"
                    class="rounded-lg p-2 text-white/85 hover:bg-white/15 hover:text-white"
                    title="{{ __('auth.logout') }}"
                >
                    <i class="bi bi-box-arrow-right text-lg"></i>
                </button>
            </form>
        </div>
    </div>
</header>
