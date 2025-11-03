@php
    $breadcrumbs = $breadcrumbs ?? [];
@endphp

@if(count($breadcrumbs) > 0)
<nav aria-label="breadcrumb" class="mb-3">
    <ol class="breadcrumb mb-0">
        @foreach($breadcrumbs as $index => $breadcrumb)
            @if($index === count($breadcrumbs) - 1)
                <li class="breadcrumb-item active" aria-current="page">
                    {{ $breadcrumb['title'] }}
                </li>
            @else
                <li class="breadcrumb-item">
                    <a href="{{ $breadcrumb['url'] ?? '#' }}">{{ $breadcrumb['title'] }}</a>
                </li>
            @endif
        @endforeach
    </ol>
</nav>
@endif

