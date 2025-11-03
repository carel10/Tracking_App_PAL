@extends('Layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center flex-wrap grid-margin mb-3">
  <div>
    <h4 class="mb-0">User Sessions</h4>
    <p class="text-muted">Active sessions for {{ $user->full_name }}</p>
  </div>
  <div>
    <a href="{{ route('users.index') }}" class="btn btn-secondary">
      <i data-feather="arrow-left" class="icon-sm me-2"></i> Back to Users
    </a>
    <form action="{{ route('users.force-logout', $user) }}" method="POST" class="d-inline">
      @csrf
      <button type="submit" 
              class="btn btn-danger"
              onclick="return confirm('Force logout this user from all devices?')">
        <i data-feather="log-out" class="icon-sm me-2"></i> Force Logout All
      </button>
    </form>
  </div>
</div>

<div class="card">
  <div class="card-body">
    <div class="mb-3">
      <strong>User:</strong> {{ $user->full_name }} ({{ $user->email }})<br>
      <strong>Total Active Sessions:</strong> {{ $sessions->count() }}
    </div>

    @if($sessions->count() > 0)
      <div class="table-responsive">
        <table class="table table-hover">
          <thead>
            <tr>
              <th>Device/Browser</th>
              <th>IP Address</th>
              <th>Login Time</th>
              <th>Expires At</th>
              <th>Status</th>
              <th>Duration</th>
            </tr>
          </thead>
          <tbody>
            @foreach($sessions as $session)
              <tr>
                <td>
                  <small>{{ Str::limit($session->user_agent ?? 'Unknown', 80) }}</small>
                </td>
                <td>
                  <code>{{ $session->ip_address ?? 'N/A' }}</code>
                </td>
                <td>{{ $session->issued_at->format('M d, Y H:i:s') }}</td>
                <td>{{ $session->expires_at->format('M d, Y H:i:s') }}</td>
                <td>
                  @if($session->expires_at->isFuture())
                    <span class="badge bg-success">Active</span>
                  @else
                    <span class="badge bg-secondary">Expired</span>
                  @endif
                </td>
                <td>
                  {{ $session->issued_at->diffForHumans() }}
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    @else
      <div class="text-center py-4">
        <p class="text-muted mb-0">No active sessions found.</p>
      </div>
    @endif
  </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function(){
    if (typeof feather !== 'undefined') {
        feather.replace();
    }
});
</script>
@endsection

