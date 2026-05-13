@extends('layouts.admin')

@section('page-title', 'Edit Camera: ' . $camera->name)

@section('content')
<div class="card border-0 shadow-sm">
    <div class="card-body p-4">
        <form action="{{ route('admin.cameras.update', $camera->id) }}" method="POST">
            @csrf @method('PUT')
            <div class="row g-4">
                <div class="col-md-6">
                    <label class="form-label fw-medium">Camera Name <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $camera->name) }}" required>
                    @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-medium">Location <span class="text-danger">*</span></label>
                    <input type="text" name="location" class="form-control @error('location') is-invalid @enderror" value="{{ old('location', $camera->location) }}" required>
                    @error('location')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-12">
                    <label class="form-label fw-medium">Stream URL (Video Source) <span class="text-danger">*</span></label>
                    <input type="url" name="stream_url" class="form-control @error('stream_url') is-invalid @enderror" value="{{ old('stream_url', $camera->stream_url) }}" required>
                    @error('stream_url')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-medium">Status <span class="text-danger">*</span></label>
                    <select name="status" class="form-select @error('status') is-invalid @enderror" required>
                        <option value="active" {{ old('status', $camera->status) == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ old('status', $camera->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                    @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-medium">Assign to Managers</label>
                    <div class="border rounded p-3" style="max-height: 150px; overflow-y: auto;">
                        @forelse($managers as $manager)
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="managers[]" value="{{ $manager->id }}" id="mgr_{{ $manager->id }}" 
                                {{ (old('_token') ? in_array($manager->id, old('managers', [])) : in_array($manager->id, $assigned_managers)) ? 'checked' : '' }}>
                                <label class="form-check-label" for="mgr_{{ $manager->id }}">
                                    {{ $manager->name }} <span class="text-muted small">({{ $manager->area ?? 'No specific area' }})</span>
                                </label>
                            </div>
                        @empty
                            <div class="text-muted small fst-italic">No active managers found.</div>
                        @endforelse
                    </div>
                </div>
            </div>
            <hr class="my-4">
            <div class="d-flex justify-content-end gap-2">
                <a href="{{ route('admin.cameras.index') }}" class="btn btn-outline-secondary px-4">Cancel</a>
                <button type="submit" class="btn btn-teal px-4">Update Camera</button>
            </div>
        </form>
    </div>
</div>
@endsection
