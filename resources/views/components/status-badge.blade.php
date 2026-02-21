@props(['status'])

@php
    $status = $status instanceof \BackedEnum ? $status->value : $status;
    $status = strtolower($status);
    $color = match ($status) {
        'active', 'paid', 'completed', 'present', 'approved', 'success' => 'success',
        'inactive', 'pending', 'late', 'partial' => 'warning',
        'cancelled', 'unpaid', 'failed', 'absent', 'rejected' => 'danger',
        'draft', 'open' => 'secondary',
        'leave' => 'info',
        default => 'secondary',
    };
@endphp

<span {{ $attributes->merge(['class' => "badge bg-$color"]) }}>
    {{ ucfirst($status) }}
</span>