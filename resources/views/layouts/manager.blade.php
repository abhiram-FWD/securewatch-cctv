<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $pageTitle ?? 'SecureWatch' }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css'])
    @yield('styles')
</head>
<body>
    <div class="sidebar-backdrop" id="sidebarBackdrop" onclick="closeSidebar()"></div>

    <div class="sidebar" id="sidebar">
        <div class="sidebar-logo">
            <i class="bi bi-shield-check text-teal"></i> Secure<span>Watch</span>
        </div>
        <div class="sidebar-user-block">
            <div class="sidebar-user-name">{{ Auth::user()->name }}</div>
            <div class="sidebar-user-meta">Manager &bull; {{ Auth::user()->area ?? 'All Areas' }}</div>
            <div class="mt-2">
                @if(session('is_on_duty'))
                    <span class="badge badge-green">● ON DUTY</span>
                @else
                    <span class="badge bg-secondary-subtle text-secondary">○ OFF DUTY</span>
                @endif
            </div>
        </div>
        <nav class="sidebar-nav">
            <a href="{{ route('manager.dashboard') }}" class="{{ request()->is('manager/dashboard') ? 'active' : '' }}">
                <i class="bi bi-grid"></i> Dashboard
            </a>
            <a href="{{ route('manager.alerts') }}" class="{{ request()->is('manager/alerts') || request()->is('manager/alerts/show/*') ? 'active' : '' }}">
                <i class="bi bi-bell"></i> Alerts
            </a>
            <a href="{{ route('manager.alerts.create') }}" class="{{ request()->is('manager/alerts/create') ? 'active' : '' }}">
                <i class="bi bi-exclamation-triangle"></i> Raise Alert
            </a>
            <a href="{{ route('manager.reports') }}" class="{{ request()->is('manager/reports*') ? 'active' : '' }}">
                <i class="bi bi-bar-chart-line"></i> Reports
            </a>
            <a href="{{ route('manager.messages') }}" class="{{ request()->is('manager/messages*') ? 'active' : '' }}">
                <i class="bi bi-envelope"></i> Messages
            </a>
        </nav>
        <div class="sidebar-footer">
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="sidebar-logout-btn">
                    <i class="bi bi-box-arrow-right"></i> Logout
                </button>
            </form>
        </div>
    </div>

    <div class="main-content">
        <div class="top-bar position-relative">
            <div class="d-flex align-items-center gap-3">
                <button class="hamburger-btn" onclick="toggleSidebar()" data-bs-toggle="tooltip" data-bs-title="Toggle sidebar"><i class="bi bi-list"></i></button>
                <span class="top-bar-title">@yield('page-title')</span>
            </div>
            <div class="d-flex align-items-center gap-3">
                <div class="text-muted small fw-medium d-none d-md-block" id="managerClock"></div>
                @if(session('is_on_duty'))
                    <span style="background:var(--teal-light); color:var(--teal); font-size:11px; font-weight:600; padding:4px 10px; border-radius:6px;">
                        <i class="bi bi-clock me-1"></i>Shift Active
                    </span>
                @endif
                @php($unreadCount = $unread_count ?? 0)
                <div class="position-relative" style="cursor:pointer" onclick="toggleNotifications()" data-bs-toggle="tooltip" data-bs-title="Notifications">
                    <i class="bi bi-bell" style="font-size:18px"></i>
                    <span id="managerBellBadge" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger {{ $unreadCount > 0 ? '' : 'd-none' }}" style="font-size:9px">{{ $unreadCount }}</span>
                </div>
                <div class="notif-dropdown" id="notif-dropdown">
                    <div class="notif-header">Notifications <span id="notif-count" class="badge bg-danger" style="font-size:10px;">{{ $unreadCount > 0 ? $unreadCount : '' }}</span></div>
                    <div id="notif-list" style="max-height:300px; overflow-y:auto;"><div class="notif-empty">Loading…</div></div>
                </div>
                <div class="dropdown">
                    <div class="user-avatar dropdown-toggle" data-bs-toggle="dropdown" title="{{ Auth::user()->name }}">
                        {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                    </div>
                    <ul class="dropdown-menu dropdown-menu-end avatar-dropdown-menu">
                        <li class="px-3 py-2">
                            <div class="dropdown-user-name">{{ Auth::user()->name }}</div>
                            <div class="dropdown-user-email">{{ Auth::user()->email }}</div>
                        </li>
                        <li><hr class="dropdown-divider my-1"></li>
                        <li>
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button type="submit" class="dropdown-item text-danger"><i class="bi bi-box-arrow-right me-2"></i>Logout</button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="page-content">
            @if(session('success'))
                <div class="flash-success"><i class="bi bi-check-circle"></i> {{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="flash-error"><i class="bi bi-exclamation-circle"></i> {{ session('error') }}</div>
            @endif
            @if($errors->any())
                <div class="flash-error"><i class="bi bi-exclamation-circle"></i> {{ $errors->first() }}</div>
            @endif
            @yield('content')
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function updateManagerClock() {
            const el = document.getElementById('managerClock');
            if (!el) return;
            const now = new Date();
            el.textContent = now.toLocaleDateString('en-US', { weekday:'short', month:'short', day:'numeric', hour:'2-digit', minute:'2-digit', second:'2-digit' });
        }
        setInterval(updateManagerClock, 1000); updateManagerClock();
        function toggleSidebar() {
            document.getElementById('sidebar').classList.toggle('open');
            document.getElementById('sidebarBackdrop').classList.toggle('show');
        }
        function closeSidebar() {
            document.getElementById('sidebar').classList.remove('open');
            document.getElementById('sidebarBackdrop').classList.remove('show');
        }
        function toggleNotifications() {
            const dd = document.getElementById('notif-dropdown');
            dd.classList.toggle('show');
            if (dd.classList.contains('show')) loadNotifications();
        }
        function loadNotifications() {
            fetch('/api/notifications').then(r => r.json()).then(data => {
                const list = document.getElementById('notif-list');
                const badge = document.getElementById('managerBellBadge');
                const count = document.getElementById('notif-count');
                if (data.unread_count > 0) { badge.classList.remove('d-none'); badge.textContent=data.unread_count; count.textContent=data.unread_count; }
                else { badge.classList.add('d-none'); badge.textContent=''; count.textContent=''; }
                if (!data.notifications || data.notifications.length === 0) { list.innerHTML='<div class="notif-empty">No new notifications</div>'; return; }
                list.innerHTML = data.notifications.map(n => `<div class="notif-item"><div>${n.message}</div><div class="notif-time">${n.time_ago}</div></div>`).join('');
            }).catch(() => {});
        }
        document.addEventListener('click', function(e) {
            const dd = document.getElementById('notif-dropdown');
            if (!e.target.closest('#notif-dropdown') && !e.target.closest('[onclick="toggleNotifications()"]')) dd.classList.remove('show');
        });
        document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach((el) => new bootstrap.Tooltip(el));
    </script>
    <script src="{{ asset('js/alert-sound.js') }}"></script>
    @vite(['resources/js/app.js'])
    @stack('scripts')
</body>
</html>
