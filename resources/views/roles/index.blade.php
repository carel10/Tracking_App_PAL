@extends('Layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center flex-wrap grid-margin mb-3">
  <div>
    <h4 class="mb-0">Role Management</h4>
    <p class="text-muted">Manage role structure and permissions</p>
  </div>
  <div>
    <a href="{{ route('roles.create') }}" class="btn btn-primary">
      <i data-feather="plus" class="icon-sm me-2"></i> Create Role
    </a>
  </div>
</div>

@if(session('success'))
  <div class="alert alert-success alert-dismissible fade show" role="alert">
    {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
  </div>
@endif

<div class="card">
  <div class="card-body">
    <div class="table-responsive">
      <table class="table table-hover">
        <thead>
          <tr>
            <th>ID</th>
            <th>Role Name</th>
            <th>Division</th>
            <th>Hierarchy Level</th>
            <th>Description</th>
            <th>Permissions</th>
            <th>Users Count</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          @forelse($roles as $role)
          <tr>
            <td>{{ $role->id }}</td>
            <td>
              <strong>{{ $role->name }}</strong>
            </td>
            <td>
              @if($role->division)
                <span class="badge bg-info">{{ $role->division->name }}</span>
              @else
                <span class="badge bg-secondary">Global</span>
              @endif
            </td>
            <td>
              <span class="badge bg-primary">{{ $role->hierarchy_level }}</span>
            </td>
            <td>{{ Str::limit($role->description ?? '-', 50) }}</td>
            <td>
              @if($role->permissions->count() > 0)
                <button type="button" 
                        class="btn btn-sm btn-link p-0" 
                        data-bs-toggle="popover" 
                        data-bs-trigger="focus"
                        data-bs-html="true"
                        data-bs-content="@foreach($role->permissions as $perm){{ $perm->name }} ({{ $perm->module }})<br>@endforeach">
                  <span class="badge bg-success">{{ $role->permissions->count() }} permission(s)</span>
                </button>
              @else
                <span class="text-muted">No permissions</span>
              @endif
            </td>
            <td>
              <a href="{{ route('roles.users', $role) }}" class="text-decoration-none">
                <span class="badge bg-info">{{ $role->users_count }} user(s)</span>
              </a>
            </td>
            <td>
              <div class="d-flex gap-1">
                <a href="{{ route('roles.edit', $role) }}" class="btn btn-sm btn-primary" title="Edit">
                  <i data-feather="edit-2" class="icon-sm"></i>
                </a>
                <button type="button" 
                        class="btn btn-sm btn-info" 
                        title="Assign Permissions"
                        data-bs-toggle="modal" 
                        data-bs-target="#assignPermissionsModal{{ $role->id }}">
                  <i data-feather="shield" class="icon-sm"></i>
                </button>
                <a href="{{ route('roles.users', $role) }}" class="btn btn-sm btn-secondary" title="View Users">
                  <i data-feather="users" class="icon-sm"></i>
                </a>
              </div>

              <!-- Assign Permissions Modal -->
              <div class="modal fade" id="assignPermissionsModal{{ $role->id }}" tabindex="-1">
                <div class="modal-dialog modal-lg">
                  <div class="modal-content">
                    <form action="{{ route('roles.update', $role) }}" method="POST">
                      @csrf
                      @method('PUT')
                      <div class="modal-header">
                        <h5 class="modal-title">Assign Permissions to {{ $role->name }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                      </div>
                      <div class="modal-body">
                        <input type="hidden" name="name" value="{{ $role->name }}">
                        <input type="hidden" name="division_id" value="{{ $role->division_id }}">
                        <input type="hidden" name="hierarchy_level" value="{{ $role->hierarchy_level }}">
                        <input type="hidden" name="description" value="{{ $role->description }}">
                        
                        <div class="row">
                          @php
                            $permissions = \App\Models\Permission::orderBy('module')->orderBy('name')->get();
                            $groupedPermissions = $permissions->groupBy('module');
                          @endphp
                          
                          @foreach($groupedPermissions as $module => $modulePermissions)
                            <div class="col-md-6 mb-3">
                              <h6 class="text-primary">{{ ucfirst($module) }}</h6>
                              @foreach($modulePermissions as $permission)
                                <div class="form-check mb-2">
                                  <input class="form-check-input" 
                                         type="checkbox" 
                                         name="permission_ids[]" 
                                         value="{{ $permission->id }}" 
                                         id="perm_{{ $role->id }}_{{ $permission->id }}"
                                         {{ $role->permissions->contains($permission->id) ? 'checked' : '' }}>
                                  <label class="form-check-label" for="perm_{{ $role->id }}_{{ $permission->id }}">
                                    {{ $permission->name }}
                                    <small class="text-muted d-block">{{ $permission->name }}</small>
                                  </label>
                                </div>
                              @endforeach
                            </div>
                          @endforeach
                        </div>
                      </div>
                      <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                          <i data-feather="x" class="icon-sm me-2"></i> Cancel
                        </button>
                        <button type="submit" class="btn btn-primary">
                          <i data-feather="save" class="icon-sm me-2"></i> Save Permissions
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
              <p class="text-muted mb-0">No roles found.</p>
            </td>
          </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    <div class="mt-3">
      {{ $roles->links() }}
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
</script>
@endsection
