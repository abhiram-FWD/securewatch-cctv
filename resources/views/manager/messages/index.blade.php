@extends('layouts.manager')

@section('page-title', 'Messages from Admin')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0 pt-4 pb-0">
                <h5 class="fw-bold mb-0">Inbox</h5>
            </div>
            <div class="card-body p-0 mt-3">
                <div class="list-group list-group-flush border-top">
                    @forelse($messages as $msg)
                        <div class="list-group-item p-4">
                            <div class="d-flex w-100 justify-content-between align-items-center mb-3">
                                <div>
                                    @if($msg->type == 'warning') <span class="badge bg-danger fs-6"><i class="bi bi-exclamation-triangle-fill me-1"></i> Warning</span>
                                    @elseif($msg->type == 'instruction') <span class="badge bg-primary fs-6"><i class="bi bi-megaphone-fill me-1"></i> Instruction</span>
                                    @elseif($msg->type == 'laziness') <span class="badge bg-warning text-dark fs-6"><i class="bi bi-emoji-sleep-fill me-1"></i> Notice</span>
                                    @elseif($msg->type == 'appreciation') <span class="badge bg-success fs-6"><i class="bi bi-star-fill me-1"></i> Appreciation</span>
                                    @else <span class="badge bg-secondary fs-6"><i class="bi bi-info-circle-fill me-1"></i> General</span> @endif
                                </div>
                                <div class="text-end">
                                    <small class="text-muted d-block fw-medium">{{ $msg->created_at->diffForHumans() }}</small>
                                    <small class="text-muted" style="font-size:0.7rem;">{{ $msg->created_at->format('M d, H:i') }}</small>
                                </div>
                            </div>
                            <div class="bg-light p-4 rounded text-dark fs-5 mb-2">
                                {{ $msg->message }}
                            </div>
                            <div class="text-end">
                                <small class="text-muted fst-italic"><i class="bi bi-check2-all text-primary me-1"></i> Read</small>
                            </div>
                        </div>
                    @empty
                        <div class="empty-state">
                            <div class="empty-icon">📭</div>
                            <div class="empty-title">No messages yet</div>
                            <div class="empty-subtitle">Messages from admin will appear here</div>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
