@extends('layouts.admin')

@section('page-title', 'Send Message')

@section('content')
<div class="row g-4">
    <!-- Send Message Form -->
    <div class="col-md-5">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white border-0 pt-4 pb-0">
                <h5 class="fw-bold mb-0">New Broadcast Message</h5>
            </div>
            <div class="card-body p-4">
                <form action="{{ route('admin.messages.send') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label fw-medium small text-muted">To:</label>
                        <select name="to_type" id="to_type" class="form-select @error('to_type') is-invalid @enderror" required>
                            <option value="all">Everyone (All Staff)</option>
                            <option value="role">Specific Role</option>
                            <option value="specific">Specific Person</option>
                        </select>
                    </div>

                    <div class="mb-3 d-none" id="role_select_div">
                        <label class="form-label fw-medium small text-muted">Select Role:</label>
                        <select name="to_role" class="form-select">
                            <option value="guard">All Guards</option>
                            <option value="manager">All Managers</option>
                        </select>
                    </div>

                    <div class="mb-3 d-none" id="person_select_div">
                        <label class="form-label fw-medium small text-muted">Select User:</label>
                        <select name="to_user_id" class="form-select">
                            @foreach($all_users as $u)
                                <option value="{{ $u->id }}">{{ $u->name }} ({{ ucfirst($u->role) }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-medium small text-muted">Message Type:</label>
                        <select name="type" class="form-select @error('type') is-invalid @enderror" required>
                            <option value="general">📋 General Update</option>
                            <option value="instruction">📢 Instruction</option>
                            <option value="warning">⚠️ Warning</option>
                            <option value="laziness">😴 Laziness Warning</option>
                            <option value="appreciation">✅ Appreciation</option>
                        </select>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-medium small text-muted">Message Content:</label>
                        <textarea name="message" class="form-control @error('message') is-invalid @enderror" rows="4" required minlength="10" placeholder="Type your message here..."></textarea>
                        @error('message')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <button type="submit" class="btn btn-teal w-100 shadow-sm"><i class="bi bi-send me-2"></i>Send Message</button>
                </form>
            </div>
        </div>
    </div>

    <!-- Sent Messages List -->
    <div class="col-md-7">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white border-0 pt-4 pb-0">
                <h5 class="fw-bold mb-0">Sent Messages</h5>
            </div>
            <div class="card-body p-0 mt-3">
                <div class="list-group list-group-flush border-top">
                    @forelse($sent_messages as $msg)
                        <div class="list-group-item p-4">
                            <div class="d-flex w-100 justify-content-between align-items-center mb-2">
                                <div>
                                    @if($msg->type == 'warning') <span class="badge bg-danger">⚠️ Warning</span>
                                    @elseif($msg->type == 'instruction') <span class="badge bg-primary">📢 Instruction</span>
                                    @elseif($msg->type == 'laziness') <span class="badge bg-warning text-dark">😴 Laziness</span>
                                    @elseif($msg->type == 'appreciation') <span class="badge bg-success">✅ Appreciation</span>
                                    @else <span class="badge bg-secondary">📋 General</span> @endif
                                    
                                    <span class="ms-2 small fw-bold text-dark">To: {{ $msg->receiver->name ?? 'All' }}</span>
                                </div>
                                <small class="text-muted">{{ $msg->created_at->diffForHumans() }}</small>
                            </div>
                            <p class="mb-2 text-dark {{ !$msg->is_read ? 'fw-bold' : '' }}">{{ $msg->message }}</p>
                            <small class="text-muted fst-italic">
                                @if($msg->is_read) <i class="bi bi-check2-all text-primary me-1"></i> Read 
                                @else <i class="bi bi-check2 me-1"></i> Delivered @endif
                            </small>
                        </div>
                    @empty
                        <div class="empty-state">
                            <div class="empty-icon">📭</div>
                            <div class="empty-title">No messages yet</div>
                            <div class="empty-subtitle">Sent messages will appear here</div>
                        </div>
                    @endforelse
                </div>
            </div>
            @if($sent_messages->hasPages())
                <div class="card-footer bg-white border-0 py-3">
                    {{ $sent_messages->links('pagination::bootstrap-5') }}
                </div>
            @endif
        </div>
    </div>
</div>

<script>
    document.getElementById('to_type').addEventListener('change', function() {
        document.getElementById('role_select_div').classList.toggle('d-none', this.value !== 'role');
        document.getElementById('person_select_div').classList.toggle('d-none', this.value !== 'specific');
    });
</script>
@endsection
