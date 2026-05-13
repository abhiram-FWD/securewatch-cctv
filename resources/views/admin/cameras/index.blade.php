@extends('layouts.admin')

@section('page-title', 'Camera Management')

@section('content')
<div class="d-flex justify-content-end mb-4">
    <a href="{{ route('admin.cameras.create') }}" class="btn btn-teal"><i class="bi bi-plus-lg me-1"></i> Add New Camera</a>
</div>

<div class="camera-grid">
    @forelse($cameras as $camera)
        <div class="camera-card">
            @if($camera->status === 'active' && $camera->stream_url)
                <video src="{{ $camera->stream_url }}" autoplay muted loop playsinline></video>
            @else
                <div class="camera-placeholder d-flex h-100 align-items-center justify-content-center text-secondary">
                    <i class="bi bi-camera-video-off fs-1"></i>
                </div>
            @endif
            <span class="{{ $camera->status === 'active' ? 'camera-badge-live' : 'camera-badge-offline' }}">
                {{ $camera->status === 'active' ? 'LIVE' : 'OFFLINE' }}
            </span>
            <span class="camera-badge-num">CAM-{{ str_pad($camera->id, 3, '0', STR_PAD_LEFT) }}</span>
            <div class="camera-overlay">
                <div class="small fw-bold">{{ $camera->name }}</div>
                <div class="small text-white-50">{{ $camera->location }}</div>
            </div>
            <div class="camera-hover-btn">
                <a href="{{ route('admin.cameras.edit', $camera->id) }}" class="btn btn-sm btn-outline-light w-100">Edit Camera</a>
            </div>
        </div>
        <div class="content-card mt-2 mb-0">
            <div class="card-body-custom py-2">
                <strong class="small text-muted d-block mb-1">Assigned Managers</strong>
                @if($camera->managers->count() > 0)
                    @foreach($camera->managers as $manager)
                        <span class="badge bg-light text-dark border me-1">{{ $manager->name }}</span>
                    @endforeach
                @else
                    <span class="text-muted small fst-italic">None assigned</span>
                @endif
            </div>
            <div class="card-body-custom pt-0 d-flex justify-content-end">
                <form action="{{ route('admin.cameras.delete', $camera->id) }}" method="POST" onsubmit="return confirm('Are you sure?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                </form>
            </div>
        </div>
    @empty
    <div>
        <div class="empty-state">
            <div class="empty-icon">📷</div>
            <div class="empty-title">No cameras added</div>
            <div class="empty-subtitle">Add your first camera to start monitoring</div>
        </div>
    </div>
    @endforelse
</div>

<div class="mt-4">
    {{ $cameras->links('pagination::bootstrap-5') }}
</div>
@endsection
