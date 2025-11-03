@extends('Layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center flex-wrap grid-margin mb-3">
  <div>
    <h4 class="mb-0">Audit Logs</h4>
    <p class="text-muted">Compliance & forensic tracking of all system activities</p>
  </div>
  <div>
    <a href="{{ route('audit-logs.index') }}" class="btn btn-secondary">
      <i data-feather="refresh-cw" class="icon-sm me-2"></i> Reset Filters
    </a>
  </div>
</div>

<!-- Filter Section -->
<div class="card mb-4">
  <div class="card-body">
    <form method="GET" action="{{ route('audit-logs.index') }}" id="filterForm">
      <div class="row g-3">
        <!-- Filter by User -->
        <div class="col-md-3">
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

        <!-- Filter by Action -->
        <div class="col-md-3">
          <label for="action" class="form-label">Filter by Action</label>
          <select class="form-select" id="action" name="action">
            <option value="">All Actions</option>
            @foreach($actions as $action)
              <option value="{{ $action }}" {{ request('action') == $action ? 'selected' : '' }}>
                {{ $action }}
              </option>
            @endforeach
          </select>
        </div>

        <!-- Filter by Division -->
        <div class="col-md-2">
          <label for="division_id" class="form-label">Filter by Division</label>
          <select class="form-select" id="division_id" name="division_id">
            <option value="">All Divisions</option>
            @foreach($divisions as $division)
              <option value="{{ $division->id }}" {{ request('division_id') == $division->id ? 'selected' : '' }}>
                {{ $division->name }}
              </option>
            @endforeach
          </select>
        </div>

        <!-- Filter by Time Period -->
        <div class="col-md-2">
          <label for="time_period" class="form-label">Time Period</label>
          <select class="form-select" id="time_period" name="time_period">
            <option value="">All Time</option>
            <option value="today" {{ request('time_period') == 'today' ? 'selected' : '' }}>Today</option>
            <option value="week" {{ request('time_period') == 'week' ? 'selected' : '' }}>Last Week</option>
            <option value="month" {{ request('time_period') == 'month' ? 'selected' : '' }}>Last Month</option>
            <option value="year" {{ request('time_period') == 'year' ? 'selected' : '' }}>Last Year</option>
          </select>
        </div>

        <!-- Custom Date Range -->
        <div class="col-md-2">
          <label for="date_from" class="form-label">Date From</label>
          <input type="date" class="form-control" id="date_from" name="date_from" value="{{ request('date_from') }}">
        </div>
        <div class="col-md-2">
          <label for="date_to" class="form-label">Date To</label>
          <input type="date" class="form-control" id="date_to" name="date_to" value="{{ request('date_to') }}">
        </div>

        <!-- Submit Button -->
        <div class="col-md-12 d-flex gap-2">
          <button type="submit" class="btn btn-primary">
            <i data-feather="search" class="icon-sm me-2"></i> Apply Filters
          </button>
          <a href="{{ route('audit-logs.index') }}" class="btn btn-secondary">
            <i data-feather="x" class="icon-sm me-2"></i> Clear
          </a>
        </div>
      </div>
    </form>
  </div>
</div>

<!-- Statistics Card -->
<div class="row mb-4">
  <div class="col-md-3">
    <div class="card">
      <div class="card-body">
        <div class="d-flex justify-content-between align-items-center">
          <div>
            <h6 class="text-muted mb-1">Total Logs</h6>
            <h3 class="mb-0 text-primary">{{ $totalLogs ?? $logs->total() }}</h3>
          </div>
          <div class="wd-50 ht-50 rounded-circle bg-primary d-flex align-items-center justify-content-center">
            <i data-feather="file-text" class="text-white"></i>
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
            <h6 class="text-muted mb-1">Today's Logs</h6>
            <h3 class="mb-0 text-success">{{ $todayLogs ?? 0 }}</h3>
          </div>
          <div class="wd-50 ht-50 rounded-circle bg-success d-flex align-items-center justify-content-center">
            <i data-feather="calendar" class="text-white"></i>
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
            <h3 class="mb-0 text-info">{{ $uniqueUsers ?? 0 }}</h3>
          </div>
          <div class="wd-50 ht-50 rounded-circle bg-info d-flex align-items-center justify-content-center">
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
            <h6 class="text-muted mb-1">Unique Actions</h6>
            <h3 class="mb-0 text-warning">{{ $uniqueActions ?? 0 }}</h3>
          </div>
          <div class="wd-50 ht-50 rounded-circle bg-warning d-flex align-items-center justify-content-center">
            <i data-feather="activity" class="text-white"></i>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Audit Logs Table -->
<div class="card">
  <div class="card-body">
    <div class="table-responsive">
      <table class="table table-hover">
        <thead>
          <tr>
            <th>ID</th>
            <th>Actor (User)</th>
            <th>Action</th>
            <th>Target Object</th>
            <th>Timestamp</th>
            <th>IP Address</th>
            <th>Device Info</th>
            <th>Details</th>
          </tr>
        </thead>
        <tbody>
          @forelse($logs as $log)
          <tr>
            <td>{{ $log->id }}</td>
            <td>
              @if($log->actor)
                <div class="d-flex align-items-center">
                  <div class="wd-30 ht-30 rounded-circle bg-primary d-flex align-items-center justify-content-center me-2">
                    <span class="text-white fw-bold small">{{ strtoupper(substr($log->actor->full_name ?? 'U', 0, 1)) }}</span>
                  </div>
                  <div>
                    <strong>{{ $log->actor->full_name ?? 'Unknown' }}</strong>
                    <br>
                    <small class="text-muted">{{ $log->actor->email ?? '-' }}</small>
                    @if($log->actor->division)
                      <br>
                      <span class="badge bg-info badge-sm">{{ $log->actor->division->name }}</span>
                    @endif
                  </div>
                </div>
              @else
                <span class="text-muted">System / Unknown</span>
              @endif
            </td>
            <td>
              <span class="badge bg-primary">{{ $log->action }}</span>
            </td>
            <td>
              @if($log->target_table && $log->target_id)
                @php
                  $targetObj = $log->target_object;
                @endphp
                @if($targetObj)
                  <div>
                    <strong>{{ $log->target_table }}</strong>
                    <br>
                    <small class="text-muted">
                      @if($log->target_table == 'users')
                        ID: {{ $targetObj->id }} - {{ $targetObj->full_name ?? 'N/A' }}
                      @elseif($log->target_table == 'divisions')
                        ID: {{ $targetObj->id }} - {{ $targetObj->name ?? 'N/A' }}
                      @elseif($log->target_table == 'roles')
                        ID: {{ $targetObj->id }} - {{ $targetObj->name ?? 'N/A' }}
                      @elseif($log->target_table == 'permissions')
                        ID: {{ $targetObj->id }} - {{ $targetObj->name ?? 'N/A' }}
                      @elseif($log->target_table == 'admin_scopes')
                        ID: {{ $targetObj->id }} - Admin: {{ $targetObj->adminUser->full_name ?? 'N/A' }}
                      @else
                        ID: {{ $log->target_id }}
                      @endif
                    </small>
                  </div>
                @else
                  <span class="text-muted">{{ $log->target_table }} #{{ $log->target_id }}</span>
                @endif
              @else
                <span class="text-muted">-</span>
              @endif
            </td>
            <td>
              <small>{{ $log->created_at->format('Y-m-d H:i:s') }}</small>
              <br>
              <small class="text-muted">{{ $log->created_at->diffForHumans() }}</small>
            </td>
            <td>
              @if(isset($log->details['ip_address']))
                <code>{{ $log->details['ip_address'] }}</code>
              @else
                <span class="text-muted">-</span>
              @endif
            </td>
            <td>
              @if(isset($log->details['user_agent']))
                <small>{{ Str::limit($log->details['user_agent'], 50) }}</small>
                <br>
                <button type="button" 
                        class="btn btn-sm btn-link p-0 text-primary" 
                        data-bs-toggle="popover" 
                        data-bs-trigger="focus"
                        data-bs-content="{{ $log->details['user_agent'] }}">
                  View Full
                </button>
              @else
                <span class="text-muted">-</span>
              @endif
            </td>
            <td>
              @if($log->details && count($log->details) > 0)
                <button type="button" 
                        class="btn btn-sm btn-info" 
                        data-bs-toggle="modal" 
                        data-bs-target="#detailsModal{{ $log->id }}">
                  <i data-feather="eye" class="icon-sm"></i> View JSON
                </button>
              @else
                <span class="text-muted">-</span>
              @endif

              <!-- Details JSON Modal -->
              <div class="modal fade" id="detailsModal{{ $log->id }}" tabindex="-1">
                <div class="modal-dialog modal-lg">
                  <div class="modal-content">
                    <div class="modal-header">
                      <h5 class="modal-title">Details JSON - Log #{{ $log->id }}</h5>
                      <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                      <pre class="bg-light p-3 rounded" style="max-height: 400px; overflow-y: auto;"><code>{{ json_encode($log->details, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) }}</code></pre>
                    </div>
                    <div class="modal-footer">
                      <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i data-feather="x" class="icon-sm me-2"></i> Close
                      </button>
                      <button type="button" class="btn btn-primary" onclick="copyToClipboard('{{ $log->id }}')">
                        <i data-feather="copy" class="icon-sm me-2"></i> Copy JSON
                      </button>
                    </div>
                  </div>
                </div>
              </div>
            </td>
          </tr>
          @empty
          <tr>
            <td colspan="8" class="text-center py-4">
              <i data-feather="file-text" class="icon-lg text-muted mb-3"></i>
              <p class="text-muted mb-0">No audit logs found.</p>
            </td>
          </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    <div class="mt-3">
      {{ $logs->links() }}
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
});

function copyToClipboard(logId) {
    const modal = document.querySelector('#detailsModal' + logId);
    const code = modal.querySelector('code');
    const text = code.textContent;
    
    navigator.clipboard.writeText(text).then(function() {
        // Show success message
        const btn = event.target.closest('button');
        const originalText = btn.innerHTML;
        btn.innerHTML = '<i data-feather="check" class="icon-sm me-2"></i> Copied!';
        btn.classList.add('btn-success');
        btn.classList.remove('btn-primary');
        
        setTimeout(function() {
            btn.innerHTML = originalText;
            btn.classList.remove('btn-success');
            btn.classList.add('btn-primary');
            feather.replace();
        }, 2000);
    });
}
</script>
@endsection

