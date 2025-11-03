@extends('Layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center flex-wrap grid-margin mb-3">
  <div>
    <h4 class="mb-0">User Management</h4>
    <p class="text-muted">Manage system users and their access</p>
  </div>
  <div>
    <a href="{{ route('users.create') }}" class="btn btn-primary">
      <i data-feather="plus" class="icon-sm me-2"></i> Add New User
    </a>
  </div>
</div>

@if(session('success'))
  <div class="alert alert-success alert-dismissible fade show" role="alert">
    {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
  </div>
@endif

<!-- Search and Filter -->
<div class="card mb-4">
  <div class="card-body">
    <form method="GET" action="{{ route('users.index') }}" class="row g-3">
      <div class="col-md-3">
        <label for="search" class="form-label">Search</label>
        <input type="text" 
               class="form-control" 
               id="search" 
               name="search" 
               placeholder="Name or Email"
               value="{{ request('search') }}">
      </div>
      <div class="col-md-2">
        <label for="division_id" class="form-label">Division</label>
        <select class="form-select" id="division_id" name="division_id">
          <option value="">All Divisions</option>
          @foreach($divisions as $division)
            <option value="{{ $division->id }}" {{ request('division_id') == $division->id ? 'selected' : '' }}>
              {{ $division->name }}
            </option>
          @endforeach
        </select>
      </div>
      <div class="col-md-2">
        <label for="role_id" class="form-label">Role</label>
        <select class="form-select" id="role_id" name="role_id">
          <option value="">All Roles</option>
          @foreach($roles as $role)
            <option value="{{ $role->id }}" {{ request('role_id') == $role->id ? 'selected' : '' }}>
              {{ $role->name }}
            </option>
          @endforeach
        </select>
      </div>
      <div class="col-md-2">
        <label for="status" class="form-label">Status</label>
        <select class="form-select" id="status" name="status">
          <option value="">All Status</option>
          <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
          <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
          <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
          <option value="suspended" {{ request('status') == 'suspended' ? 'selected' : '' }}>Suspended</option>
        </select>
      </div>
      <div class="col-md-3 d-flex align-items-end gap-2">
        <button type="submit" class="btn btn-primary">
          <i data-feather="search" class="icon-sm me-2"></i> Filter
        </button>
        <a href="{{ route('users.index') }}" class="btn btn-secondary">
          <i data-feather="x" class="icon-sm me-2"></i> Clear
        </a>
      </div>
    </form>
  </div>
</div>

<!-- Users Table -->
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
              <span class="badge bg-info">{{ $user->division->name ?? '-' }}</span>
            </td>
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
              <div class="d-flex gap-1">
                <a href="{{ route('users.edit', $user) }}" class="btn btn-sm btn-primary" title="Edit">
                  <i data-feather="edit-2" class="icon-sm"></i>
                </a>
                <button type="button" 
                        class="btn btn-sm btn-info" 
                        title="View Sessions"
                        onclick="window.location.href='{{ route('users.sessions', $user) }}'">
                  <i data-feather="monitor" class="icon-sm"></i>
                </button>
                <button type="button" 
                        class="btn btn-sm btn-warning" 
                        title="Reset Password"
                        data-bs-toggle="modal" 
                        data-bs-target="#resetPasswordModal{{ $user->id }}">
                  <i data-feather="key" class="icon-sm"></i>
                </button>
                <button type="button" 
                        class="btn btn-sm btn-success" 
                        title="Assign Roles"
                        data-bs-toggle="modal" 
                        data-bs-target="#assignRolesModal{{ $user->id }}">
                  <i data-feather="shield" class="icon-sm"></i>
                </button>
                <form action="{{ route('users.toggle', $user) }}" method="POST" class="d-inline">
                  @csrf
                  @method('PATCH')
                  <button type="submit" 
                          class="btn btn-sm {{ $user->status === 'active' ? 'btn-danger' : 'btn-success' }}" 
                          title="{{ $user->status === 'active' ? 'Suspend' : 'Activate' }}">
                    <i data-feather="{{ $user->status === 'active' ? 'pause' : 'play' }}" class="icon-sm"></i>
                  </button>
                </form>
                <form action="{{ route('users.force-logout', $user) }}" method="POST" class="d-inline">
                  @csrf
                  <button type="submit" 
                          class="btn btn-sm btn-secondary" 
                          title="Force Logout"
                          onclick="return confirm('Force logout this user from all devices?')">
                    <i data-feather="log-out" class="icon-sm"></i>
                  </button>
                </form>
                <form action="{{ route('users.destroy', $user) }}" method="POST" class="d-inline">
                  @csrf
                  @method('DELETE')
                  <button type="submit" 
                          class="btn btn-sm btn-danger" 
                          title="Archive"
                          onclick="return confirm('Are you sure you want to archive this user?')">
                    <i data-feather="trash-2" class="icon-sm"></i>
                  </button>
                </form>
              </div>

              <!-- Reset Password Modal -->
              <div class="modal fade" id="resetPasswordModal{{ $user->id }}" tabindex="-1">
                <div class="modal-dialog">
                  <div class="modal-content">
                    <form action="{{ route('users.reset-password', $user) }}" method="POST">
                      @csrf
                      <div class="modal-header">
                        <h5 class="modal-title">Reset Password</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                      </div>
                      <div class="modal-body">
                        <p>Reset password for <strong>{{ $user->full_name }}</strong>?</p>
                        <div class="mb-3">
                          <label for="password" class="form-label">New Password</label>
                          <input type="password" class="form-control" id="password" name="password" required minlength="8">
                        </div>
                        <div class="mb-3">
                          <label for="password_confirmation" class="form-label">Confirm Password</label>
                          <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required minlength="8">
                        </div>
                      </div>
                      <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                          <i data-feather="x" class="icon-sm me-2"></i> Cancel
                        </button>
                        <button type="submit" class="btn btn-primary">
                          <i data-feather="key" class="icon-sm me-2"></i> Reset Password
                        </button>
                      </div>
                    </form>
                  </div>
                </div>
              </div>

              <!-- Assign Roles Modal -->
              <div class="modal fade" id="assignRolesModal{{ $user->id }}" tabindex="-1">
                <div class="modal-dialog modal-lg">
                  <div class="modal-content">
                    <form action="{{ route('users.assign-roles', $user) }}" method="POST">
                      @csrf
                      <div class="modal-header">
                        <h5 class="modal-title">Assign Roles to {{ $user->full_name }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                      </div>
                      <div class="modal-body">
                        <div class="row">
                          @foreach($roles as $role)
                            <div class="col-md-6 mb-2">
                              <div class="form-check">
                                <input class="form-check-input" 
                                       type="checkbox" 
                                       name="role_ids[]" 
                                       value="{{ $role->id }}" 
                                       id="role_{{ $user->id }}_{{ $role->id }}"
                                       {{ $user->roles->contains($role->id) ? 'checked' : '' }}>
                                <label class="form-check-label" for="role_{{ $user->id }}_{{ $role->id }}">
                                  {{ $role->name }}
                                  @if($role->division)
                                    <small class="text-muted">({{ $role->division->name }})</small>
                                  @endif
                                </label>
                              </div>
                            </div>
                          @endforeach
                        </div>
                      </div>
                      <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                          <i data-feather="x" class="icon-sm me-2"></i> Cancel
                        </button>
                        <button type="submit" class="btn btn-primary">
                          <i data-feather="shield" class="icon-sm me-2"></i> Assign Roles
                        </button>
                      </div>
                    </form>
                  </div>
                </div>
              </div>
            </td>
          </tr>
          @empty
          <tr>
            <td colspan="8" class="text-center py-4">
              <p class="text-muted mb-0">No users found.</p>
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
