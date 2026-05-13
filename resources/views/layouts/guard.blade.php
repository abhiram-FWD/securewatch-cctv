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
            <div class="sidebar-user-name">{{ auth()->user()->name ?? 'Guard' }}</div>
            <div class="sidebar-user-meta">Security Guard</div>
            <div class="mt-2">
                @if(session('is_on_duty'))
                    <span class="badge badge-green">● ON DUTY</span>
                @else
                    <span class="badge bg-secondary-subtle text-secondary">○ OFF DUTY</span>
                @endif
            </div>
            @if(session('shift_start') && session('shift_end'))
                <div class="small text-secondary mt-1">
                    Shift: {{ \Carbon\Carbon::parse(session('shift_start'))->format('H:i') }} – {{ \Carbon\Carbon::parse(session('shift_end'))->format('H:i') }}
                </div>
            @endif
        </div>
        <nav class="sidebar-nav">
            <a href="{{ route('guard.dashboard') }}" class="{{ request()->is('guard/dashboard') ? 'active' : '' }}">
                <i class="bi bi-grid"></i> Dashboard
            </a>
            <a href="{{ route('guard.alerts') }}" class="{{ request()->is('guard/alerts') || request()->is('guard/alerts/resolve/*') ? 'active' : '' }}">
                <i class="bi bi-bell"></i> My Alerts
            </a>
            <a href="{{ route('guard.alerts.create') }}" class="{{ request()->is('guard/alerts/create') ? 'active' : '' }}">
                <i class="bi bi-exclamation-triangle"></i> Raise Alert
            </a>
            <a href="#" class="emergency-link" data-bs-toggle="modal" data-bs-target="#emergencyModal">
                <i class="bi bi-shield-exclamation"></i> 🚨 Emergency
            </a>
            <a href="{{ route('guard.messages') }}" class="{{ request()->is('guard/messages*') ? 'active' : '' }}">
                <i class="bi bi-envelope"></i> Messages
            </a>
        </nav>
        <div class="sidebar-footer">
            <form method="POST" action="{{ route('logout') }}">
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
                <div class="text-muted small fw-medium d-none d-md-block" id="guardClock"></div>
                @if(session('is_on_duty') && session('shift_end'))
                    <div class="text-teal small fw-medium d-none d-md-block" id="shiftCountdown"
                         data-end="{{ session('shift_end') }}"></div>
                @endif
                @php($unreadCount = $unread_count ?? 0)
                <div class="position-relative" style="cursor:pointer" onclick="toggleNotifications()" data-bs-toggle="tooltip" data-bs-title="Notifications">
                    <i class="bi bi-bell" style="font-size:18px"></i>
                    <span id="guardBellBadge" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger {{ $unreadCount > 0 ? '' : 'd-none' }}"
                          style="font-size:9px">{{ $unreadCount }}</span>
                </div>
                <div class="notif-dropdown" id="notif-dropdown">
                    <div class="notif-header">Notifications <span id="notif-count" class="badge bg-danger" style="font-size:10px;">{{ $unreadCount > 0 ? $unreadCount : '' }}</span></div>
                    <div id="notif-list" style="max-height:300px; overflow-y:auto;"><div class="notif-empty">Loading…</div></div>
                </div>
                <div class="dropdown">
                    <div class="user-avatar dropdown-toggle" data-bs-toggle="dropdown" title="{{ auth()->user()->name }}">
                        {{ strtoupper(substr(auth()->user()->name ?? 'G', 0, 1)) }}
                    </div>
                    <ul class="dropdown-menu dropdown-menu-end avatar-dropdown-menu">
                        <li class="px-3 py-2">
                            <div class="dropdown-user-name">{{ auth()->user()->name }}</div>
                            <div class="dropdown-user-email">{{ auth()->user()->email }}</div>
                        </li>
                        <li><hr class="dropdown-divider my-1"></li>
                        <li>
                            <form method="POST" action="{{ route('logout') }}">
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

    <!-- Emergency Modal -->
    <div class="modal fade" id="emergencyModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content border-0 shadow-lg">
                <form action="{{ route('guard.alerts.emergency') }}" method="POST">
                    @csrf
                    <div class="modal-header border-0" style="background:var(--red); color:#fff;">
                        <h5 class="modal-title fw-bold"><i class="bi bi-shield-exclamation me-2"></i>🚨 RAISE EMERGENCY ALERT</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body p-4">
                        <div class="flash-error mb-4" style="border-radius:8px;">
                            <i class="bi bi-exclamation-triangle-fill"></i>
                            <span><strong>Warning:</strong> This notifies ALL staff immediately, including off-duty personnel. Use only for critical situations.</span>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-medium small">Camera Location</label>
                            <select name="camera_id" class="form-select" required>
                                <option value="">Select Camera…</option>
                                @foreach(\App\Models\Camera::where('status', 'active')->get() as $camera)
                                    <option value="{{ $camera->id }}">{{ $camera->name }} ({{ $camera->location }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-medium small">Emergency Description</label>
                            <textarea name="description" class="form-control" rows="3" required minlength="10"
                                      placeholder="Describe the emergency situation…"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer border-0">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger px-4 fw-bold"
                                onclick="return confirm('Are you sure you want to raise an EMERGENCY alert? This will notify all staff immediately.')">
                            RAISE EMERGENCY
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function updateGuardClock() {
            const el = document.getElementById('guardClock');
            if (!el) return;
            const now = new Date();
            el.textContent = now.toLocaleDateString('en-US', { weekday:'short', month:'short', day:'numeric', hour:'2-digit', minute:'2-digit', second:'2-digit' });
        }
        setInterval(updateGuardClock, 1000); updateGuardClock();

        const countdownEl = document.getElementById('shiftCountdown');
        if (countdownEl) {
            const endTimeStr = countdownEl.getAttribute('data-end');
            function updateCountdown() {
                if (!endTimeStr) return;
                const now = new Date();
                const [h, m, s] = endTimeStr.split(':');
                let end = new Date();
                end.setHours(parseInt(h), parseInt(m), parseInt(s || 0), 0);
                if (end < now && now.getHours() > 12 && parseInt(h) < 12) end.setDate(end.getDate() + 1);
                const diff = end - now;
                if (diff > 0) {
                    const hh = Math.floor(diff / 3600000);
                    const mm = Math.floor((diff % 3600000) / 60000);
                    countdownEl.textContent = `Shift ends in: ${hh}h ${mm}m`;
                } else {
                    countdownEl.textContent = 'Shift ended';
                }
            }
            setInterval(updateCountdown, 60000); updateCountdown();
        }

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
                const badge = document.getElementById('guardBellBadge');
                const count = document.getElementById('notif-count');
                if (data.unread_count > 0) { badge.classList.remove('d-none'); badge.textContent=data.unread_count; count.textContent=data.unread_count; }
                else { badge.classList.add('d-none'); badge.textContent=''; count.textContent=''; }
                if (!data.notifications || data.notifications.length === 0) { list.innerHTML='<div class="notif-empty">No new notifications</div>'; return; }
                list.innerHTML = data.notifications.map(n => `<div class="notif-item"><div>${n.message}</div><div class="notif-time">${n.time_ago}</div></div>`).join('');
            }).catch(() => {});
        }
        document.addEventListener('click', function(e) {
            const dd = document.getElementById('notif-dropdown');
            if (dd && !e.target.closest('#notif-dropdown') && !e.target.closest('[onclick="toggleNotifications()"]')) dd.classList.remove('show');
        });

        document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach((el) => new bootstrap.Tooltip(el));
        window.isOnDuty = {{ session('is_on_duty') ? 'true' : 'false' }};
    </script>
    @if(session('is_on_duty'))
        <script src="{{ asset('js/alert-sound.js') }}"></script>
    @endif
    @vite(['resources/js/app.js'])
    @stack('scripts')
    @yield('scripts')
</body>
</html>
