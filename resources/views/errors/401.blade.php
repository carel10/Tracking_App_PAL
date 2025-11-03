@extends('Layouts.app')

@section('content')
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8 text-center">
            <div class="mb-4">
                <i data-feather="shield-off" class="icon-lg text-warning" style="width: 120px; height: 120px;"></i>
            </div>
            <h1 class="display-1 fw-bold text-warning">401</h1>
            <h3 class="mb-3">Unauthorized</h3>
            <p class="text-muted mb-4">
                You need to be authenticated to access this resource.
            </p>
            <div class="alert alert-info" role="alert">
                <i data-feather="info" class="icon-sm me-2"></i>
                <strong>Authentication Required:</strong> Please log in to continue.
            </div>
            <div class="mt-4">
                <a href="{{ route('login') }}" class="btn btn-primary me-2">
                    <i data-feather="log-in" class="icon-sm me-2"></i> Go to Login Page
                </a>
                <button onclick="window.history.back()" class="btn btn-secondary">
                    <i data-feather="arrow-left" class="icon-sm me-2"></i> Go Back
                </button>
            </div>
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
});
</script>
@endsection

