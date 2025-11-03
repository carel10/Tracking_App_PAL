@extends('Layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center flex-wrap grid-margin mb-3">
  <div>
    <h4 class="mb-0">Permission Management</h4>
    <p class="text-muted">Manage granular access permissions (e.g., legal.review, sc.approve)</p>
  </div>
  <div>
    <a href="{{ route('permissions.create') }}" class="btn btn-primary">
      <i data-feather="plus" class="icon-sm me-2"></i> Add Permission
    </a>
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

<!-- Search and Filter -->
<div class="card mb-4">
  <div class="card-body">
    <form method="GET" action="{{ route('permissions.index') }}" class="row g-3">
      <div class="col-md-4">
        <label for="search" class="form-label">Search</label>
        <input type="text" 
               class="form-control" 
               id="search" 
               name="search" 
               placeholder="Permission name or code"
               value="{{ request('search') }}">
      </div>
      <div class="col-md-3">
        <label for="module" class="form-label">Module</label>
        <select class="form-select" id="module" name="module">
          <option value="">All Modules</option>
          @foreach($modules as $moduleName)
            <option value="{{ $moduleName }}" {{ request('module') == $moduleName ? 'selected' : '' }}>
              {{ ucfirst($moduleName) }}
            </option>
          @endforeach
        </select>
      </div>
      <div class="col-md-3 d-flex align-items-end gap-2">
        <button type="submit" class="btn btn-primary">
          <i data-feather="search" class="icon-sm me-2"></i> Filter
        </button>
        <a href="{{ route('permissions.index') }}" class="btn btn-secondary">
          <i data-feather="x" class="icon-sm me-2"></i> Clear
        </a>
      </div>
    </form>
  </div>
</div>

<!-- Permissions grouped by module -->
@php
  $groupedPermissions = $permissions->groupBy('module');
@endphp

@if($groupedPermissions->count() > 0)
  @foreach($groupedPermissions as $moduleName => $modulePermissions)
    <div class="card mb-3">
      <div class="card-header bg-primary text-white">
        <h6 class="mb-0">
          <i data-feather="folder" class="icon-sm me-2"></i>
          {{ ucfirst($moduleName) }} Module
          <span class="badge bg-light text-dark ms-2">{{ $modulePermissions->count() }} permission(s)</span>
        </h6>
      </div>
      <div class="card-body">
        <div class="table-responsive">
          <table class="table table-sm table-hover mb-0">
            <thead>
              <tr>
                <th>ID</th>
                <th>Permission Name</th>
                <th>Permission Code</th>
                <th>Description</th>
                <th>Roles Count</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              @foreach($modulePermissions as $permission)
                <tr>
                  <td>{{ $permission->id }}</td>
                  <td><strong>{{ $permission->name }}</strong></td>
                  <td>
                    <code class="text-primary">{{ $permission->module }}.{{ Str::slug($permission->name) }}</code>
                  </td>
                  <td>
                    @if($permission->description)
                      {{ Str::limit($permission->description, 60) }}
                    @else
                      <span class="text-muted">-</span>
                    @endif
                  </td>
                  <td>
                    <span class="badge bg-info">{{ $permission->roles()->count() }} role(s)</span>
                  </td>
                  <td>
                    <div class="d-flex gap-1">
                      <a href="{{ route('permissions.edit', $permission) }}" class="btn btn-sm btn-primary" title="Edit">
                        <i data-feather="edit-2" class="icon-sm"></i>
                      </a>
                      <form action="{{ route('permissions.destroy', $permission) }}" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" 
                                class="btn btn-sm btn-danger" 
                                title="Delete"
                                onclick="return confirm('Are you sure you want to delete this permission?')">
                          <i data-feather="trash-2" class="icon-sm"></i>
                        </button>
                      </form>
                    </div>
                  </td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      </div>
    </div>
  @endforeach

  <div class="mt-3">
    {{ $permissionsPaginated->links() }}
  </div>
@else
  <div class="card">
    <div class="card-body text-center py-4">
      <p class="text-muted mb-0">No permissions found.</p>
    </div>
  </div>
@endif
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
