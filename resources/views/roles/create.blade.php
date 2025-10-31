@extends('Layouts.app')

@section('content')
<div class="col-md-12 grid-margin stretch-card">
    <div class="card">
        <div class="card-body">
            <h6 class="card-title">Create New Role</h6>
            
            <form method="POST" action="{{ route('roles.store') }}">
                @csrf

                <div class="mb-3">
                    <label for="role_name" class="form-label">Role Name <span class="text-danger">*</span></label>
                    <input type="text" 
                           class="form-control @error('role_name') is-invalid @enderror" 
                           id="role_name" 
                           name="role_name" 
                           value="{{ old('role_name') }}" 
                           required>
                    @error('role_name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="role_description" class="form-label">Description</label>
                    <textarea class="form-control @error('role_description') is-invalid @enderror" 
                              id="role_description" 
                              name="role_description" 
                              rows="3">{{ old('role_description') }}</textarea>
                    @error('role_description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="permissions" class="form-label">Permissions</label>
                    <select class="form-select @error('permissions') is-invalid @enderror" 
                            id="permissions" 
                            name="permissions[]" 
                            multiple>
                        @foreach($permissions as $permission)
                            <option value="{{ $permission->permission_id }}">
                                {{ $permission->permission_name }} ({{ $permission->permission_code }})
                            </option>
                        @endforeach
                    </select>
                    <small class="form-text text-muted">Hold Ctrl/Cmd to select multiple permissions</small>
                    @error('permissions')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mt-4">
                    <button type="submit" class="btn btn-primary me-2">
                        <i data-feather="save" class="icon-sm"></i> Save
                    </button>
                    <a href="{{ route('roles.index') }}" class="btn btn-secondary">
                        <i data-feather="x" class="icon-sm"></i> Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

