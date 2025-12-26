<button type="{{ $type }}" {{ $attributes->merge(['class' => $buttonClass()]) }}>
    {{ $slot }}
</button>