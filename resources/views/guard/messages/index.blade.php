@extends('layouts.guard')

@section('page-title', 'Messages')

@section('content')

<div class="row">
    <div class="col-lg-10 mx-auto">
        <div class="d-flex justify-content-between align-items-end mb-4">
            <div>
                <h4 class="fw-bold mb-1">Messages</h4>
                <div class="text-secondary">Messages and instructions from Admin/Managers</div>
            </div>
        </div>

        <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
            <div class="card-body p-0">
                @forelse($messages as $msg)
                    @php
                        $borderClass = 'secondary';
                        $iconClass = 'bi-chat-left-text';
                        
                        if($msg->type === 'warning') {
                            $borderClass = 'danger';
                            $iconClass = 'bi-exclamation-octagon';
                        } elseif($msg->type === 'instruction') {
                            $borderClass = 'info';
                            $iconClass = 'bi-info-circle';
                        } elseif($msg->type === 'laziness') {
                            $borderClass = 'warning';
                            $iconClass = 'bi-person-x';
                        } elseif($msg->type === 'appreciation') {
                            $borderClass = 'success';
                            $iconClass = 'bi-star';
                        }
                    @endphp
                    
                    <div class="p-4 border-bottom position-relative" style="border-left: 5px solid var(--bs-{{ $borderClass }}) !important; background-color: {{ $msg->created_at->diffInMinutes(now()) < 5 ? 'rgba(var(--bs-'.$borderClass.'-rgb), 0.05)' : 'white' }};">
                        
                        @if($msg->created_at->diffInMinutes(now()) < 5)
                            <span class="position-absolute top-0 end-0 mt-3 me-3 badge bg-danger rounded-pill">NEW</span>
                        @endif
                        
                        <div class="d-flex gap-3">
                            <div class="bg-{{ $borderClass }} bg-opacity-10 text-{{ $borderClass }} rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width: 48px; height: 48px;">
                                <i class="bi {{ $iconClass }} fs-4"></i>
                            </div>
                            
                            <div class="flex-grow-1">
                                <div class="d-flex justify-content-between align-items-start mb-1">
                                    <div>
                                        <span class="badge bg-{{ $borderClass }} mb-2">{{ ucfirst($msg->type) }}</span>
                                        <h6 class="fw-bold mb-0">From: {{ $msg->sender->name ?? 'Management' }}</h6>
                                    </div>
                                    <small class="text-secondary fw-medium">{{ $msg->created_at->diffForHumans() }}</small>
                                </div>
                                <div class="p-3 bg-light rounded-3 mt-2 text-dark" style="white-space: pre-wrap;">{{ $msg->message }}</div>
                            </div>
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

@endsection
