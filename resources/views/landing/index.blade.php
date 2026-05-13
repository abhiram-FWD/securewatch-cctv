@extends('layouts.app')

@section('styles')
<style>
    .hero-section {
        padding: 80px 0 100px;
        background-color: var(--slate);
    }
    .hero-pill {
        background-color: rgba(29, 158, 117, 0.1);
        color: var(--teal);
        font-weight: 600;
        font-size: 0.85rem;
    }
    .fake-camera-grid {
        background: #111;
        border-radius: 12px;
        padding: 10px;
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 10px;
        box-shadow: 0 20px 40px rgba(0,0,0,0.15);
    }
    .camera-box {
        background: #222;
        border-radius: 8px;
        aspect-ratio: 16/9;
        position: relative;
        overflow: hidden;
        border: 1px solid #333;
    }
    .camera-label {
        position: absolute;
        bottom: 10px;
        left: 10px;
        color: white;
        font-size: 0.75rem;
        background: rgba(0,0,0,0.6);
        padding: 4px 8px;
        border-radius: 4px;
    }
    .live-dot {
        position: absolute;
        top: 10px;
        right: 10px;
        width: 8px;
        height: 8px;
        background-color: #ff3b30;
        border-radius: 50%;
        animation: pulse 1.5s infinite;
    }
    .camera-bg {
        width: 100%;
        height: 100%;
        object-fit: cover;
        opacity: 0.6;
    }
    @keyframes pulse {
        0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(255, 59, 48, 0.7); }
        70% { transform: scale(1); box-shadow: 0 0 0 6px rgba(255, 59, 48, 0); }
        100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(255, 59, 48, 0); }
    }
    .stats-bar {
        border-top: 1px solid #e2e8f0;
        border-bottom: 1px solid #e2e8f0;
        background: white;
    }
    .feature-card {
        border: none;
        background: white;
        border-radius: 12px;
        transition: transform 0.3s, box-shadow 0.3s;
    }
    .feature-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 25px rgba(0,0,0,0.05);
    }
    .feature-icon {
        width: 50px;
        height: 50px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 12px;
        background-color: rgba(29, 158, 117, 0.1);
        color: var(--teal);
        font-size: 1.5rem;
        margin-bottom: 1rem;
    }
    .roles-section {
        background-color: var(--slate);
    }
    .role-card {
        border: none;
        border-radius: 12px;
        height: 100%;
    }
    .role-card.highlight {
        border: 2px solid var(--teal);
        box-shadow: 0 15px 30px rgba(29, 158, 117, 0.1);
    }
</style>
@endsection

@section('content')

<!-- Hero Section -->
<section class="hero-section">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6 mb-5 mb-lg-0">
                <span class="badge rounded-pill hero-pill px-3 py-2 mb-3">Real-time CCTV Monitoring</span>
                <h1 class="display-4 fw-bold text-dark mb-4">Protect every corner.<br><span class="text-teal">In real time.</span></h1>
                <p class="lead text-muted mb-4 pe-lg-5">SecureWatch empowers your security teams with intelligent crowd control, worksite safety monitoring, and crime prevention alerts in one unified platform.</p>
                <div class="d-flex gap-3">
                    <a href="{{ route('login') }}" class="btn btn-teal btn-lg px-4 rounded-pill shadow-sm">Access Dashboard</a>
                    <a href="#features" class="btn btn-outline-secondary btn-lg px-4 rounded-pill bg-white">Learn More</a>
                </div>
            </div>
            <div class="col-lg-6">
                <div style="display:grid;
                     grid-template-columns:1fr 1fr;
                     gap:8px;
                     background:#1a2332;
                     padding:12px;
                     border-radius:10px">

                  {{-- Camera 1 - Lobby --}}
                  <div style="position:relative;
                       border-radius:6px;
                       overflow:hidden;
                       aspect-ratio:16/9;
                       background:#0f1723">
                    <img src="https://images.unsplash.com/photo-1497366216548-37526070297c?w=400&q=80"
                         style="width:100%;
                                height:100%;
                                object-fit:cover;
                                opacity:0.8">
                    <div style="position:absolute;
                         bottom:0; left:0; right:0;
                         background:rgba(0,0,0,0.6);
                         padding:4px 8px">
                      <span style="color:#fff;
                             font-size:10px">
                        📷 Lobby Cam
                      </span>
                    </div>
                    <div style="position:absolute;
                         top:6px; right:6px;
                         width:8px; height:8px;
                         border-radius:50%;
                         background:#22c55e">
                    </div>
                  </div>

                  {{-- Camera 2 - Gate A --}}
                  <div style="position:relative;
                       border-radius:6px;
                       overflow:hidden;
                       aspect-ratio:16/9;
                       background:#0f1723">
                    <img src="https://images.unsplash.com/photo-1558618666-fcd25c85cd64?w=400&q=80"
                         style="width:100%;
                                height:100%;
                                object-fit:cover;
                                opacity:0.8">
                    <div style="position:absolute;
                         bottom:0; left:0; right:0;
                         background:rgba(0,0,0,0.6);
                         padding:4px 8px">
                      <span style="color:#fff;
                             font-size:10px">
                        📷 Gate A
                      </span>
                    </div>
                    <div style="position:absolute;
                         top:6px; right:6px;
                         width:8px; height:8px;
                         border-radius:50%;
                         background:#22c55e">
                    </div>
                  </div>

                  {{-- Camera 3 - Worksite --}}
                  <div style="position:relative;
                       border-radius:6px;
                       overflow:hidden;
                       aspect-ratio:16/9;
                       background:#0f1723">
                    <img src="https://images.unsplash.com/photo-1504307651254-35680f356dfd?w=400&q=80"
                         style="width:100%;
                                height:100%;
                                object-fit:cover;
                                opacity:0.8">
                    <div style="position:absolute;
                         bottom:0; left:0; right:0;
                         background:rgba(0,0,0,0.6);
                         padding:4px 8px">
                      <span style="color:#fff;
                             font-size:10px">
                        📷 Worksite Zone
                      </span>
                    </div>
                    <div style="position:absolute;
                         top:6px; right:6px;
                         width:8px; height:8px;
                         border-radius:50%;
                         background:#22c55e">
                    </div>
                  </div>

                  {{-- Camera 4 - Parking Alert --}}
                  <div style="position:relative;
                       border-radius:6px;
                       overflow:hidden;
                       aspect-ratio:16/9;
                       background:#0f1723">
                    <img src="https://images.unsplash.com/photo-1506521781263-d8422e82f27a?w=400&q=80"
                         style="width:100%;
                                height:100%;
                                object-fit:cover;
                                opacity:0.8">
                    <div style="position:absolute;
                         bottom:0; left:0; right:0;
                         background:rgba(0,0,0,0.6);
                         padding:4px 8px">
                      <span style="color:#EF9F27;
                             font-size:10px">
                        ⚠️ Parking (Alert)
                      </span>
                    </div>
                    <div style="position:absolute;
                         top:6px; right:6px;
                         width:8px; height:8px;
                         border-radius:50%;
                         background:#E24B4A">
                    </div>
                  </div>

                </div>
            </div>
        </div>
    </div>
</section>

<!-- Stats Bar -->
<section class="stats-bar py-4">
    <div class="container">
        <div class="row text-center">
            <div class="col-6 col-md-3 mb-3 mb-md-0">
                <h3 class="fw-bold text-teal mb-0">{{ $total_cameras ?? 8 }}</h3>
                <span class="text-muted small text-uppercase fw-semibold">Active Cameras</span>
            </div>
            <div class="col-6 col-md-3 mb-3 mb-md-0">
                <h3 class="fw-bold text-teal mb-0">{{ $total_users ?? 8 }}</h3>
                <span class="text-muted small text-uppercase fw-semibold">Total Users</span>
            </div>
            <div class="col-6 col-md-3">
                <h3 class="fw-bold text-teal mb-0">3</h3>
                <span class="text-muted small text-uppercase fw-semibold">Guard Shifts</span>
            </div>
            <div class="col-6 col-md-3">
                <h3 class="fw-bold text-teal mb-0">24/7</h3>
                <span class="text-muted small text-uppercase fw-semibold">Monitoring</span>
            </div>
        </div>
    </div>
</section>

<!-- Features Section -->
<section id="features" class="py-5 bg-white">
    <div class="container py-5">
        <div class="text-center mb-5">
            <h2 class="fw-bold text-dark">Everything your team needs</h2>
            <p class="text-muted">A comprehensive toolset designed for modern security operations.</p>
        </div>
        
        <div class="row g-4">
            <!-- Feature 1 -->
            <div class="col-md-4">
                <div class="card feature-card p-4 h-100 shadow-sm border-0">
                    <div class="feature-icon"><i class="bi bi-camera-video"></i></div>
                    <h5 class="fw-bold">Live Camera Feeds</h5>
                    <p class="text-muted small mb-0">Stream high-quality video feeds directly to your browser with low latency and real-time status indicators.</p>
                </div>
            </div>
            <!-- Feature 2 -->
            <div class="col-md-4">
                <div class="card feature-card p-4 h-100 shadow-sm border-0">
                    <div class="feature-icon"><i class="bi bi-bell-fill text-danger"></i></div>
                    <h5 class="fw-bold">Instant Sound Alerts</h5>
                    <p class="text-muted small mb-0">Get immediately notified of emergencies, crowd crushes, or crimes with browser push notifications and sound alarms.</p>
                </div>
            </div>
            <!-- Feature 3 -->
            <div class="col-md-4">
                <div class="card feature-card p-4 h-100 shadow-sm border-0">
                    <div class="feature-icon"><i class="bi bi-shield-lock text-primary"></i></div>
                    <h5 class="fw-bold">Role-Based Access</h5>
                    <p class="text-muted small mb-0">Granular permissions powered by Spatie. Admins configure, Managers oversee zones, and Guards report incidents.</p>
                </div>
            </div>
            <!-- Feature 4 -->
            <div class="col-md-4">
                <div class="card feature-card p-4 h-100 shadow-sm border-0">
                    <div class="feature-icon"><i class="bi bi-clock-history text-warning"></i></div>
                    <h5 class="fw-bold">Shift Management</h5>
                    <p class="text-muted small mb-0">Automatically track Guard and Manager duties with Morning, Day, and Night shifts to ensure 24/7 coverage.</p>
                </div>
            </div>
            <!-- Feature 5 -->
            <div class="col-md-4">
                <div class="card feature-card p-4 h-100 shadow-sm border-0">
                    <div class="feature-icon"><i class="bi bi-chat-left-dots text-info"></i></div>
                    <h5 class="fw-bold">Admin Messaging</h5>
                    <p class="text-muted small mb-0">Direct communication channel for Admins to send instructions or warnings to specific guards or entire teams.</p>
                </div>
            </div>
            <!-- Feature 6 -->
            <div class="col-md-4">
                <div class="card feature-card p-4 h-100 shadow-sm border-0">
                    <div class="feature-icon"><i class="bi bi-file-earmark-bar-graph text-success"></i></div>
                    <h5 class="fw-bold">Reports & Logs</h5>
                    <p class="text-muted small mb-0">Every action is tracked. Generate PDF reports of all alerts and user activities for compliance and review.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Roles Section -->
<section id="roles" class="roles-section py-5">
    <div class="container py-5">
        <div class="text-center mb-5">
            <h2 class="fw-bold text-dark">Three roles. One system.</h2>
            <p class="text-muted">A structured hierarchy to manage incidents efficiently.</p>
        </div>

        <div class="row g-4 justify-content-center">
            <!-- Guard -->
            <div class="col-md-4">
                <div class="card role-card p-4 shadow-sm bg-white">
                    <div class="text-center mb-4">
                        <div class="d-inline-block p-3 rounded-circle bg-light mb-3">
                            <i class="bi bi-person-badge fs-2 text-secondary"></i>
                        </div>
                        <h4 class="fw-bold">Guard</h4>
                        <p class="text-muted small">The boots on the ground.</p>
                    </div>
                    <ul class="list-unstyled small text-muted">
                        <li class="mb-2"><i class="bi bi-check2 text-teal me-2"></i> Assigned to specific areas</li>
                        <li class="mb-2"><i class="bi bi-check2 text-teal me-2"></i> Raises alerts when incidents occur</li>
                        <li class="mb-2"><i class="bi bi-check2 text-teal me-2"></i> Resolves alerts with notes</li>
                        <li class="mb-2"><i class="bi bi-check2 text-teal me-2"></i> Receives direct messages</li>
                    </ul>
                </div>
            </div>
            <!-- Manager -->
            <div class="col-md-4">
                <div class="card role-card highlight p-4 bg-white position-relative">
                    <div class="position-absolute top-0 start-50 translate-middle badge rounded-pill bg-teal px-3 py-2 shadow-sm">
                        Command Center
                    </div>
                    <div class="text-center mb-4 mt-3">
                        <div class="d-inline-block p-3 rounded-circle bg-light mb-3">
                            <i class="bi bi-headset fs-2 text-teal"></i>
                        </div>
                        <h4 class="fw-bold">Manager</h4>
                        <p class="text-muted small">Oversees zones and coordinates guards.</p>
                    </div>
                    <ul class="list-unstyled small text-muted">
                        <li class="mb-2"><i class="bi bi-check2 text-teal me-2"></i> Monitors specific assigned cameras</li>
                        <li class="mb-2"><i class="bi bi-check2 text-teal me-2"></i> Reviews alerts raised by guards</li>
                        <li class="mb-2"><i class="bi bi-check2 text-teal me-2"></i> Sends instructions to guards</li>
                        <li class="mb-2"><i class="bi bi-check2 text-teal me-2"></i> Oversees Crowd, Crime, or Worksite</li>
                    </ul>
                </div>
            </div>
            <!-- Admin -->
            <div class="col-md-4">
                <div class="card role-card p-4 shadow-sm bg-white">
                    <div class="text-center mb-4">
                        <div class="d-inline-block p-3 rounded-circle bg-light mb-3">
                            <i class="bi bi-shield-lock fs-2 text-dark"></i>
                        </div>
                        <h4 class="fw-bold">Admin</h4>
                        <p class="text-muted small">System administrator and owner.</p>
                    </div>
                    <ul class="list-unstyled small text-muted">
                        <li class="mb-2"><i class="bi bi-check2 text-teal me-2"></i> Manages all users and cameras</li>
                        <li class="mb-2"><i class="bi bi-check2 text-teal me-2"></i> Views complete system logs</li>
                        <li class="mb-2"><i class="bi bi-check2 text-teal me-2"></i> Downloads PDF reports</li>
                        <li class="mb-2"><i class="bi bi-check2 text-teal me-2"></i> Broadcasts system-wide messages</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Footer -->
<footer class="bg-white py-4 border-top">
    <div class="container text-center">
        <h5 class="fw-bold text-dark mb-1"><i class="bi bi-shield-check text-teal"></i> Secure<span class="text-teal">Watch</span></h5>
        <p class="text-muted small mb-0">CCTV Monitoring System &middot; MVC Laravel Project &copy; {{ date('Y') }}</p>
    </div>
</footer>

@endsection
