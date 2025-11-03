@extends('Layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center flex-wrap grid-margin mb-3">
  <div>
    <h4 class="mb-0">{{ isset($division) ? 'Edit Division' : 'Create New Division' }}</h4>
    <p class="text-muted">{{ isset($division) ? 'Update division information' : 'Create a new division' }}</p>
  </div>
  <div>
    <a href="{{ route('divisions.index') }}" class="btn btn-secondary">
      <i data-feather="arrow-left" class="icon-sm me-2"></i> Back to List
    </a>
  </div>
</div>

<div class="card">
  <div class="card-body">
    <form method="POST" action="{{ isset($division) ? route('divisions.update', $division) : route('divisions.store') }}">
      @csrf
      @if(isset($division))
        @method('PUT')
      @endif

      <div class="row">
        <div class="col-md-12 mb-3">
          <label for="name" class="form-label">Division Name <span class="text-danger">*</span></label>
          <input type="text" 
                 class="form-control @error('name') is-invalid @enderror" 
                 id="name" 
                 name="name" 
                 value="{{ old('name', $division->name ?? '') }}" 
                 required
                 maxlength="100"
                 placeholder="e.g., IT, HR, Finance, Operations">
          @error('name')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
          <small class="form-text text-muted">Enter the name of the division</small>
        </div>
      </div>

      <div class="mb-3">
        <label for="description" class="form-label">Description</label>
        <textarea class="form-control @error('description') is-invalid @enderror" 
                  id="description" 
                  name="description" 
                  rows="4"
                  placeholder="Enter division description...">{{ old('description', $division->description ?? '') }}</textarea>
        @error('description')
          <div class="invalid-feedback">{{ $message }}</div>
        @enderror
        <small class="form-text text-muted">Describe the purpose and responsibilities of this division</small>
      </div>

      @if(isset($division))
        <div class="alert alert-info">
          <strong>Statistics:</strong><br>
          <small>This division currently has {{ $division->users()->count() }} user(s) and {{ $division->roles()->count() }} role(s).</small>
        </div>
      @endif

      <div class="mt-4">
        <button type="submit" class="btn btn-primary">
          <i data-feather="save" class="icon-sm me-2"></i> {{ isset($division) ? 'Update Division' : 'Create Division' }}
        </button>
        <a href="{{ route('divisions.index') }}" class="btn btn-secondary">
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

