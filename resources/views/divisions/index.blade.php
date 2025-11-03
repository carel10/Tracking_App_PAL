@extends('Layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center flex-wrap grid-margin mb-3">
  <div>
    <h4 class="mb-0">Division Management</h4>
    <p class="text-muted">Align system with company organizational structure</p>
  </div>
  <div>
    <a href="{{ route('divisions.create') }}" class="btn btn-primary">
      <i data-feather="plus" class="icon-sm me-2"></i> Add Division
    </a>
  </div>
</div>

@if(session('success'))
  <div class="alert alert-success alert-dismissible fade show" role="alert">
    {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
  </div>
@endif

<div class="row">
  @forelse($divisions as $division)
    <div class="col-md-6 col-lg-4 mb-4">
      <div class="card h-100">
        <div class="card-body">
          <div class="d-flex justify-content-between align-items-start mb-3">
            <div>
              <h5 class="card-title mb-1">{{ $division->name }}</h5>
              @if($division->description)
                <p class="text-muted mb-0 small">{{ Str::limit($division->description, 80) }}</p>
              @endif
            </div>
          </div>

          <div class="d-flex gap-3 mb-3">
            <div>
              <h6 class="mb-0 text-primary">{{ $division->users_count }}</h6>
              <small class="text-muted">Users</small>
            </div>
            <div>
              <h6 class="mb-0 text-info">{{ $division->roles_count }}</h6>
              <small class="text-muted">Roles</small>
            </div>
          </div>

          <div class="d-flex gap-2">
            <a href="{{ route('divisions.edit', $division) }}" class="btn btn-sm btn-primary flex-fill" title="Edit">
              <i data-feather="edit-2" class="icon-sm me-1"></i> Edit
            </a>
            <a href="{{ route('divisions.users', $division) }}" class="btn btn-sm btn-success" title="View Users">
              <i data-feather="users" class="icon-sm"></i>
            </a>
            <a href="{{ route('divisions.roles', $division) }}" class="btn btn-sm btn-info" title="View Roles">
              <i data-feather="shield" class="icon-sm"></i>
            </a>
          </div>
        </div>
      </div>
    </div>
  @empty
    <div class="col-12">
      <div class="card">
        <div class="card-body text-center py-5">
          <i data-feather="building" class="icon-lg text-muted mb-3"></i>
          <p class="text-muted mb-0">No divisions found. Create your first division to get started.</p>
        </div>
      </div>
    </div>
  @endforelse
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

