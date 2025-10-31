@extends('Layouts.app')

@section('content')
<div class="container mt-4">
    <h3>Activity Log</h3>
    <table class="table">
        <thead><tr><th>#</th><th>User</th><th>Action</th><th>IP</th><th>When</th></tr></thead>
        <tbody>
            @foreach($logs as $log)
            <tr>
                <td>{{ $log->id }}</td>
                <td>{{ optional($log->user)->name ?? 'System' }}</td>
                <td>{{ $log->action }}</td>
                <td>{{ $log->ip_address }}</td>
                <td>{{ $log->created_at }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    {{ $logs->links() }}
</div>
@endsection
