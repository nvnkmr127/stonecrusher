@props([
    'label',
    'value',
    'subtext' => null,
    'accentBg' => 'bg-blue-lt',
    'accentText' => '',
    'href' => null,
])
 
@php
    $tag = $href ? 'a' : 'div';
    $attrs = $href ? $attributes->merge(['href' => $href]) : $attributes;
@endphp
 
<{{ $tag }} {{ $attrs->merge(['class' => trim('stat-premium-card border-0 shadow-sm ' . ($href ? 'text-decoration-none' : ''))]) }}>
    <div class="stat-icon-wrapper {{ $accentBg }}">
        {{ $icon ?? '' }}
    </div>
    <div class="text-muted small fw-bold text-uppercase mb-1" style="letter-spacing: 0.5px;">{{ $label }}</div>
    <div class="h1 mb-0 fw-bold {{ $accentText }}">{!! $value !!}</div>
    @if($subtext)
        <div class="text-muted small mt-2">{{ $subtext }}</div>
    @endif
</{{ $tag }}>
