@extends('Layouts.app')

@section('content')
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8 text-center">
            <div class="mb-4">
                <i data-feather="lock" class="icon-lg text-danger" style="width: 120px; height: 120px;"></i>
            </div>
            <h1 class="display-1 fw-bold text-danger">403</h1>
            <h3 class="mb-3">Forbidden</h3>
            <p class="text-muted mb-4">
                You don't have permission to access this resource.
            </p>
            <div class="alert alert-warning" role="alert">
                <i data-feather="alert-triangle" class="icon-sm me-2"></i>
                <strong>Access Denied:</strong> You don't have the required permissions to view this page.
            </div>
            <div class="mt-4">
                @auth
                    <a href="{{ route('dashboard') }}" class="btn btn-primary me-2">
                        <i data-feather="home" class="icon-sm me-2"></i> Return to Dashboard
                    </a>
                @else
                    <a href="{{ route('login') }}" class="btn btn-primary me-2">
                        <i data-feather="log-in" class="icon-sm me-2"></i> Go to Login
                    </a>
                @endauth
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

