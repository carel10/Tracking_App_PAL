@extends('Layouts.app')

@section('content')
<!-- Dashboard: System Overview -->
<div class="d-flex justify-content-between align-items-center flex-wrap grid-margin mb-3">
  <div>
    <h4 class="mb-0">Dashboard</h4>
    <p class="text-muted">System overview and real-time monitoring</p>
  </div>
</div>

<!-- System Health Indicator -->
<div class="row mb-4">
  <div class="col-12">
    <div class="card">
      <div class="card-body">
        <div class="d-flex justify-content-between align-items-center">
          <div>
            <h6 class="card-title mb-2">System Health</h6>
            <div class="d-flex align-items-center">
              <div class="me-3">
                <h3 class="mb-0 text-{{ $systemHealth['statusColor'] }}">{{ $systemHealth['score'] }}%</h3>
              </div>
              <div>
                <span class="badge bg-{{ $systemHealth['statusColor'] }}">{{ $systemHealth['statusText'] }}</span>
                @if(count($systemHealth['issues']) > 0)
                  <small class="text-muted ms-2">{{ count($systemHealth['issues']) }} issue(s) detected</small>
                @endif
              </div>
            </div>
            @if(count($systemHealth['issues']) > 0)
              <div class="mt-2">
                <ul class="list-unstyled mb-0">
                  @foreach($systemHealth['issues'] as $issue)
                    <li><small class="text-danger">• {{ $issue }}</small></li>
                  @endforeach
                </ul>
              </div>
            @endif
          </div>
          <div class="text-end">
            <i data-feather="activity" class="icon-lg text-{{ $systemHealth['statusColor'] }}"></i>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Core Statistics -->
<div class="row mb-4">
  <div class="col-md-3">
    <div class="card">
      <div class="card-body">
        <div class="d-flex justify-content-between align-items-center">
          <div>
            <h6 class="card-title mb-2">Total Users</h6>
            <h3 class="mb-0">{{ $stats['totalUsers'] }}</h3>
            <small class="text-muted">All registered users</small>
          </div>
          <div class="text-primary">
            <i data-feather="users" class="icon-lg"></i>
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
            <h6 class="card-title mb-2">Active Sessions</h6>
            <h3 class="mb-0">{{ $stats['totalActiveSessions'] }}</h3>
            <small class="text-muted">Currently logged in</small>
          </div>
          <div class="text-success">
            <i data-feather="check-circle" class="icon-lg"></i>
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
            <h6 class="card-title mb-2">Roles</h6>
            <h3 class="mb-0">{{ $stats['rolesCount'] }}</h3>
            <small class="text-muted">Total roles</small>
          </div>
          <div class="text-info">
            <i data-feather="shield" class="icon-lg"></i>
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
            <h6 class="card-title mb-2">Permissions</h6>
            <h3 class="mb-0">{{ $stats['permissionsCount'] }}</h3>
            <small class="text-muted">Total permissions</small>
          </div>
          <div class="text-warning">
            <i data-feather="key" class="icon-lg"></i>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Security Alerts -->
@if(count($securityAlerts) > 0)
<div class="row mb-4">
  <div class="col-12">
    <div class="card border-{{ count(collect($securityAlerts)->where('type', 'danger')) > 0 ? 'danger' : 'warning' }}">
      <div class="card-header bg-{{ count(collect($securityAlerts)->where('type', 'danger')) > 0 ? 'danger' : 'warning' }} text-white">
        <div class="d-flex align-items-center">
          <i data-feather="alert-triangle" class="me-2"></i>
          <h6 class="mb-0">Security Alerts</h6>
        </div>
      </div>
      <div class="card-body">
        @foreach($securityAlerts as $alert)
          <div class="alert alert-{{ $alert['type'] }} alert-dismissible fade show mb-2" role="alert">
            <strong>{{ ucfirst($alert['type']) }}:</strong> {{ $alert['message'] }}
            @if(isset($alert['ip_address']))
              <small class="d-block mt-1">IP: {{ $alert['ip_address'] }} | Attempts: {{ $alert['attempts'] }}</small>
            @endif
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
          </div>
        @endforeach
      </div>
    </div>
  </div>
</div>
@endif

<!-- Failed Login Attempts & Last Login Users -->
<div class="row mb-4">
  <!-- Failed Login Attempts -->
  <div class="col-md-6">
    <div class="card">
      <div class="card-body">
        <h6 class="card-title mb-3">Failed Login Attempts</h6>
        <div class="d-flex align-items-center">
          <div class="me-3">
            <h2 class="mb-0 text-{{ $failedLoginAttempts > 20 ? 'danger' : ($failedLoginAttempts > 10 ? 'warning' : 'success') }}">
              {{ $failedLoginAttempts }}
            </h2>
          </div>
          <div>
            <small class="text-muted">In the last 24 hours</small>
            @if($failedLoginAttempts > 20)
              <div class="mt-1">
                <span class="badge bg-danger">High</span>
              </div>
            @elseif($failedLoginAttempts > 10)
              <div class="mt-1">
                <span class="badge bg-warning">Moderate</span>
              </div>
            @else
              <div class="mt-1">
                <span class="badge bg-success">Normal</span>
              </div>
            @endif
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Last Login Users -->
  <div class="col-md-6">
    <div class="card">
      <div class="card-body">
        <h6 class="card-title mb-3">Recent Logins</h6>
        @if($lastLoginUsers->count() > 0)
          <div class="list-group list-group-flush">
            @foreach($lastLoginUsers as $session)
              <div class="list-group-item px-0">
                <div class="d-flex justify-content-between align-items-center">
                  <div>
                    <h6 class="mb-1">{{ $session->user->full_name ?? 'Unknown User' }}</h6>
                    <small class="text-muted">{{ $session->user->email ?? 'N/A' }}</small>
                  </div>
                  <div class="text-end">
                    <small class="text-muted">{{ $session->issued_at->diffForHumans() }}</small>
                    @if($session->ip_address)
                      <div class="mt-1">
                        <small class="badge bg-secondary">{{ $session->ip_address }}</small>
                      </div>
                    @endif
                  </div>
                </div>
              </div>
            @endforeach
          </div>
        @else
          <p class="text-muted mb-0">No recent logins</p>
        @endif
      </div>
    </div>
  </div>
</div>

@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function(){
    // Initialize feather icons
    if (typeof feather !== 'undefined') {
        feather.replace();
    }
});
</script>
@endsection
