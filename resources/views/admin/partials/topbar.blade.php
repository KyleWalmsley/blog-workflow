<header class="topbar">
    <nav class="topbar-tabs">
        <a href="{{ route('admin.dashboard') }}"
           class="topbar-tab {{ request()->routeIs('admin.dashboard') ? 'topbar-tab-active' : '' }}">
            Dashboard
        </a>
        <a href="{{ route('admin.notifications.index') }}"
           class="topbar-tab {{ request()->routeIs('admin.notifications.*') ? 'topbar-tab-active' : '' }}">
            Notifications
            @if(($unreadNotifications ?? 0) > 0)
                <span class="topbar-notif-dot"></span>
            @endif
        </a>
    </nav>

    <div class="topbar-right">
        <a href="{{ route('admin.notifications.index') }}" class="topbar-bell">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" width="20" height="20">
                <path fill-rule="evenodd" d="M4 8a6 6 0 1112 0c0 1.887.454 3.665 1.257 5.234a.75.75 0 01-.515 1.076 32.94 32.94 0 01-3.256.508 3.5 3.5 0 01-6.972 0 32.933 32.933 0 01-3.256-.508.75.75 0 01-.515-1.076A11.448 11.448 0 004 8zm6 7c-.655 0-1.305-.05-1.94-.144a2 2 0 003.88 0A25.974 25.974 0 0110 15z" clip-rule="evenodd" />
            </svg>
            @if(($unreadNotifications ?? 0) > 0)
                <span class="topbar-bell-dot"></span>
            @endif
        </a>
    </div>
</header>
