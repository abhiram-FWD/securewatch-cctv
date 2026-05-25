@extends('layouts.guard')

@section('page-title', 'Resolve Alert #' . $alert->id)

@section('content')
<div class="mb-4">
    <a href="{{ route('guard.alerts') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-2"></i>Back
    </a>
</div>

<div class="row g-4">
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
            <div class="bg-dark position-relative" style="height: 300px;">
                @if($alert->camera)
                <video src="{{ $alert->camera->stream_url }}" class="w-100 h-100 object-fit-cover" autoplay loop muted playsinline></video>
                <div class="position-absolute top-0 start-0 w-100 p-3 bg-gradient-dark">
                    <h5 class="text-white mb-0 fw-bold">{{ $alert->camera->name }}</h5>
                    <div class="small text-white-50"><i class="bi bi-geo-alt me-1"></i> {{ $alert->camera->location }}</div>
                </div>
                @else
                <div class="d-flex align-items-center justify-content-center h-100 text-white-50">
                    <i class="bi bi-camera-video-off fs-1"></i>
                </div>
                @endif
            </div>
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div>
                        @if($alert->is_emergency)
                            <span class="badge bg-danger me-1">🚨 EMERGENCY</span>
                        @endif
                        <span class="badge bg-{{ $alert->type === 'crime' ? 'danger' : ($alert->type === 'crowd' ? 'warning text-dark' : 'primary') }}">
                            {{ ucfirst($alert->type) }}
                        </span>
                    </div>
                    <div class="text-end">
                        <div class="small text-secondary fw-bold">Raised By</div>
                        <div class="fw-medium">{{ $alert->raisedBy->name ?? 'System' }}</div>
                        <div class="small text-secondary">{{ $alert->created_at->format('M d, Y h:i A') }}</div>
                    </div>
                </div>
                
                <h6 class="fw-bold mb-2">Alert Description</h6>
                <p class="mb-0 text-secondary bg-light p-3 rounded-3">{{ $alert->description }}</p>
                
                @if($alert->instruction)
                <div class="alert alert-info border-0 shadow-sm rounded-3 mt-4 mb-0 d-flex gap-3">
                    <i class="bi bi-info-circle-fill fs-4 text-info mt-1"></i>
                    <div>
                        <h6 class="fw-bold mb-1 text-info-emphasis">Manager's Instruction</h6>
                        <p class="mb-0 text-dark">{{ $alert->instruction }}</p>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
    
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm rounded-4">
            @if($alert->status === 'resolved')
                <div class="card-header bg-white border-bottom p-4">
                    <h5 class="fw-bold mb-0 text-success"><i class="bi bi-check-circle-fill me-2"></i>Alert Resolved</h5>
                    <p class="text-secondary small mb-0 mt-1">This alert has been successfully resolved.</p>
                </div>
                <div class="card-body p-4">
                    <div class="mb-3">
                        <div class="small text-secondary fw-bold mb-1">Resolved By</div>
                        <div class="fw-medium">{{ $alert->resolvedBy->name ?? 'Unknown' }}</div>
                    </div>
                    @if($alert->resolution_note)
                    <div class="mb-3">
                        <div class="small text-secondary fw-bold mb-1">Resolution Report</div>
                        <div class="bg-light p-3 rounded-3 text-secondary">{{ $alert->resolution_note }}</div>
                    </div>
                    @endif
                    <div class="d-flex justify-content-end border-top pt-4">
                        <a href="{{ route('guard.alerts') }}" class="btn btn-outline-secondary rounded-pill px-4 fw-bold">
                            <i class="bi bi-arrow-left me-1"></i> Back to Alerts
                        </a>
                    </div>
                </div>
            @else
                <div class="card-header bg-white border-bottom p-4">
                    <h5 class="fw-bold mb-0">Write Resolution Report</h5>
                    <p class="text-secondary small mb-0 mt-1">Describe exactly what happened and what action you took. Minimum 2 lines.</p>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('guard.alerts.resolve', $alert->id) }}" method="POST">
                        @csrf
                        
                        <div class="mb-4">
                            <textarea name="resolution_note" id="resolutionNote" class="form-control rounded-3 shadow-sm border-0 bg-light p-3" rows="6" minlength="20" maxlength="500" required placeholder="Example: Attended the location and found a group of 5 people. Requested them to disperse, which they did without incident. Scene is now clear..."></textarea>
                            
                            <div class="d-flex justify-content-between align-items-center mt-2">
                                <div class="small text-secondary"><i class="bi bi-exclamation-triangle me-1"></i> This action cannot be undone</div>
                                <div id="resCharCount" class="small fw-bold text-danger">0/20 minimum chars</div>
                            </div>
                        </div>

                        <div class="d-flex gap-2 justify-content-end border-top pt-4">
                            <a href="{{ route('guard.alerts') }}" class="btn btn-light rounded-pill px-4 fw-bold">Cancel</a>
                            <button type="submit" id="resolveBtn" class="btn btn-success rounded-pill px-5 fw-bold" disabled>
                                <i class="bi bi-check-circle me-1"></i> Mark as Resolved
                            </button>
                        </div>
                    </form>
                </div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const resTextarea = document.getElementById('resolutionNote');
        const resCharCount = document.getElementById('resCharCount');
        const resolveBtn = document.getElementById('resolveBtn');
        
        resTextarea.addEventListener('input', function() {
            const len = this.value.length;
            
            if (len >= 20) {
                resCharCount.classList.remove('text-danger');
                resCharCount.classList.add('text-success');
                resCharCount.innerHTML = len + '/500 chars <i class="bi bi-check-circle-fill ms-1"></i> Minimum requirement met';
                resolveBtn.removeAttribute('disabled');
            } else {
                resCharCount.classList.remove('text-success');
                resCharCount.classList.add('text-danger');
                resCharCount.textContent = len + '/20 minimum chars';
                resolveBtn.setAttribute('disabled', 'disabled');
            }
        });
    });
</script>
<style>
    .bg-gradient-dark {
        background: linear-gradient(to bottom, rgba(0,0,0,0.8) 0%, rgba(0,0,0,0) 100%);
    }
</style>
@endpush

@endsection
