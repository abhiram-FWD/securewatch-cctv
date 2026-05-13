@extends('layouts.guard')

@section('page-title', 'Raise Alert')

@section('content')
    <div class="mb-4">
        <a href="{{ route('guard.dashboard') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-2"></i>Back to Dashboard
        </a>
    </div>
    <div class="row justify-content-center">
        <div class="col-md-8">
            
            @if(!session('is_on_duty'))
                <div class="card border-danger shadow-sm">
                    <div class="card-body text-center p-5">
                        <i class="bi bi-shield-lock text-danger fs-1 mb-3"></i>
                        <h4 class="fw-bold text-danger">Action Denied</h4>
                        <p class="text-muted fs-5 mb-0">You are currently off duty.</p>
                        <p class="text-muted mb-4">You cannot raise alerts until your shift begins.</p>
                        <a href="{{ route('guard.dashboard') }}" class="btn btn-outline-secondary">Return to Dashboard</a>
                    </div>
                </div>
            @else
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-white border-bottom py-3 d-flex align-items-center">
                        <i class="bi bi-exclamation-triangle text-warning fs-4 me-2"></i>
                        <h5 class="mb-0 fw-bold">Report New Issue</h5>
                    </div>
                    <div class="card-body p-4">
                        <form action="{{ route('guard.alerts.raise') }}" method="POST">
                            @csrf
                            
                            <div class="mb-4">
                                <label class="form-label fw-bold text-secondary">Camera Location <span class="text-danger">*</span></label>
                                <select name="camera_id" class="form-select form-select-lg" required>
                                    <option value="">Select Camera...</option>
                                    @foreach(\App\Models\Camera::where('status', 'active')->get() as $camera)
                                        <option value="{{ $camera->id }}" {{ request('camera_id') == $camera->id ? 'selected' : '' }}>
                                            {{ $camera->name }} ({{ $camera->location }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="mb-4">
                                <label class="form-label fw-bold text-secondary d-block">Alert Type <span class="text-danger">*</span></label>
                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <input type="radio" class="btn-check" name="type" id="type_crowd" value="crowd" required>
                                        <label class="btn btn-outline-warning w-100 text-start p-3 border-2 h-100 d-flex flex-column" for="type_crowd">
                                            <i class="bi bi-people fs-3 mb-2"></i>
                                            <span class="fw-bold">Crowd Issue</span>
                                        </label>
                                    </div>
                                    <div class="col-md-4">
                                        <input type="radio" class="btn-check" name="type" id="type_crime" value="crime" required>
                                        <label class="btn btn-outline-danger w-100 text-start p-3 border-2 h-100 d-flex flex-column" for="type_crime">
                                            <i class="bi bi-exclamation-octagon fs-3 mb-2"></i>
                                            <span class="fw-bold">Crime/Suspicious</span>
                                        </label>
                                    </div>
                                    <div class="col-md-4">
                                        <input type="radio" class="btn-check" name="type" id="type_worksite" value="worksite" required>
                                        <label class="btn btn-outline-info w-100 text-start p-3 border-2 h-100 d-flex flex-column" for="type_worksite">
                                            <i class="bi bi-cone-striped fs-3 mb-2"></i>
                                            <span class="fw-bold">Worksite Issue</span>
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-4">
                                <label class="form-label fw-bold text-secondary">Description <span class="text-danger">*</span></label>
                                <textarea name="description" id="alert_description" class="form-control" rows="4" required minlength="10" placeholder="Provide details about what you see..."></textarea>
                                <div class="d-flex justify-content-between mt-1">
                                    <small class="text-muted">Minimum 10 characters.</small>
                                    <small id="char_count" class="text-danger fw-bold">0 / 10</small>
                                </div>
                            </div>

                            <div class="d-flex justify-content-end gap-2 mt-5 border-top pt-4">
                                <a href="{{ route('guard.alerts') }}" class="btn btn-light border px-4">Cancel</a>
                                <button type="submit" id="submit_btn" class="btn btn-teal px-4 fw-bold" disabled>Raise Alert</button>
                            </div>
                        </form>
                    </div>
                </div>
            @endif
            
        </div>
    </div>
@endsection

@section('scripts')
@if(session('is_on_duty'))
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const descInput = document.getElementById('alert_description');
        const charCount = document.getElementById('char_count');
        const submitBtn = document.getElementById('submit_btn');
        
        if(descInput) {
            descInput.addEventListener('input', function() {
                const len = this.value.length;
                charCount.textContent = len + ' / 10';
                
                if (len >= 10) {
                    charCount.classList.remove('text-danger');
                    charCount.classList.add('text-success');
                    submitBtn.disabled = false;
                } else {
                    charCount.classList.remove('text-success');
                    charCount.classList.add('text-danger');
                    submitBtn.disabled = true;
                }
            });
        }
    });
</script>
@endif
@endsection
