@extends('Layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center flex-wrap grid-margin mb-3">
  <div>
    <h4 class="mb-0">Roles in Division: {{ $division->name }}</h4>
    <p class="text-muted">View all roles belonging to this division</p>
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
            <h3 class="mb-0 text-info">{{ $roles->total() }}</h3>
            <small class="text-muted">Total Roles</small>
          </div>
          <div>
            <h3 class="mb-0 text-primary">
              {{ $division->users()->count() }}
            </h3>
            <small class="text-muted">Division Users</small>
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
            <th>Role Name</th>
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
              <span class="badge bg-primary">{{ $role->hierarchy_level }}</span>
            </td>
            <td>
              {{ Str::limit($role->description ?? '-', 50) }}
            </td>
            <td>
              @if($role->permissions->count() > 0)
                <button type="button" 
                        class="btn btn-sm btn-link p-0" 
                        data-bs-toggle="popover" 
                        data-bs-trigger="focus"
                        data-bs-html="true"
                        data-bs-content="@foreach($role->permissions as $perm){{ $perm->name }}<br>@endforeach">
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
              <a href="{{ route('roles.edit', $role) }}" class="btn btn-sm btn-primary" title="Edit">
                <i data-feather="edit-2" class="icon-sm"></i>
              </a>
            </td>
          </tr>
          @empty
          <tr>
            <td colspan="7" class="text-center py-4">
              <p class="text-muted mb-0">No roles found in this division.</p>
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

