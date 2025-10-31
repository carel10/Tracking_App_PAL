@extends('Layouts.app')

@section('content')
<div class="col-md-12 grid-margin stretch-card">
    <div class="card">
        <div class="card-body">
            <h6 class="card-title">Create New Permission</h6>
            
            <form method="POST" action="{{ route('permissions.store') }}">
                @csrf

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="permission_name" class="form-label">Permission Name <span class="text-danger">*</span></label>
                        <input type="text" 
                               class="form-control @error('permission_name') is-invalid @enderror" 
                               id="permission_name" 
                               name="permission_name" 
                               value="{{ old('permission_name') }}" 
                               required>
                        @error('permission_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="permission_code" class="form-label">Permission Code <span class="text-danger">*</span></label>
                        <input type="text" 
                               class="form-control @error('permission_code') is-invalid @enderror" 
                               id="permission_code" 
                               name="permission_code" 
                               value="{{ old('permission_code') }}" 
                               placeholder="e.g., users.manage"
                               required>
                        @error('permission_code')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-3">
                    <label for="category" class="form-label">Category</label>
                    <input type="text" 
                           class="form-control @error('category') is-invalid @enderror" 
                           id="category" 
                           name="category" 
                           value="{{ old('category') }}" 
                           placeholder="e.g., User Management">
                    @error('category')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mt-4">
                    <button type="submit" class="btn btn-primary me-2">
                        <i data-feather="save" class="icon-sm"></i> Save
                    </button>
                    <a href="{{ route('permissions.index') }}" class="btn btn-secondary">
                        <i data-feather="x" class="icon-sm"></i> Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

