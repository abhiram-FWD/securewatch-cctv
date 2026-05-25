@extends('layouts.manager')

@section('page-title', 'Alert Details')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <a href="{{ route('manager.alerts') }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-2"></i>Back to Alerts</a>
    @if($alert->status == 'open')
        <span class="badge bg-danger fs-6 px-3 py-2"><i class="bi bi-exclamation-circle me-2"></i>OPEN</span>
    @else
        <span class="badge bg-success fs-6 px-3 py-2"><i class="bi bi-check-circle me-2"></i>RESOLVED</span>
    @endif
</div>

<div class="row g-4">
    <div class="col-md-7">
        <div class="card border-0 shadow-sm mb-4">
            <div class="position-relative bg-dark rounded-top" style="aspect-ratio: 16/9; overflow: hidden;">
                @if($alert->camera && $alert->camera->stream_url)
                    <video src="{{ $alert->camera->stream_url }}" class="w-100 h-100 object-fit-cover" autoplay muted loop playsinline></video>
                    @if($alert->camera->status == 'active')
                        <span class="position-absolute top-0 end-0 m-3 badge bg-success"><i class="bi bi-record-circle me-1"></i>LIVE</span>
                    @endif
                @else
                    <div class="w-100 h-100 d-flex align-items-center justify-content-center text-muted">
                        <i class="bi bi-camera-video-off fs-1"></i>
                    </div>
                @endif
            </div>
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div>
                        <h4 class="fw-bold mb-1">{{ $alert->camera->name ?? 'Unknown Camera' }}</h4>
                        <p class="text-muted mb-0"><i class="bi bi-geo-alt-fill text-teal me-1"></i>{{ $alert->camera->location ?? 'Unknown Location' }}</p>
                    </div>
                    <div>
                        @if($alert->type == 'crowd') <span class="badge bg-warning text-dark fs-6">Crowd</span>
                        @elseif($alert->type == 'crime') <span class="badge bg-danger fs-6">Crime</span>
                        @elseif($alert->type == 'worksite') <span class="badge bg-primary fs-6">Worksite</span>
                        @elseif($alert->type == 'emergency') <span class="badge bg-dark text-danger border border-danger fs-6">Emergency</span>
                        @else <span class="badge bg-secondary fs-6">{{ ucfirst($alert->type) }}</span> @endif
                        
                        @if($alert->is_emergency) <span class="badge bg-danger ms-1 fs-6"><i class="bi bi-exclamation-triangle-fill"></i></span> @endif
                    </div>
                </div>
                
                <hr class="my-4">
                
                <h6 class="fw-bold text-muted text-uppercase small mb-2">Alert Description</h6>
                <p class="fs-5 text-dark">{{ $alert->description }}</p>
                
                <div class="d-flex align-items-center gap-4 mt-4 bg-light p-3 rounded">
                    <div>
                        <small class="text-muted d-block fw-bold">Raised By</small>
                        <span class="fw-medium">{{ $alert->raisedBy->name ?? 'System' }}</span>
                    </div>
                    <div class="border-start ps-4">
                        <small class="text-muted d-block fw-bold">Time</small>
                        <span class="fw-medium">{{ $alert->created_at->format('M d, Y h:i A') }}</span>
                        <small class="text-muted ms-1">({{ $alert->created_at->diffForHumans() }})</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-5">
        <!-- INSTRUCTION SECTION -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white border-0 pt-4 pb-0">
                <h5 class="fw-bold mb-0"><i class="bi bi-chat-square-text text-primary me-2"></i>Guard Instructions</h5>
            </div>
            <div class="card-body p-4">
                @if($alert->instruction)
                    <div class="bg-primary bg-opacity-10 text-primary p-3 rounded mb-3 border border-primary border-opacity-25">
                        <i class="bi bi-info-circle-fill me-2"></i> {{ $alert->instruction }}
                    </div>
                @endif
                
                @if($alert->status == 'open')
                    <form action="{{ route('manager.alerts.instruct', $alert->id) }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label fw-medium small text-muted">{{ $alert->instruction ? 'Update Instruction' : 'Send Instruction to Guards' }}</label>
                            <textarea name="instruction" class="form-control @error('instruction') is-invalid @enderror" rows="3" required minlength="10" placeholder="Type instructions here...">{{ old('instruction') }}</textarea>
                            @error('instruction')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            <div class="form-text">Minimum 10 characters. This will notify all on-duty guards.</div>
                        </div>
                        <button type="submit" class="btn btn-primary w-100"><i class="bi bi-send me-2"></i>{{ $alert->instruction ? 'Update Instruction' : 'Send Instruction' }}</button>
                    </form>
                @elseif(!$alert->instruction)
                    <p class="text-muted small fst-italic mb-0">No instructions were sent for this alert.</p>
                @endif
            </div>
        </div>

        <!-- RESOLUTION SECTION -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0 pt-4 pb-0">
                <h5 class="fw-bold mb-0"><i class="bi bi-check-circle text-success me-2"></i>Resolution</h5>
            </div>
            <div class="card-body p-4">
                @if($alert->status == 'open')
                    @if($alert->raisedBy && $alert->raisedBy->role === 'manager')
                        <div class="d-flex flex-column align-items-center text-center p-4 rounded" style="border: 2px solid #E24B4A; background-color: rgba(226, 75, 74, 0.04); box-shadow: 0 0 15px rgba(226, 75, 74, 0.3);">
                            <i class="bi bi-shield-fill-exclamation text-danger fs-3 mb-2"></i>
                            <h6 class="fw-bold text-danger mb-0">Pending Guard Resolution</h6>
                        </div>
                    @else
                        <form action="{{ route('manager.alerts.resolve', $alert->id) }}" method="POST" id="resolveForm">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label fw-medium small text-muted">What happened and what action was taken?</label>
                                <textarea name="resolution_note" id="resolution_note" class="form-control @error('resolution_note') is-invalid @enderror" rows="4" required placeholder="Detail the resolution here..."></textarea>
                                @error('resolution_note')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                <div class="d-flex justify-content-between mt-1">
                                    <div class="form-text">Minimum 20 characters required.</div>
                                    <small class="text-muted" id="charCount">0/20</small>
                                </div>
                            </div>
                            <button type="submit" id="resolveBtn" class="btn btn-success w-100" disabled><i class="bi bi-check-circle me-2"></i>Mark as Resolved</button>
                        </form>
                    @endif
                @else
                    <div class="bg-success bg-opacity-10 text-success p-4 rounded border border-success border-opacity-25 mb-3">
                        <p class="mb-0 fw-medium">{{ $alert->resolution_note }}</p>
                    </div>
                    <div class="d-flex align-items-center gap-2 text-muted small">
                        <i class="bi bi-person-check-fill"></i> Resolved by {{ $alert->resolvedBy->name ?? 'Unknown' }} on {{ $alert->updated_at->format('M d, Y h:i A') }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

@if($alert->status == 'open' && !($alert->raisedBy && $alert->raisedBy->role === 'manager'))
<script>
    document.getElementById('resolution_note').addEventListener('input', function() {
        const len = this.value.length;
        document.getElementById('charCount').textContent = len + '/20';
        if(len >= 20) {
            document.getElementById('charCount').classList.replace('text-danger', 'text-success');
            document.getElementById('resolveBtn').disabled = false;
        } else {
            document.getElementById('charCount').classList.add('text-danger');
            document.getElementById('charCount').classList.remove('text-success');
            document.getElementById('resolveBtn').disabled = true;
        }
    });
</script>
@endif
@endsection
