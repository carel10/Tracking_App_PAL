@extends('Layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center flex-wrap grid-margin mb-3">
  <div>
    <h4 class="mb-0">Users in Division: {{ $division->name }}</h4>
    <p class="text-muted">View all users belonging to this division</p>
  </div>
  <div>
    <a href="{{ route('divisions.index') }}" class="btn btn-secondary">
      <i data-feather="arrow-left" class="icon-sm me-2"></i> Back to Divisions
    </a>
  </div>
</div>

<div class="card mb-3">
  <div class="card-body">
    <div class="row">
      <div class="col-md-6">
        <h6>Division Information</h6>
        <table class="table table-sm">
          <tr>
            <td><strong>Name:</strong></td>
            <td>{{ $division->name }}</td>
          </tr>
          <tr>
            <td><strong>Description:</strong></td>
            <td>{{ $division->description ?? '-' }}</td>
          </tr>
        </table>
      </div>
      <div class="col-md-6">
        <h6>Statistics</h6>
        <div class="d-flex gap-3">
          <div>
            <h3 class="mb-0 text-primary">{{ $users->total() }}</h3>
            <small class="text-muted">Total Users</small>
          </div>
          <div>
            <h3 class="mb-0 text-success">
              {{ $users->where('status', 'active')->count() }}
            </h3>
            <small class="text-muted">Active Users</small>
          </div>
          <div>
            <h3 class="mb-0 text-info">
              {{ $division->roles()->count() }}
            </h3>
            <small class="text-muted">Division Roles</small>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="card">
  <div class="card-body">
    <div class="table-responsive">
      <table class="table table-hover">
        <thead>
          <tr>
            <th>ID</th>
            <th>Full Name</th>
            <th>Email</th>
            <th>Roles</th>
            <th>Status</th>
            <th>Created</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          @forelse($users as $user)
          <tr>
            <td>{{ $user->id }}</td>
            <td>{{ $user->full_name }}</td>
            <td>{{ $user->email }}</td>
            <td>
              @forelse($user->roles as $role)
                <span class="badge bg-secondary me-1">{{ $role->name }}</span>
              @empty
                <span class="text-muted">No roles</span>
              @endforelse
            </td>
            <td>
              @if($user->status === 'active')
                <span class="badge bg-success">Active</span>
              @elseif($user->status === 'inactive')
                <span class="badge bg-secondary">Inactive</span>
              @elseif($user->status === 'suspended')
                <span class="badge bg-danger">Suspended</span>
              @else
                <span class="badge bg-warning">Pending</span>
              @endif
            </td>
            <td>{{ $user->created_at->format('M d, Y') }}</td>
            <td>
              <a href="{{ route('users.edit', $user) }}" class="btn btn-sm btn-primary" title="Edit User">
                <i data-feather="edit-2" class="icon-sm"></i>
              </a>
            </td>
          </tr>
          @empty
          <tr>
            <td colspan="7" class="text-center py-4">
              <p class="text-muted mb-0">No users found in this division.</p>
            </td>
          </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    <div class="mt-3">
      {{ $users->links() }}
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
});
</script>
@endsection

