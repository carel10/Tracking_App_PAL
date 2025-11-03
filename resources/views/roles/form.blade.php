@extends('Layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center flex-wrap grid-margin mb-3">
  <div>
    <h4 class="mb-0">{{ isset($role) ? 'Edit Role' : 'Create New Role' }}</h4>
    <p class="text-muted">{{ isset($role) ? 'Update role information and permissions' : 'Create a new role with permissions' }}</p>
  </div>
  <div>
    <a href="{{ route('roles.index') }}" class="btn btn-secondary">
      <i data-feather="arrow-left" class="icon-sm me-2"></i> Back to List
    </a>
  </div>
</div>

<div class="card">
  <div class="card-body">
    <form method="POST" action="{{ isset($role) ? route('roles.update', $role) : route('roles.store') }}">
      @csrf
      @if(isset($role))
        @method('PUT')
      @endif

      <div class="row">
        <div class="col-md-6 mb-3">
          <label for="name" class="form-label">Role Name <span class="text-danger">*</span></label>
          <input type="text" 
                 class="form-control @error('name') is-invalid @enderror" 
                 id="name" 
                 name="name" 
                 value="{{ old('name', $role->name ?? '') }}" 
                 required
                 maxlength="120">
          @error('name')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
          <small class="form-text text-muted">Example: SC_Approver, Legal_Reviewer</small>
        </div>

        <div class="col-md-3 mb-3">
          <label for="division_id" class="form-label">Division</label>
          <select class="form-select @error('division_id') is-invalid @enderror" 
                  id="division_id" 
                  name="division_id">
            <option value="">Global Role</option>
            @foreach($divisions as $division)
              <option value="{{ $division->id }}" 
                      {{ old('division_id', $role->division_id ?? '') == $division->id ? 'selected' : '' }}>
                {{ $division->name }}
              </option>
            @endforeach
          </select>
          @error('division_id')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
          <small class="form-text text-muted">Optional: Leave empty for global role</small>
        </div>

        <div class="col-md-3 mb-3">
          <label for="hierarchy_level" class="form-label">Hierarchy Level <span class="text-danger">*</span></label>
          <input type="number" 
                 class="form-control @error('hierarchy_level') is-invalid @enderror" 
                 id="hierarchy_level" 
                 name="hierarchy_level" 
                 value="{{ old('hierarchy_level', $role->hierarchy_level ?? 0) }}" 
                 required
                 min="0">
          @error('hierarchy_level')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
          <small class="form-text text-muted">For role inheritance/ordering</small>
        </div>
      </div>

      <div class="mb-3">
        <label for="description" class="form-label">Description</label>
        <textarea class="form-control @error('description') is-invalid @enderror" 
                  id="description" 
                  name="description" 
                  rows="3"
                  placeholder="Enter role description...">{{ old('description', $role->description ?? '') }}</textarea>
        @error('description')
          <div class="invalid-feedback">{{ $message }}</div>
        @enderror
      </div>

      <div class="mb-3">
        <label class="form-label">Assign Permissions</label>
        <div class="border rounded p-3" style="max-height: 400px; overflow-y: auto;">
          @php
            $groupedPermissions = $permissions->groupBy('module');
            $checkedPermissions = isset($role) ? $role->permissions->pluck('id')->toArray() : old('permission_ids', []);
          @endphp
          
          @foreach($groupedPermissions as $module => $modulePermissions)
            <div class="mb-4">
              <h6 class="text-primary mb-2">
                <i data-feather="folder" class="icon-sm me-1"></i>
                {{ ucfirst($module) }} Module
              </h6>
              <div class="row">
                @foreach($modulePermissions as $permission)
                  <div class="col-md-6 mb-2">
                    <div class="form-check">
                      <input class="form-check-input" 
                             type="checkbox" 
                             name="permission_ids[]" 
                             value="{{ $permission->id }}" 
                             id="permission_{{ $permission->id }}"
                             {{ in_array($permission->id, $checkedPermissions) ? 'checked' : '' }}>
                      <label class="form-check-label" for="permission_{{ $permission->id }}">
                        <strong>{{ $permission->name }}</strong>
                        <small class="text-muted d-block">{{ $permission->name }}</small>
                        @if($permission->description)
                          <small class="text-muted d-block">{{ Str::limit($permission->description, 60) }}</small>
                        @endif
                      </label>
                    </div>
                  </div>
                @endforeach
              </div>
            </div>
          @endforeach
        </div>
        @error('permission_ids')
          <div class="text-danger small mt-1">{{ $message }}</div>
        @enderror
        @error('permission_ids.*')
          <div class="text-danger small mt-1">{{ $message }}</div>
        @enderror
      </div>

      <div class="mt-4">
        <button type="submit" class="btn btn-primary">
          <i data-feather="save" class="icon-sm me-2"></i> {{ isset($role) ? 'Update Role' : 'Create Role' }}
        </button>
        <a href="{{ route('roles.index') }}" class="btn btn-secondary">
          <i data-feather="x" class="icon-sm me-2"></i> Cancel
        </a>
      </div>
    </form>
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

