@extends('layouts.admin')

@section('page-title', 'Add New User')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-body p-4">
                <form method="POST" action="{{ route('admin.users.store') }}">
                    @csrf
                    <div class="row g-3">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-medium">Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required>
                            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-medium">Email <span class="text-danger">*</span></label>
                            <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" required>
                            @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-medium">Password <span class="text-danger">*</span></label>
                            <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" required minlength="6">
                            @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-medium">Role <span class="text-danger">*</span></label>
                            <select name="role" id="role-select" class="form-select @error('role') is-invalid @enderror" required>
                                <option value="" disabled selected>Select Role</option>
                                <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                                <option value="manager" {{ old('role') == 'manager' ? 'selected' : '' }}>Manager</option>
                                <option value="guard" {{ old('role') == 'guard' ? 'selected' : '' }}>Guard</option>
                            </select>
                            @error('role')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-6 mb-3" id="shift-container" style="display: none;">
                            <label class="form-label fw-medium">Shift</label>
                            <select name="shift" class="form-select @error('shift') is-invalid @enderror">
                                <option value="" disabled selected>Select Shift</option>
                                <option value="morning" {{ old('shift') == 'morning' ? 'selected' : '' }}>Morning Shift</option>
                                <option value="day" {{ old('shift') == 'day' ? 'selected' : '' }}>Day Shift</option>
                                <option value="night" {{ old('shift') == 'night' ? 'selected' : '' }}>Night Shift</option>
                            </select>
                            @error('shift')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-6 mb-3" id="area-container" style="display: none;">
                            <label class="form-label fw-medium">Assigned Area</label>
                            <input type="text" name="area" class="form-control @error('area') is-invalid @enderror" value="{{ old('area') }}">
                            @error('area')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-medium">Status <span class="text-danger">*</span></label>
                            <select name="status" class="form-select @error('status') is-invalid @enderror" required>
                                <option value="active" {{ old('status', 'active') == 'active' ? 'selected' : '' }}>Active</option>
                                <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                            </select>
                            @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-12 mb-3" id="cameraSection" style="display:none">
                          <label class="form-label fw-medium">Assign Cameras</label>
                          <div style="display:grid; grid-template-columns: 1fr 1fr; gap:8px; max-height:200px; overflow-y:auto; border:1px solid #e2e8f0; border-radius:6px; padding:10px">
                            @foreach($cameras as $camera)
                              <label style="display:flex; align-items:center; gap:8px; font-size:13px">
                                <input type="checkbox" name="cameras[]" value="{{ $camera->id }}">
                                {{ $camera->name }}
                                <small style="color:#94a3b8">{{ $camera->location }}</small>
                              </label>
                            @endforeach
                          </div>
                        </div>
                    </div>
                    <hr class="my-4">
                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary px-4">Cancel</a>
                        <button type="submit" class="btn btn-teal px-4">Create User</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const roleSelect = document.getElementById('role-select');
        const shiftContainer = document.getElementById('shift-container');
        const areaContainer = document.getElementById('area-container');
        const cameraSection = document.getElementById('cameraSection');

        function toggleFields() {
            const role = roleSelect.value;
            
            if (role === 'manager') {
                shiftContainer.style.display = 'block';
                areaContainer.style.display = 'block';
                cameraSection.style.display = 'block';
            } else if (role === 'guard') {
                shiftContainer.style.display = 'block';
                areaContainer.style.display = 'block';
                cameraSection.style.display = 'none';
            } else {
                shiftContainer.style.display = 'none';
                areaContainer.style.display = 'none';
                cameraSection.style.display = 'none';
            }
        }

        roleSelect.addEventListener('change', toggleFields);
        toggleFields();
    });
</script>
@endsection
