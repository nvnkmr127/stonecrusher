@props(['active' => false, 'href' => '#'])

<li class="breadcrumb-item {{ $active ? 'active' : '' }}" {{ $active ? 'aria-current="page"' : '' }}>
    @if ($active)
        {{ $slot }}
    @else
        <a href="{{ $href }}">{{ $slot }}</a>
    @endif
</li>