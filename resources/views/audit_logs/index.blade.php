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

{{-- 
  FILTER & STATS DISABLED
  Karena partial file tidak ada, include sengaja dimatikan
--}}

{{-- @include('audit_logs._filters') --}}
{{-- @include('audit_logs._stats') --}}

<!-- Audit Logs Table -->
<div class="card mt-3">
  <div class="card-body">
    <div class="table-responsive">
      <table class="table table-hover">
        <thead>
          <tr>
            <th>ID</th>
            <th>Actor</th>
            <th>Action</th>
            <th>Target</th>
            <th>Timestamp</th>
            <th>IP</th>
            <th>Device</th>
            <th>Details</th>
          </tr>
        </thead>

        <tbody>
          @forelse($logs as $log)
          <tr>
            <td>{{ $log->id }}</td>

            <!-- Actor -->
            <td>
              @if($log->actor)
                <strong>{{ $log->actor->full_name }}</strong><br>
                <small class="text-muted">{{ $log->actor->email }}</small>
              @else
                <span class="text-muted">System</span>
              @endif
            </td>

            <!-- Action -->
            <td>
              <span class="badge bg-primary">{{ $log->action }}</span>
            </td>

            <!-- Target -->
            <td>
              @php $targetObj = $log->target_object; @endphp
              @if($targetObj)
                <strong>{{ $log->target_table }}</strong><br>
                <small class="text-muted">ID: {{ $targetObj->id }}</small>
              @else
                <span class="text-muted">-</span>
              @endif
            </td>

            <!-- Timestamp -->
            <td>
              <small>{{ $log->created_at->format('Y-m-d H:i:s') }}</small><br>
              <small class="text-muted">{{ $log->created_at->diffForHumans() }}</small>
            </td>

            <!-- IP Address -->
            <td>{{ $log->details['ip_address'] ?? '-' }}</td>

            <!-- Device -->
            <td>
              @if(isset($log->details['user_agent']))
                <small>{{ \Illuminate\Support\Str::limit($log->details['user_agent'], 40) }}</small>
              @else
                <span class="text-muted">-</span>
              @endif
            </td>

            <!-- JSON Details -->
            <td>
              @if(is_array($log->details) && count($log->details) > 0)
              <button class="btn btn-info btn-sm" data-bs-toggle="modal" data-bs-target="#details-{{ $log->id }}">
                View JSON
              </button>

              <!-- Modal JSON -->
              <div class="modal fade" id="details-{{ $log->id }}" tabindex="-1">
                <div class="modal-dialog modal-lg">
                  <div class="modal-content">
                    <div class="modal-header">
                      <h5>Log Details (ID: {{ $log->id }})</h5>
                      <button class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">
                      <pre class="bg-light p-3 rounded" style="max-height: 400px; overflow-y: auto;">
{{ json_encode($log->details, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}
                      </pre>
                    </div>

                  </div>
                </div>
              </div>
              @else
                <span class="text-muted">-</span>
              @endif
            </td>

          </tr>
          @empty
          <tr>
            <td colspan="8" class="text-center py-4 text-muted">
              <i>No audit logs</i>
            </td>
          </tr>
          @endforelse

        </tbody>
      </table>
    </div>

    <!-- Pagination -->
    <div class="mt-3">
      {{ $logs->links() }}
    </div>

  </div>
</div>
@endsection
