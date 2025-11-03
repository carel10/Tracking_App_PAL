@extends('Layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center flex-wrap grid-margin mb-3">
  <div>
    <h4 class="mb-0">Session Monitoring</h4>
    <p class="text-muted">Real-time security access monitoring and management</p>
  </div>
  <div>
    <button type="button" class="btn btn-info" data-bs-toggle="modal" data-bs-target="#sessionLimitModal">
      <i data-feather="settings" class="icon-sm me-2"></i> Session Limit Config
    </button>
    <button type="button" class="btn btn-secondary" onclick="location.reload()">
      <i data-feather="refresh-cw" class="icon-sm me-2"></i> Refresh
    </button>
  </div>
</div>

@if(session('success'))
  <div class="alert alert-success alert-dismissible fade show" role="alert">
    {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
  </div>
@endif

<!-- Statistics Cards -->
<div class="row mb-4">
  <div class="col-md-3">
    <div class="card">
      <div class="card-body">
        <div class="d-flex justify-content-between align-items-center">
          <div>
            <h6 class="text-muted mb-1">Total Active Sessions</h6>
            <h3 class="mb-0 text-primary">{{ $totalActiveSessions }}</h3>
          </div>
          <div class="wd-50 ht-50 rounded-circle bg-primary d-flex align-items-center justify-content-center">
            <i data-feather="monitor" class="text-white"></i>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="col-md-3">
    <div class="card">
      <div class="card-body">
        <div class="d-flex justify-content-between align-items-center">
          <div>
            <h6 class="text-muted mb-1">Unique Users</h6>
            <h3 class="mb-0 text-success">{{ $uniqueUsers }}</h3>
          </div>
          <div class="wd-50 ht-50 rounded-circle bg-success d-flex align-items-center justify-content-center">
            <i data-feather="users" class="text-white"></i>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="col-md-3">
    <div class="card">
      <div class="card-body">
        <div class="d-flex justify-content-between align-items-center">
          <div>
            <h6 class="text-muted mb-1">Unique IP Addresses</h6>
            <h3 class="mb-0 text-info">{{ $uniqueIPs }}</h3>
          </div>
          <div class="wd-50 ht-50 rounded-circle bg-info d-flex align-items-center justify-content-center">
            <i data-feather="map-pin" class="text-white"></i>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="col-md-3">
    <div class="card">
      <div class="card-body">
        <div class="d-flex justify-content-between align-items-center">
          <div>
            <h6 class="text-muted mb-1">Session Limit</h6>
            <h3 class="mb-0 text-warning">{{ $sessionLimit }} per user</h3>
          </div>
          <div class="wd-50 ht-50 rounded-circle bg-warning d-flex align-items-center justify-content-center">
            <i data-feather="lock" class="text-white"></i>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Filter Section -->
<div class="card mb-4">
  <div class="card-body">
    <form method="GET" action="{{ route('session-monitoring.index') }}" class="row g-3">
      <div class="col-md-4">
        <label for="user_id" class="form-label">Filter by User</label>
        <select class="form-select" id="user_id" name="user_id">
          <option value="">All Users</option>
          @foreach($users as $user)
            <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
              {{ $user->full_name }} ({{ $user->email }})
            </option>
          @endforeach
        </select>
      </div>
      <div class="col-md-4">
        <label for="ip_address" class="form-label">Filter by IP Address</label>
        <input type="text" 
               class="form-control" 
               id="ip_address" 
               name="ip_address" 
               placeholder="e.g., 192.168.1.1"
               value="{{ request('ip_address') }}">
      </div>
      <div class="col-md-4 d-flex align-items-end gap-2">
        <button type="submit" class="btn btn-primary">
          <i data-feather="search" class="icon-sm me-2"></i> Filter
        </button>
        <a href="{{ route('session-monitoring.index') }}" class="btn btn-secondary">
          <i data-feather="x" class="icon-sm me-2"></i> Clear
        </a>
      </div>
    </form>
  </div>
</div>

<!-- Active Sessions Table -->
<div class="card">
  <div class="card-body">
    <div class="table-responsive">
      <table class="table table-hover">
        <thead>
          <tr>
            <th>ID</th>
            <th>User</th>
            <th>Device Info</th>
            <th>IP Address</th>
            <th>Login Time</th>
            <th>Expires At</th>
            <th>Time Remaining</th>
            <th>Duration</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          @forelse($sessions as $session)
          <tr>
            <td>{{ $session->id }}</td>
            <td>
              @if($session->user)
                <div class="d-flex align-items-center">
                  <div class="wd-30 ht-30 rounded-circle bg-primary d-flex align-items-center justify-content-center me-2">
                    <span class="text-white fw-bold small">{{ strtoupper(substr($session->user->full_name ?? 'U', 0, 1)) }}</span>
                  </div>
                  <div>
                    <strong>{{ $session->user->full_name }}</strong>
                    <br>
                    <small class="text-muted">{{ $session->user->email }}</small>
                    @if($session->user->division)
                      <br>
                      <span class="badge bg-info badge-sm">{{ $session->user->division->name }}</span>
                    @endif
                  </div>
                </div>
              @else
                <span class="text-muted">Unknown User</span>
              @endif
            </td>
            <td>
              @php
                $userAgent = $session->user_agent ?? 'Unknown';
                // Simple browser detection
                $browser = 'Unknown';
                $os = 'Unknown';
                if (strpos($userAgent, 'Chrome') !== false) $browser = 'Chrome';
                elseif (strpos($userAgent, 'Firefox') !== false) $browser = 'Firefox';
                elseif (strpos($userAgent, 'Safari') !== false) $browser = 'Safari';
                elseif (strpos($userAgent, 'Edge') !== false) $browser = 'Edge';
                elseif (strpos($userAgent, 'Opera') !== false) $browser = 'Opera';
                
                if (strpos($userAgent, 'Windows') !== false) $os = 'Windows';
                elseif (strpos($userAgent, 'Mac') !== false) $os = 'macOS';
                elseif (strpos($userAgent, 'Linux') !== false) $os = 'Linux';
                elseif (strpos($userAgent, 'Android') !== false) $os = 'Android';
                elseif (strpos($userAgent, 'iOS') !== false) $os = 'iOS';
              @endphp
              <div>
                <strong>{{ $browser }}</strong>
                <br>
                <small class="text-muted">{{ $os }}</small>
                <br>
                <button type="button" 
                        class="btn btn-sm btn-link p-0 text-primary" 
                        data-bs-toggle="popover" 
                        data-bs-trigger="focus"
                        data-bs-content="{{ $userAgent }}">
                  <small>View Full User Agent</small>
                </button>
              </div>
            </td>
            <td>
              <code>{{ $session->ip_address ?? 'N/A' }}</code>
            </td>
            <td>
              <small>{{ $session->issued_at->format('Y-m-d H:i:s') }}</small>
              <br>
              <small class="text-muted">{{ $session->issued_at->diffForHumans() }}</small>
            </td>
            <td>
              <small>{{ $session->expires_at->format('Y-m-d H:i:s') }}</small>
              <br>
              <small class="text-muted" id="expires_{{ $session->id }}">{{ $session->expires_at->diffForHumans() }}</small>
            </td>
            <td>
              <span class="badge bg-success" id="remaining_{{ $session->id }}">
                {{ now()->diffInMinutes($session->expires_at) }} min
              </span>
            </td>
            <td>
              <small>{{ $session->issued_at->diffForHumans($session->expires_at, true) }}</small>
            </td>
            <td>
              <div class="d-flex gap-1">
                <form action="{{ route('session-monitoring.force-logout', $session->id) }}" method="POST" class="d-inline">
                  @csrf
                  @method('DELETE')
                  <button type="submit" 
                          class="btn btn-sm btn-danger" 
                          title="Force Logout"
                          onclick="return confirm('Force logout this session?')">
                    <i data-feather="log-out" class="icon-sm"></i>
                  </button>
                </form>
                @if($session->user)
                  <form action="{{ route('session-monitoring.force-logout-user', $session->user->id) }}" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" 
                            class="btn btn-sm btn-warning" 
                            title="Force Logout All Sessions for User"
                            onclick="return confirm('Force logout all sessions for {{ $session->user->full_name }}?')">
                      <i data-feather="user-x" class="icon-sm"></i>
                    </button>
                  </form>
                @endif
              </div>
            </td>
          </tr>
          @empty
          <tr>
            <td colspan="9" class="text-center py-4">
              <i data-feather="monitor" class="icon-lg text-muted mb-3"></i>
              <p class="text-muted mb-0">No active sessions found.</p>
            </td>
          </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    <div class="mt-3">
      {{ $sessions->links() }}
    </div>
  </div>
</div>

<!-- Session Limit Configuration Modal -->
<div class="modal fade" id="sessionLimitModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <form action="{{ route('session-monitoring.update-limit') }}" method="POST">
        @csrf
        @method('PATCH')
        <div class="modal-header">
          <h5 class="modal-title">Session Limit Configuration</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label for="session_limit" class="form-label">Maximum Sessions Per User <span class="text-danger">*</span></label>
            <input type="number" 
                   class="form-control" 
                   id="session_limit" 
                   name="session_limit" 
                   min="1" 
                   max="20" 
                   value="{{ $sessionLimit }}" 
                   required>
            <small class="text-muted">Maximum number of concurrent sessions allowed per user (1-20)</small>
          </div>

          <div class="mb-3">
            <label for="session_lifetime" class="form-label">Session Lifetime (Minutes) <span class="text-danger">*</span></label>
            <input type="number" 
                   class="form-control" 
                   id="session_lifetime" 
                   name="session_lifetime" 
                   min="15" 
                   max="1440" 
                   value="{{ $sessionLifetime }}" 
                   required>
            <small class="text-muted">Session expiration time in minutes (15 minutes to 24 hours)</small>
          </div>

          <div class="alert alert-info">
            <i data-feather="info" class="icon-sm me-2"></i>
            <strong>Note:</strong> When a user exceeds the session limit, the oldest sessions will be automatically terminated.
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary">Save Configuration</button>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function(){
    if (typeof feather !== 'undefined') {
        feather.replace();
    }
    
    // Initialize popovers
    var popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
    var popoverList = popoverTriggerList.map(function (popoverTriggerEl) {
        return new bootstrap.Popover(popoverTriggerEl);
    });

    // Auto-refresh every 30 seconds
    setInterval(function() {
        location.reload();
    }, 30000);

    // Update countdown timers every minute
    setInterval(function() {
        @foreach($sessions as $session)
            updateRemainingTime({{ $session->id }}, '{{ $session->expires_at->toIso8601String() }}');
        @endforeach
    }, 60000);
});

function updateRemainingTime(sessionId, expiresAt) {
    const expiryDate = new Date(expiresAt);
    const now = new Date();
    const diff = expiryDate - now;
    
    if (diff <= 0) {
        document.getElementById('remaining_' + sessionId).textContent = 'Expired';
        document.getElementById('remaining_' + sessionId).className = 'badge bg-danger';
        return;
    }
    
    const minutes = Math.floor(diff / 60000);
    const hours = Math.floor(minutes / 60);
    const days = Math.floor(hours / 24);
    
    let timeText = '';
    if (days > 0) {
        timeText = days + 'd ' + (hours % 24) + 'h';
    } else if (hours > 0) {
        timeText = hours + 'h ' + (minutes % 60) + 'm';
    } else {
        timeText = minutes + ' min';
    }
    
    document.getElementById('remaining_' + sessionId).textContent = timeText;
    
    // Change badge color based on remaining time
    if (minutes < 5) {
        document.getElementById('remaining_' + sessionId).className = 'badge bg-danger';
    } else if (minutes < 30) {
        document.getElementById('remaining_' + sessionId).className = 'badge bg-warning';
    } else {
        document.getElementById('remaining_' + sessionId).className = 'badge bg-success';
    }
}
</script>
@endsection

