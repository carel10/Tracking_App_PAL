@extends('Layouts.app')

@section('content')
<div class="col-md-12 grid-margin stretch-card">
    <div class="card">
        <div class="card-body">
            <h6 class="card-title mb-3">Activity Log</h6>

            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>User</th>
                            <th>Activity</th>
                            <th>IP Address</th>
                            <th>User Agent</th>
                            <th>Timestamp</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($logs as $log)
                        <tr>
                            <td>{{ $log->log_id }}</td>
                            <td>
                                <div>
                                    <strong>{{ $log->user->full_name ?? 'Unknown' }}</strong><br>
                                    <small class="text-muted">{{ $log->user->username ?? '-' }}</small>
                                </div>
                            </td>
                            <td>{{ $log->activity }}</td>
                            <td><code>{{ $log->ip_address ?? '-' }}</code></td>
                            <td>
                                <small>{{ Str::limit($log->user_agent ?? '-', 50) }}</small>
                            </td>
                            <td>{{ $log->timestamp ? \Carbon\Carbon::parse($log->timestamp)->format('Y-m-d H:i:s') : '-' }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center">No activity logs found.</td>
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
</div>
@endsection
