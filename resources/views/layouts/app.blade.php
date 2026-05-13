<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $system_name ?? 'SecureWatch' }} - CCTV Monitoring System</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        :root {
            --teal: #1D9E75;
            --teal-hover: #167a5a;
            --slate: #f4f7f6;
            --dark-slate: #2c3e50;
        }
        body {
            background-color: var(--slate);
            font-family: 'Inter', system-ui, sans-serif;
        }
        .text-teal { color: var(--teal) !important; }
        .bg-teal { background-color: var(--teal) !important; }
        .btn-teal {
            background-color: var(--teal);
            color: white;
            border: none;
        }
        .btn-teal:hover {
            background-color: var(--teal-hover);
            color: white;
        }
        .btn-outline-teal {
            border-color: var(--teal);
            color: var(--teal);
        }
        .btn-outline-teal:hover {
            background-color: var(--teal);
            color: white;
        }
        .navbar-brand {
            font-weight: 700;
            color: var(--dark-slate);
        }
        .navbar-brand span {
            color: var(--teal);
        }
    </style>
    @yield('styles')
</head>
<body>

    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm py-3">
        <div class="container">
            <a class="navbar-brand fs-4" href="{{ route('home') }}">
                <i class="bi bi-shield-check text-teal"></i> Secure<span>Watch</span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto mb-2 mb-lg-0 align-items-center">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('home') }}#features">Features</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('home') }}#roles">Roles</a>
                    </li>
                        <li class="nav-item ms-lg-3 d-flex align-items-center">
                            <a href="/login"
                               style="border:1.5px solid #1D9E75;
                                      color:#1D9E75;
                                      padding:8px 20px;
                                      border-radius:6px;
                                      text-decoration:none;
                                      font-size:13px;
                                      font-weight:500;
                                      margin-right:8px">
                              Login
                            </a>
                            <a href="/register"
                               style="background:#1D9E75;
                                      color:#fff;
                                      padding:8px 20px;
                                      border-radius:6px;
                                      text-decoration:none;
                                      font-size:13px;
                                      font-weight:500">
                              Register
                            </a>
                        </li>
                </ul>
            </div>
        </div>
    </nav>

    <main>
        @yield('content')
    </main>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    @yield('scripts')
</body>
</html>
