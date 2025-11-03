@extends('Layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center flex-wrap grid-margin mb-3">
  <div>
    <h4 class="mb-0">{{ isset($permission) ? 'Edit Permission' : 'Create New Permission' }}</h4>
    <p class="text-muted">{{ isset($permission) ? 'Update permission information' : 'Create a new granular access permission' }}</p>
  </div>
  <div>
    <a href="{{ route('permissions.index') }}" class="btn btn-secondary">
      <i data-feather="arrow-left" class="icon-sm me-2"></i> Back to List
    </a>
  </div>
</div>

<div class="card">
  <div class="card-body">
    <form method="POST" action="{{ isset($permission) ? route('permissions.update', $permission) : route('permissions.store') }}">
      @csrf
      @if(isset($permission))
        @method('PUT')
      @endif

      <div class="row">
        <div class="col-md-6 mb-3">
          <label for="name" class="form-label">Permission Name <span class="text-danger">*</span></label>
          <input type="text" 
                 class="form-control @error('name') is-invalid @enderror" 
                 id="name" 
                 name="name" 
                 value="{{ old('name', $permission->name ?? '') }}" 
                 required
                 maxlength="150"
                 placeholder="e.g., Review Document">
          @error('name')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
          <small class="form-text text-muted">Human-readable permission name</small>
        </div>

        <div class="col-md-6 mb-3">
          <label for="module" class="form-label">Module <span class="text-danger">*</span></label>
          <div class="input-group">
            <select class="form-select @error('module') is-invalid @enderror" 
                    id="module" 
                    name="module" 
                    required
                    onchange="document.getElementById('module_custom').value = '';">
              <option value="">Select or Enter Module</option>
              @foreach($existingModules as $moduleName)
                <option value="{{ $moduleName }}" 
                        {{ old('module', $permission->module ?? '') == $moduleName ? 'selected' : '' }}>
                  {{ ucfirst($moduleName) }}
                </option>
              @endforeach
              <option value="__custom__">+ Add New Module</option>
            </select>
            <input type="text" 
                   class="form-control @error('module') is-invalid @enderror" 
                   id="module_custom" 
                   name="module_custom"
                   placeholder="Enter new module name"
                   style="display: none;"
                   maxlength="100">
          </div>
          @error('module')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
          <small class="form-text text-muted">Examples: global, production, sc, legal, finance</small>
        </div>
      </div>

      <div class="mb-3">
        <label for="description" class="form-label">Description</label>
        <textarea class="form-control @error('description') is-invalid @enderror" 
                  id="description" 
                  name="description" 
                  rows="4"
                  placeholder="Enter permission description...">{{ old('description', $permission->description ?? '') }}</textarea>
        @error('description')
          <div class="invalid-feedback">{{ $message }}</div>
        @enderror
        <small class="form-text text-muted">Describe what this permission allows users to do</small>
      </div>

      <div class="mb-3">
        <div class="alert alert-info">
          <strong>Note:</strong> Permission code will be automatically generated as: 
          <code id="permission_code_preview">
            @if(isset($permission))
              {{ $permission->module }}.{{ Str::slug($permission->name) }}
            @else
              [module].[permission-name-slug]
            @endif
          </code>
        </div>
      </div>

      <div class="mt-4">
        <button type="submit" class="btn btn-primary">
          <i data-feather="save" class="icon-sm me-2"></i> {{ isset($permission) ? 'Update Permission' : 'Create Permission' }}
        </button>
        <a href="{{ route('permissions.index') }}" class="btn btn-secondary">
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

    const moduleSelect = document.getElementById('module');
    const moduleCustom = document.getElementById('module_custom');

    // Show custom input when "Add New Module" is selected
    moduleSelect.addEventListener('change', function() {
        if (this.value === '__custom__') {
            moduleCustom.style.display = 'block';
            moduleCustom.required = true;
            moduleSelect.required = false;
        } else {
            moduleCustom.style.display = 'none';
            moduleCustom.required = false;
            moduleSelect.required = true;
            moduleCustom.value = '';
        }
        updatePermissionCodePreview();
    });

    // Update permission code preview
    const nameInput = document.getElementById('name');
    nameInput.addEventListener('input', updatePermissionCodePreview);
    moduleSelect.addEventListener('change', updatePermissionCodePreview);
    moduleCustom.addEventListener('input', updatePermissionCodePreview);

    function updatePermissionCodePreview() {
        const name = nameInput.value || '[permission-name]';
        const module = moduleSelect.value === '__custom__' 
            ? (moduleCustom.value || '[module]')
            : (moduleSelect.value || '[module]');
        
        const slug = name.toLowerCase().replace(/[^a-z0-9]+/g, '.').replace(/(^\.|\.$)/g, '');
        const preview = module !== '__custom__' && module 
            ? module.toLowerCase() + '.' + slug
            : '[module].[permission-name-slug]';
        
        document.getElementById('permission_code_preview').textContent = preview;
    }

    // Handle form submission for custom module
    document.querySelector('form').addEventListener('submit', function(e) {
        if (moduleSelect.value === '__custom__' && moduleCustom.value) {
            // Replace the select value with custom input value
            const hiddenInput = document.createElement('input');
            hiddenInput.type = 'hidden';
            hiddenInput.name = 'module';
            hiddenInput.value = moduleCustom.value;
            this.appendChild(hiddenInput);
            moduleSelect.removeAttribute('name');
        }
    });
});
</script>
@endsection

