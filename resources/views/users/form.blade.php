@extends('Layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center flex-wrap grid-margin mb-3">
  <div>
    <h4 class="mb-0">{{ isset($user) ? 'Edit User' : 'Create New User' }}</h4>
    <p class="text-muted">{{ isset($user) ? 'Update user information' : 'Add a new user to the system' }}</p>
  </div>
  <div>
    <a href="{{ route('users.index') }}" class="btn btn-secondary">
      <i data-feather="arrow-left" class="icon-sm me-2"></i> Back to List
    </a>
  </div>
</div>

<div class="card">
  <div class="card-body">
    <form method="POST" action="{{ isset($user) ? route('users.update', $user) : route('users.store') }}">
      @csrf
      @if(isset($user))
        @method('PUT')
      @endif

      <div class="row">
        <div class="col-md-6 mb-3">
          <label for="full_name" class="form-label">Full Name <span class="text-danger">*</span></label>
          <input type="text" 
                 class="form-control @error('full_name') is-invalid @enderror" 
                 id="full_name" 
                 name="full_name" 
                 value="{{ old('full_name', $user->full_name ?? '') }}" 
                 required>
          @error('full_name')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>

        <div class="col-md-6 mb-3">
          <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
          <input type="email" 
                 class="form-control @error('email') is-invalid @enderror" 
                 id="email" 
                 name="email" 
                 value="{{ old('email', $user->email ?? '') }}" 
                 required>
          @error('email')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>
      </div>

      <div class="row">
        <div class="col-md-6 mb-3">
          <label for="password" class="form-label">
            Password 
            @if(!isset($user))
              <span class="text-danger">*</span>
            @else
              <small class="text-muted">(Leave blank to keep current password)</small>
            @endif
          </label>
          <input type="password" 
                 class="form-control @error('password') is-invalid @enderror" 
                 id="password" 
                 name="password"
                 {{ !isset($user) ? 'required' : '' }}
                 minlength="8">
          @error('password')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>

        @if(isset($user))
        <div class="col-md-6 mb-3">
          <label for="password_confirmation" class="form-label">
            Confirm Password 
            <small class="text-muted">(if changing password)</small>
          </label>
          <input type="password" 
                 class="form-control @error('password_confirmation') is-invalid @enderror" 
                 id="password_confirmation" 
                 name="password_confirmation"
                 minlength="8">
          @error('password_confirmation')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>
        @else
        <div class="col-md-6 mb-3">
          <label for="password_confirmation" class="form-label">Confirm Password <span class="text-danger">*</span></label>
          <input type="password" 
                 class="form-control @error('password_confirmation') is-invalid @enderror" 
                 id="password_confirmation" 
                 name="password_confirmation" 
                 required
                 minlength="8">
          @error('password_confirmation')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>
        @endif
      </div>

      <div class="row">
        <div class="col-md-6 mb-3">
          <label for="division_id" class="form-label">Division <span class="text-danger">*</span></label>
          <select class="form-select @error('division_id') is-invalid @enderror" 
                  id="division_id" 
                  name="division_id" 
                  required>
            <option value="">Select Division</option>
            @foreach($divisions as $division)
              <option value="{{ $division->id }}" 
                      {{ old('division_id', $user->division_id ?? '') == $division->id ? 'selected' : '' }}>
                {{ $division->name }}
              </option>
            @endforeach
          </select>
          @error('division_id')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>

        <div class="col-md-6 mb-3">
          <label for="status" class="form-label">Status</label>
          <select class="form-select @error('status') is-invalid @enderror" 
                  id="status" 
                  name="status">
            <option value="pending" {{ old('status', $user->status ?? 'pending') == 'pending' ? 'selected' : '' }}>Pending</option>
            <option value="active" {{ old('status', $user->status ?? '') == 'active' ? 'selected' : '' }}>Active</option>
            <option value="inactive" {{ old('status', $user->status ?? '') == 'inactive' ? 'selected' : '' }}>Inactive</option>
            <option value="suspended" {{ old('status', $user->status ?? '') == 'suspended' ? 'selected' : '' }}>Suspended</option>
          </select>
          @error('status')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>
      </div>

      <div class="mb-3">
        <label class="form-label">Assign Roles</label>
        <div class="row">
          @foreach($roles as $role)
            <div class="col-md-4 mb-2">
              <div class="form-check">
                <input class="form-check-input" 
                       type="checkbox" 
                       name="role_ids[]" 
                       value="{{ $role->id }}" 
                       id="role_{{ $role->id }}"
                       {{ (isset($user) && $user->roles->contains($role->id)) || in_array($role->id, old('role_ids', [])) ? 'checked' : '' }}>
                <label class="form-check-label" for="role_{{ $role->id }}">
                  {{ $role->name }}
                  @if($role->division)
                    <small class="text-muted d-block">({{ $role->division->name }})</small>
                  @else
                    <small class="text-muted d-block">(Global)</small>
                  @endif
                  @if($role->description)
                    <small class="text-muted d-block">{{ Str::limit($role->description, 50) }}</small>
                  @endif
                </label>
              </div>
            </div>
          @endforeach
        </div>
        @error('role_ids')
          <div class="text-danger small">{{ $message }}</div>
        @enderror
        @error('role_ids.*')
          <div class="text-danger small">{{ $message }}</div>
        @enderror
      </div>

      <div class="mt-4">
        <button type="submit" class="btn btn-primary">
          <i data-feather="save" class="icon-sm me-2"></i> {{ isset($user) ? 'Update User' : 'Create User' }}
        </button>
        <a href="{{ route('users.index') }}" class="btn btn-secondary">
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
