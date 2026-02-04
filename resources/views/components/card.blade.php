<div {{ $attributes->merge(['class' => 'card']) }}>
    @if(isset($custom_header))
        <div class="card-header">
            {{ $custom_header }}
        </div>
    @elseif(isset($actions))
        <div class="card-header">
            @isset($header)
                <h3 class="card-title">{{ $header }}</h3>
            @endisset
            <div class="card-actions ms-auto">
                {{ $actions }}
            </div>
        </div>
    @elseif(isset($header))
        <div class="card-header">
            <h3 class="card-title">{{ $header }}</h3>
        </div>
    @endif

    <div class="card-body">
        {{ $slot }}
    </div>

    @isset($footer)
        <div class="card-footer">
            {{ $footer }}
        </div>
    @endisset
</div>