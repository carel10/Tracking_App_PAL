@extends('Layouts.app')

@section('content')
<div class="container mt-5 text-center">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <h1 class="display-1">500</h1>
            <h3>Server error</h3>
            <p>Something went wrong on our side. Please try again later.</p>
            <a href="{{ route('dashboard') }}" class="btn btn-primary">Return to dashboard</a>
        </div>
    </div>
</div>
@endsection
