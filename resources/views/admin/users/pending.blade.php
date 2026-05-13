@extends('layouts.admin')
@section('page-title', 'Pending Approvals')
@section('content')
<div class="page-content">

  {{-- Page Header --}}
  <div style="display:flex; justify-content:space-between; 
       align-items:center; margin-bottom:20px">
    <div>
      <h5 style="margin:0; color:#1a2332">
        Pending Approvals
      </h5>
      <small style="color:#64748b">
        Review and approve registration requests
      </small>
    </div>
  </div>

  {{-- Flash Messages --}}
  @if(session('success'))
    <div class="flash-success">
      <i class="bi bi-check-circle"></i>
      {{ session('success') }}
    </div>
  @endif
  @if(session('error'))
    <div class="flash-error">
      <i class="bi bi-exclamation-circle"></i>
      {{ session('error') }}
    </div>
  @endif

  {{-- Pending count banner --}}
  @if($pendingUsers->count() > 0)
    <div style="background:#FAEEDA; 
         border:1px solid #EF9F27;
         border-radius:8px; 
         padding:14px 20px;
         margin-bottom:20px">
      <strong style="color:#854F0B">
        🔔 {{ $pendingUsers->count() }} 
        registration request(s) waiting
      </strong>
    </div>
  @endif

  {{-- Empty State --}}
  @if($pendingUsers->isEmpty())
    <div class="empty-state">
      <div class="empty-icon">✅</div>
      <div class="empty-title">
        No pending approvals
      </div>
      <div class="empty-subtitle">
        All registration requests 
        have been reviewed
      </div>
    </div>
  @else
    {{-- Pending Users Cards --}}
    <div style="display:grid; 
         grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
         gap:16px">
      @foreach($pendingUsers as $user)
        <div class="content-card">
          <div class="card-body-custom">
            
            {{-- User Info --}}
            <div style="display:flex; 
                 align-items:center; 
                 gap:12px; margin-bottom:14px">
              <div style="width:44px; height:44px;
                   border-radius:50%;
                   background:#E6F1FB;
                   display:flex; align-items:center;
                   justify-content:center;
                   font-size:18px; font-weight:500;
                   color:#185FA5">
                {{ strtoupper(substr($user->name, 0, 1)) }}
              </div>
              <div>
                <div style="font-size:14px; 
                     font-weight:500; color:#1a2332">
                  {{ $user->name }}
                </div>
                <div style="font-size:12px; color:#64748b">
                  {{ $user->email }}
                </div>
              </div>
            </div>

            {{-- User Details --}}
            <div style="display:flex; 
                 flex-direction:column; gap:6px;
                 margin-bottom:16px">
              <div style="font-size:12px; color:#374151">
                <i class="bi bi-person-badge" 
                   style="color:#1D9E75; margin-right:6px">
                </i>
                Role: 
                <span style="font-weight:500; 
                      text-transform:capitalize">
                  {{ $user->role }}
                </span>
              </div>
              @if($user->shift)
                <div style="font-size:12px; color:#374151">
                  <i class="bi bi-clock" 
                     style="color:#1D9E75; margin-right:6px">
                  </i>
                  Shift: 
                  <span style="font-weight:500; 
                        text-transform:capitalize">
                    {{ $user->shift }}
                  </span>
                </div>
              @endif
              @if($user->area)
                <div style="font-size:12px; color:#374151">
                  <i class="bi bi-geo-alt" 
                     style="color:#1D9E75; margin-right:6px">
                  </i>
                  Area: {{ $user->area }}
                </div>
              @endif
              <div style="font-size:12px; color:#64748b">
                <i class="bi bi-calendar" 
                   style="margin-right:6px"></i>
                Registered: 
                {{ $user->created_at->diffForHumans() }}
              </div>
            </div>

            {{-- Action Buttons --}}
            <div style="display:flex; gap:8px">
              {{-- Approve Button --}}
              <form method="POST" 
                    action="{{ route('admin.users.approve', $user->id) }}"
                    style="flex:1"
                    onsubmit="return confirm(
                      'Approve {{ $user->name }} as {{ $user->role }}?')">
                @csrf
                <button type="submit"
                        style="width:100%; background:#1D9E75;
                               color:#fff; border:none;
                               padding:8px; border-radius:6px;
                               font-size:12px; cursor:pointer">
                  <i class="bi bi-check-circle"></i>
                  Approve
                </button>
              </form>

              {{-- Reject Button --}}
              <button type="button"
                      style="flex:1; background:#E24B4A;
                             color:#fff; border:none;
                             padding:8px; border-radius:6px;
                             font-size:12px; cursor:pointer"
                      onclick="openRejectModal(
                        {{ $user->id }}, 
                        '{{ $user->name }}',
                        '{{ $user->role }}')">
                <i class="bi bi-x-circle"></i>
                Reject
              </button>
            </div>

          </div>
        </div>
      @endforeach
    </div>
  @endif

</div>

{{-- Reject Modal --}}
<div id="rejectModal" 
     style="display:none; position:fixed;
            top:0; left:0; right:0; bottom:0;
            background:rgba(0,0,0,0.5);
            z-index:9999;
            align-items:center;
            justify-content:center">
  <div style="background:#fff; border-radius:10px;
              padding:24px; width:400px;
              max-width:90%">
    <h6 style="margin:0 0 6px; color:#E24B4A">
      ❌ Reject Registration
    </h6>
    <p style="font-size:13px; color:#64748b; 
              margin-bottom:16px"
       id="rejectModalSubtitle">
    </p>
    <form method="POST" id="rejectForm">
      @csrf
      <div style="margin-bottom:14px">
        <label style="font-size:12px; 
               font-weight:500; color:#374151;
               display:block; margin-bottom:6px">
          Reason for rejection 
          <span style="color:#E24B4A">*</span>
        </label>
        <textarea name="rejection_reason" 
                  id="rejectReason"
                  rows="3"
                  required
                  minlength="10"
                  placeholder="Please provide a reason (min 10 characters)..."
                  style="width:100%; 
                         border:1px solid #e2e8f0;
                         border-radius:6px;
                         padding:8px 12px;
                         font-size:13px;
                         resize:vertical;
                         box-sizing:border-box">
        </textarea>
        <small style="color:#94a3b8; font-size:11px">
          Minimum 10 characters required
        </small>
      </div>
      <div style="display:flex; gap:8px">
        <button type="button"
                onclick="closeRejectModal()"
                style="flex:1; background:#f1f5f9;
                       color:#64748b; border:none;
                       padding:9px; border-radius:6px;
                       font-size:13px; cursor:pointer">
          Cancel
        </button>
        <button type="submit"
                style="flex:1; background:#E24B4A;
                       color:#fff; border:none;
                       padding:9px; border-radius:6px;
                       font-size:13px; cursor:pointer">
          Confirm Rejection
        </button>
      </div>
    </form>
  </div>
</div>

@endsection

@section('scripts')
<script>
function openRejectModal(userId, userName, userRole) {
  document.getElementById('rejectModal')
    .style.display = 'flex';
  document.getElementById('rejectModalSubtitle')
    .textContent = 'Rejecting: ' + userName + 
                   ' (' + userRole + ')';
  document.getElementById('rejectForm')
    .action = '/admin/users/reject/' + userId;
  document.getElementById('rejectReason').value = '';
}
function closeRejectModal() {
  document.getElementById('rejectModal')
    .style.display = 'none';
}
</script>
@endsection
