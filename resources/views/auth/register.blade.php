<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register | SecureWatch</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css'])
</head>
<body class="bg-light">
    <div class="container">
        <div class="row justify-content-center align-items-center min-vh-100 py-5">
            <div class="col-md-6 col-lg-5">
                <div class="text-center mb-4">
                    <div class="d-inline-flex align-items-center justify-content-center bg-teal text-white rounded-circle mb-3" style="width:60px;height:60px;font-size:28px;">
                        <i class="bi bi-shield-check"></i>
                    </div>
                    <h2 class="fw-bold">Secure<span class="text-teal">Watch</span></h2>
                    <p class="text-muted">Create a new account</p>
                </div>

                <div class="card border-0 shadow-sm">
                    <div class="card-body p-4">
                        <div class="alert alert-info d-flex align-items-start mb-4 border-0 bg-light-blue text-primary">
                            <i class="bi bi-info-circle-fill me-3 fs-5 mt-1"></i>
                            <div>
                                <strong>Registration requires admin approval.</strong><br>
                                <span class="small">You will be able to login once your account is approved by the administrator.</span>
                            </div>
                        </div>

                        @if ($errors->any())
                            <div class="alert alert-danger border-0">
                                <ul class="mb-0 ps-3">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form method="POST" action="{{ route('register') }}">
                            @csrf
                            <div class="mb-3">
                                <label for="name" class="form-label text-muted small fw-bold">FULL NAME</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0"><i class="bi bi-person text-muted"></i></span>
                                    <input type="text" class="form-control bg-light border-start-0 ps-0" id="name" name="name" value="{{ old('name') }}" required autofocus>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="email" class="form-label text-muted small fw-bold">EMAIL ADDRESS</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0"><i class="bi bi-envelope text-muted"></i></span>
                                    <input type="email" class="form-control bg-light border-start-0 ps-0" id="email" name="email" value="{{ old('email') }}" required>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="password" class="form-label text-muted small fw-bold">PASSWORD</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0"><i class="bi bi-lock text-muted"></i></span>
                                    <input type="password" class="form-control bg-light border-start-0 border-end-0 ps-0" id="password" name="password" required>
                                    <span class="input-group-text bg-light border-start-0" id="togglePassword" style="cursor: pointer;">
                                        <i class="bi bi-eye-slash text-muted" id="togglePasswordIcon"></i>
                                    </span>
                                </div>
                                <div class="form-text small">Must be at least 8 chars, contain an uppercase letter & number.</div>
                            </div>

                            <div class="mb-3">
                                <label for="password_confirmation" class="form-label text-muted small fw-bold">CONFIRM PASSWORD</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0"><i class="bi bi-lock-fill text-muted"></i></span>
                                    <input type="password" class="form-control bg-light border-start-0 border-end-0 ps-0" id="password_confirmation" name="password_confirmation" required>
                                    <span class="input-group-text bg-light border-start-0" id="toggleConfirmPassword" style="cursor: pointer;">
                                        <i class="bi bi-eye-slash text-muted" id="toggleConfirmPasswordIcon"></i>
                                    </span>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="role" class="form-label text-muted small fw-bold">ROLE</label>
                                <select class="form-select bg-light" id="role" name="role" required>
                                    <option value="">Select Role...</option>
                                    @foreach($roles as $val => $label)
                                        <option value="{{ $val }}" {{ old('role') == $val ? 'selected' : '' }}>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div id="shiftField" style="display:none">
                                <div class="mb-3">
                                    <label for="shift" class="form-label text-muted small fw-bold">SHIFT</label>
                                    <select class="form-select bg-light" id="shift" name="shift">
                                        <option value="">Select Shift...</option>
                                        <option value="morning" {{ old('shift') == 'morning' ? 'selected' : '' }}>Morning Shift (6AM - 6PM)</option>
                                        <option value="night" {{ old('shift') == 'night' ? 'selected' : '' }}>Night Shift (6PM - 6AM)</option>
                                    </select>
                                </div>
                            </div>

                            <div class="mb-4">
                                <label for="area" class="form-label text-muted small fw-bold">AREA (OPTIONAL)</label>
                                <input type="text" class="form-control bg-light" id="area" name="area" value="{{ old('area') }}" placeholder="Your assigned area (optional)">
                            </div>

                            <button type="submit" class="btn btn-teal w-100 py-2 fw-bold mb-3">Submit Registration Request</button>
                            
                            <div class="text-center">
                                <a href="{{ route('login') }}" class="text-decoration-none small text-teal">Already have an account? Login</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        var roleSelect = document.getElementById('role');
        var shiftDiv = document.getElementById('shiftField');

        function toggleShift() {
            if(roleSelect.value === 'guard') {
                shiftDiv.style.display = 'block';
            } else {
                shiftDiv.style.display = 'none';
            }
        }

        roleSelect.addEventListener('change', toggleShift);
        toggleShift(); // run on page load

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

        document.getElementById('toggleConfirmPassword').addEventListener('click', function () {
            const passwordInput = document.getElementById('password_confirmation');
            const icon = document.getElementById('toggleConfirmPasswordIcon');
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
</body>
</html>
