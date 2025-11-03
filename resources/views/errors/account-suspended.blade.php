@extends('Layouts.app')

@section('content')
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8 text-center">
            <div class="mb-4">
                <i data-feather="user-x" class="icon-lg text-danger" style="width: 120px; height: 120px;"></i>
            </div>
            <h1 class="display-4 fw-bold text-danger mb-3">Account Suspended</h1>
            <h3 class="mb-3">Your account has been deactivated</h3>
            <p class="text-muted mb-4">
                Your account is currently inactive. Please contact the administrator for assistance.
            </p>
            <div class="alert alert-danger" role="alert">
                <i data-feather="alert-circle" class="icon-sm me-2"></i>
                <strong>Account Status:</strong> Your account has been suspended or deactivated by the administrator.
                <br>
                <small>If you believe this is an error, please contact your system administrator.</small>
            </div>
            <div class="card mt-4">
                <div class="card-body">
                    <h6 class="card-title">What should you do?</h6>
                    <ul class="list-unstyled text-start">
                        <li class="mb-2">
                            <i data-feather="mail" class="icon-sm me-2 text-primary"></i>
                            Contact your system administrator or IT support team
                        </li>
                        <li class="mb-2">
                            <i data-feather="phone" class="icon-sm me-2 text-primary"></i>
                            Provide your account email address for verification
                        </li>
                        <li>
                            <i data-feather="help-circle" class="icon-sm me-2 text-primary"></i>
                            Wait for account reactivation approval
                        </li>
                    </ul>
                </div>
            </div>
            <div class="mt-4">
                <form action="{{ route('logout') }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-secondary">
                        <i data-feather="log-out" class="icon-sm me-2"></i> Logout
                    </button>
                </form>
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

