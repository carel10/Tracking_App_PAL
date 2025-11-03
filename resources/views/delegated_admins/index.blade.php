@extends('Layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center flex-wrap grid-margin mb-3">
  <div>
    <h4 class="mb-0">Delegated Admin Management</h4>
    <p class="text-muted">Assign admin users to divisions with specific permissions</p>
  </div>
  <div>
    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#assignAdminModal">
      <i data-feather="user-plus" class="icon-sm me-2"></i> Assign Admin to Division
    </button>
  </div>
</div>

@if(session('success'))
  <div class="alert alert-success alert-dismissible fade show" role="alert">
    {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
  </div>
@endif

@if(session('error'))
  <div class="alert alert-danger alert-dismissible fade show" role="alert">
    {{ session('error') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
  </div>
@endif

<!-- Statistics Card -->
<div class="row mb-4">
  <div class="col-md-4">
    <div class="card">
      <div class="card-body">
        <div class="d-flex justify-content-between align-items-center">
          <div>
            <h6 class="text-muted mb-1">Total Delegated Admins</h6>
            <h3 class="mb-0 text-primary">{{ $adminScopes->total() }}</h3>
          </div>
          <div class="wd-50 ht-50 rounded-circle bg-primary d-flex align-items-center justify-content-center">
            <i data-feather="users" class="text-white"></i>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="col-md-4">
    <div class="card">
      <div class="card-body">
        <div class="d-flex justify-content-between align-items-center">
          <div>
            <h6 class="text-muted mb-1">With User Management</h6>
            <h3 class="mb-0 text-success">{{ $adminScopes->where('can_manage_users', true)->count() }}</h3>
          </div>
          <div class="wd-50 ht-50 rounded-circle bg-success d-flex align-items-center justify-content-center">
            <i data-feather="user-check" class="text-white"></i>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="col-md-4">
    <div class="card">
      <div class="card-body">
        <div class="d-flex justify-content-between align-items-center">
          <div>
            <h6 class="text-muted mb-1">With Role Assignment</h6>
            <h3 class="mb-0 text-info">{{ $adminScopes->where('can_manage_roles', true)->count() }}</h3>
          </div>
          <div class="wd-50 ht-50 rounded-circle bg-info d-flex align-items-center justify-content-center">
            <i data-feather="shield" class="text-white"></i>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Delegated Admins Table -->
<div class="card">
  <div class="card-body">
    <div class="table-responsive">
      <table class="table table-hover">
        <thead>
          <tr>
            <th>ID</th>
            <th>Admin User</th>
            <th>Division</th>
            <th>Can Manage Users?</th>
            <th>Can Assign Roles?</th>
            <th>Access Level</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          @forelse($adminScopes as $scope)
          <tr>
            <td>{{ $scope->id }}</td>
            <td>
              <div class="d-flex align-items-center">
                <div class="wd-30 ht-30 rounded-circle bg-primary d-flex align-items-center justify-content-center me-2">
                  <span class="text-white fw-bold small">{{ strtoupper(substr($scope->adminUser->full_name ?? 'U', 0, 1)) }}</span>
                </div>
                <div>
                  <strong>{{ $scope->adminUser->full_name }}</strong>
                  <br>
                  <small class="text-muted">{{ $scope->adminUser->email }}</small>
                </div>
              </div>
            </td>
            <td>
              <span class="badge bg-info">{{ $scope->division->name }}</span>
            </td>
            <td>
              <form action="{{ route('delegated-admins.update-permission', $scope->id) }}" method="POST" class="d-inline">
                @csrf
                @method('PATCH')
                <input type="hidden" name="permission_type" value="can_manage_users">
                <input type="hidden" name="value" value="{{ $scope->can_manage_users ? 0 : 1 }}">
                <div class="form-check form-switch">
                  <input class="form-check-input" 
                         type="checkbox" 
                         role="switch"
                         {{ $scope->can_manage_users ? 'checked' : '' }}
                         onchange="this.form.submit()">
                  <label class="form-check-label">
                    {{ $scope->can_manage_users ? 'Yes' : 'No' }}
                  </label>
                </div>
              </form>
            </td>
            <td>
              <form action="{{ route('delegated-admins.update-permission', $scope->id) }}" method="POST" class="d-inline">
                @csrf
                @method('PATCH')
                <input type="hidden" name="permission_type" value="can_manage_roles">
                <input type="hidden" name="value" value="{{ $scope->can_manage_roles ? 0 : 1 }}">
                <div class="form-check form-switch">
                  <input class="form-check-input" 
                         type="checkbox" 
                         role="switch"
                         {{ $scope->can_manage_roles ? 'checked' : '' }}
                         onchange="this.form.submit()">
                  <label class="form-check-label">
                    {{ $scope->can_manage_roles ? 'Yes' : 'No' }}
                  </label>
                </div>
              </form>
            </td>
            <td>
              @if(!$scope->can_manage_users && !$scope->can_manage_roles)
                <span class="badge bg-secondary">Read-Only</span>
              @elseif($scope->can_manage_users && $scope->can_manage_roles)
                <span class="badge bg-success">Full Access</span>
              @elseif($scope->can_manage_users)
                <span class="badge bg-primary">User Management</span>
              @else
                <span class="badge bg-info">Role Assignment</span>
              @endif
            </td>
            <td>
              <form action="{{ route('delegated-admins.destroy', $scope->id) }}" method="POST" class="d-inline">
                @csrf
                @method('DELETE')
                <button type="submit" 
                        class="btn btn-sm btn-danger" 
                        title="Remove Assignment"
                        onclick="return confirm('Are you sure you want to remove this admin assignment?')">
                  <i data-feather="trash-2" class="icon-sm"></i>
                </button>
              </form>
            </td>
          </tr>
          @empty
          <tr>
            <td colspan="7" class="text-center py-4">
              <i data-feather="users" class="icon-lg text-muted mb-3"></i>
              <p class="text-muted mb-0">No delegated admins found. Assign your first admin to a division.</p>
            </td>
          </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    <div class="mt-3">
      {{ $adminScopes->links() }}
    </div>
  </div>
</div>

<!-- Assign Admin Modal -->
<div class="modal fade" id="assignAdminModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <form action="{{ route('delegated-admins.store') }}" method="POST">
        @csrf
        <div class="modal-header">
          <h5 class="modal-title">Assign Admin to Division</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label for="admin_user_id" class="form-label">Select Admin User <span class="text-danger">*</span></label>
            <select class="form-select" id="admin_user_id" name="admin_user_id" required>
              <option value="">Choose an admin user...</option>
              @foreach($users as $user)
                <option value="{{ $user->id }}" {{ old('admin_user_id') == $user->id ? 'selected' : '' }}>
                  {{ $user->full_name }} ({{ $user->email }})
                </option>
              @endforeach
            </select>
            @error('admin_user_id')
              <div class="text-danger small">{{ $message }}</div>
            @enderror
          </div>

          <div class="mb-3">
            <label for="division_id" class="form-label">Select Division <span class="text-danger">*</span></label>
            <select class="form-select" id="division_id" name="division_id" required>
              <option value="">Choose a division...</option>
              @foreach($divisions as $division)
                <option value="{{ $division->id }}" {{ old('division_id') == $division->id ? 'selected' : '' }}>
                  {{ $division->name }}
                </option>
              @endforeach
            </select>
            @error('division_id')
              <div class="text-danger small">{{ $message }}</div>
            @enderror
          </div>

          <div class="mb-3">
            <label class="form-label">Permissions</label>
            <div class="form-check mb-2">
              <input class="form-check-input" 
                     type="checkbox" 
                     name="can_manage_users" 
                     value="1" 
                     id="can_manage_users"
                     {{ old('can_manage_users') ? 'checked' : '' }}>
              <label class="form-check-label" for="can_manage_users">
                Can manage users?
              </label>
              <small class="d-block text-muted">Allow this admin to manage users in the assigned division</small>
            </div>
            <div class="form-check">
              <input class="form-check-input" 
                     type="checkbox" 
                     name="can_manage_roles" 
                     value="1" 
                     id="can_manage_roles"
                     {{ old('can_manage_roles') ? 'checked' : '' }}>
              <label class="form-check-label" for="can_manage_roles">
                Can assign roles?
              </label>
              <small class="d-block text-muted">Allow this admin to assign roles to users in the assigned division</small>
            </div>
            <small class="text-muted d-block mt-2">
              <strong>Note:</strong> If both permissions are unchecked, the admin will have read-only access.
            </small>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
            <i data-feather="x" class="icon-sm me-2"></i> Cancel
          </button>
          <button type="submit" class="btn btn-primary">
            <i data-feather="user-check" class="icon-sm me-2"></i> Assign Admin
          </button>
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
});
</script>
@endsection

