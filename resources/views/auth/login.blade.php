@extends('Layouts.auth')

@section('content')
<div class="container d-flex align-items-center justify-content-center" style="min-height: 100vh; padding: 2rem;">
    <div class="row w-100 justify-content-center">
        <div class="col-md-5 col-lg-4">
            <div class="card shadow-sm">
                <div class="card-body p-5">
                    <div class="text-center mb-4">
                        <h3 class="mb-2">Welcome Back</h3>
                        <p class="text-muted">Sign in to access your account</p>
                    </div>

                    @if($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ $errors->first() }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('login.post') }}">
                        @csrf
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" 
                                   name="email" 
                                   id="email" 
                                   class="form-control @error('email') is-invalid @enderror" 
                                   placeholder="Enter your email"
                                   value="{{ old('email') }}"
                                   required 
                                   autofocus>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" 
                                   name="password" 
                                   id="password" 
                                   class="form-control @error('password') is-invalid @enderror" 
                                   placeholder="Enter your password"
                                   required>
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-grid mb-4">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i data-feather="log-in" class="me-2"></i>Login
                            </button>
                        </div>
                    </form>

                    <div class="text-center mb-4">
                        <hr class="my-4">
                        <p class="text-muted mb-2">Or continue with</p>
                        <a href="{{ route('sso.login') }}" class="btn btn-outline-secondary w-100">
                            <i data-feather="key" class="me-2"></i>Sign in with SSO
                        </a>
                    </div>

                    <div class="text-center">
                        <a href="{{ route('password.forgot') }}" class="text-decoration-none">
                            <small class="text-muted">Forgot password?</small>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Initialize feather icons
    if (typeof feather !== 'undefined') {
        feather.replace();
    }
</script>
@endsection
