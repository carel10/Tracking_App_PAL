@extends('Layouts.app')

@section('content')
<div class="container mt-5 text-center">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <h1 class="display-1">404</h1>
            <h3>Page not found</h3>
            <p>The page you are looking for could not be found.</p>
            <a href="{{ route('dashboard') }}" class="btn btn-primary">Return to dashboard</a>
        </div>
    </div>
</div>
@endsection
