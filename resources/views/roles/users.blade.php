@extends('Layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center flex-wrap grid-margin mb-3">
  <div>
    <h4 class="mb-0">Users with Role: {{ $role->name }}</h4>
    <p class="text-muted">
      View all users assigned to this role
      @if($role->division)
        <span class="badge bg-info ms-2">{{ $role->division->name }} Division</span>
      @else
        <span class="badge bg-secondary ms-2">Global Role</span>
      @endif
    </p>
  </div>
  <div>
    <a href="{{ route('roles.index') }}" class="btn btn-secondary">
      <i data-feather="arrow-left" class="icon-sm me-2"></i> Back to Roles
    </a>
  </div>
</div>

<div class="card mb-3">
  <div class="card-body">
    <div class="row">
      <div class="col-md-6">
        <h6>Role Information</h6>
        <table class="table table-sm">
          <tr>
            <td><strong>Name:</strong></td>
            <td>{{ $role->name }}</td>
          </tr>
          <tr>
            <td><strong>Division:</strong></td>
            <td>{{ $role->division->name ?? 'Global' }}</td>
          </tr>
          <tr>
            <td><strong>Hierarchy Level:</strong></td>
            <td><span class="badge bg-primary">{{ $role->hierarchy_level }}</span></td>
          </tr>
          <tr>
            <td><strong>Description:</strong></td>
            <td>{{ $role->description ?? '-' }}</td>
          </tr>
          <tr>
            <td><strong>Permissions:</strong></td>
            <td>
              @if($role->permissions->count() > 0)
                {{ $role->permissions->count() }} permission(s)
              @else
                No permissions
              @endif
            </td>
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
            <th>Division</th>
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
              <span class="badge bg-info">{{ $user->division->name ?? '-' }}</span>
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
              <p class="text-muted mb-0">No users assigned to this role.</p>
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

