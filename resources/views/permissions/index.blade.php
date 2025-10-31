@extends('Layouts.app')

@section('content')
<div class="container mt-4">
    <h3>Permissions</h3>
    <table class="table">
        <thead><tr><th>#</th><th>Name</th></tr></thead>
        <tbody>
            @foreach($permissions as $p)
            <tr><td>{{ $p->id }}</td><td>{{ $p->name }}</td></tr>
            @endforeach
        </tbody>
    </table>
    {{ $permissions->links() }}
</div>
@endsection
