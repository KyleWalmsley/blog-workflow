<aside class="sidebar">
<div class="logo-area">
    <div class="logo">
        <div class="logo-icon">B</div>
        <span class="logo-text">Blog Workflow</span>
    </div>
</div>

<nav>
    <div class="nav-section">
        <div class="nav-label">Workflow</div>
        <a href="{{ route('admin.dashboard') }}" class="nav-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
            Dashboard
        </a>
        <a href="{{ route('admin.jobs.index') }}" class="nav-item {{ request()->routeIs('admin.jobs.*') ? 'active' : '' }}">
            Jobs
        </a>
        <a href="{{ route('admin.clients.index') }}" class="nav-item {{ request()->routeIs('admin.clients.*') ? 'active' : '' }}">
            Clients
        </a>
    </div>
    <div class="nav-section">
        <div class="nav-label">System</div>
        <a href="{{ route('admin.notifications.index') }}" class="nav-item {{ request()->routeIs('admin.notifications.*') ? 'active' : '' }}">
            Notifications
            @if(($unreadNotifications ?? 0) > 0)
                <span class="nav-badge">{{ $unreadNotifications }}</span>
            @endif
        </a>
    </div>
</nav>
</aside>
</aside>
