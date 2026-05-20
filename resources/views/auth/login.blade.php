@extends('layouts.app')

@section('styles')
<style>
    .login-container {
        min-height: calc(100vh - 76px);
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .login-card {
        width: 100%;
        max-width: 450px;
        border: none;
        border-radius: 12px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.05);
    }
    .role-tabs {
        border-bottom: 1px solid #eee;
    }
    .role-tab {
        padding: 15px 0;
        text-align: center;
        color: #6c757d;
        cursor: pointer;
        font-weight: 500;
        border-bottom: 2px solid transparent;
        transition: all 0.3s;
    }
    .role-tab:hover {
        color: var(--teal);
    }
    .role-tab.active {
        color: var(--teal);
        border-bottom-color: var(--teal);
    }
</style>
@endsection

@section('content')
<div class="login-container">
    <div class="login-card card bg-white">
        
        <div class="text-center pt-4 pb-2">
            <h3 class="fw-bold text-dark mb-0">Secure<span class="text-teal">Watch</span></h3>
            <p class="text-muted small">Sign in to your account</p>
        </div>

        <!-- Role Tabs (Visual Only) -->
        <div class="role-tabs d-flex justify-content-around px-3 mb-4">
            <div class="role-tab active flex-fill" id="tab-admin" onclick="switchTab('admin')">Admin</div>
            <div class="role-tab flex-fill" id="tab-manager" onclick="switchTab('manager')">Manager</div>
            <div class="role-tab flex-fill" id="tab-guard" onclick="switchTab('guard')">Guard</div>
        </div>

        <div class="card-body px-4 pb-4">
            
            @if(session('error'))
                <div class="alert alert-danger text-center small rounded-3">
                    <i class="bi bi-exclamation-circle me-1"></i> {{ session('error') }}
                </div>
            @endif

            @if(session('success'))
                <div class="alert alert-success text-center small rounded-3">
                    <i class="bi bi-check-circle me-1"></i> {{ session('success') }}
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}">
                @csrf
                <input type="hidden" name="login_type" id="login_type" value="admin">
                <div class="mb-3">
                    <label class="form-label text-muted small fw-semibold">Email Address</label>
                    <input type="email" id="email" name="email" class="form-control form-control-lg @error('email') is-invalid @enderror" value="{{ old('email') }}" required autofocus placeholder="Enter your email">
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-4">
                    <label class="form-label text-muted small fw-semibold">Password</label>
                    <div class="input-group">
                        <input type="password" id="password" name="password" class="form-control form-control-lg border-end-0 @error('password') is-invalid @enderror" required placeholder="Enter your password">
                        <span class="input-group-text bg-white border-start-0" id="togglePassword" style="cursor: pointer;">
                            <i class="bi bi-eye-slash text-muted" id="togglePasswordIcon"></i>
                        </span>
                    </div>
                    <small id="login-hint" style="color:#94a3b8; font-size:11px; display:block; margin-top: 4px;">Use your admin credentials</small>
                    @error('password')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>

                <button type="submit" class="btn btn-teal btn-lg w-100 rounded-3 mb-3 shadow-sm">
                    Login to Dashboard
                </button>
            </form>
            <div style="text-align:center; margin-top:16px; font-size:13px; color:#64748b">
                Don't have an account?
                <a href="/register" style="color:#1D9E75; text-decoration:none; font-weight:500">Register here</a>
            </div>
        </div>
        
        <div class="card-footer bg-transparent border-0 text-center pb-4 text-muted small">
            SecureWatch CCTV Monitoring System &copy; {{ date('Y') }}
        </div>
    </div>
</div>

<script>
const tabs = {
  admin: {
    email: 'Enter your email',
    password: 'Enter your password',
    hint: 'Use your admin credentials'
  },
  manager: {
    email: 'Enter your email',
    password: 'Enter your password',
    hint: 'Contact admin if you forgot password'
  },
  guard: {
    email: 'Enter your email',
    password: 'Enter your password',
    hint: 'Contact admin if you forgot password'
  }
};

function switchTab(role) {
  // Update active tab style
  document.querySelectorAll('.role-tab')
    .forEach(t => t.classList.remove('active'));
  document.getElementById('tab-' + role)
    .classList.add('active');

  // Update hidden input
  document.getElementById('login_type').value = role;

  // Update placeholders
  document.getElementById('email')
    .placeholder = tabs[role].email;
  document.getElementById('password')
    .placeholder = tabs[role].password;

  // Update hint text
  document.getElementById('login-hint')
    .textContent = tabs[role].hint;

}

document.getElementById('togglePassword').addEventListener('click', function () {
  const passwordInput = document.getElementById('password');
  const icon = document.getElementById('togglePasswordIcon');
  if (passwordInput.type === 'password') {
    passwordInput.type = 'text';
    icon.classList.remove('bi-eye-slash');
    icon.classList.add('bi-eye');
  } else {
    passwordInput.type = 'password';
    icon.classList.remove('bi-eye');
    icon.classList.add('bi-eye-slash');
  }
});
</script>
@endsection
