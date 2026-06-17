<header class="topbar">
    <h1 class="topbar-title">@yield('page-title', 'Dashboard')</h1>
    <div class="topbar-right">
        @if(($unreadNotifications ?? 0) > 0)
            <a href="{{ route('admin.notifications.index') }}" class="btn btn-sm btn-brand">
                {{ $unreadNotifications }} unread
            </a>
        @endif
    </div>
</header>
