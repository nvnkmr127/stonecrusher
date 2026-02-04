<div x-data="{ show: true }" x-show="show" x-transition.duration.300ms {{ $attributes->merge(['class' => 'alert ' . $alertClass()]) }} role="alert">
    <div class="d-flex">
        <div>
            {{ $slot }}
        </div>
        @if($dismissible)
            <a class="btn-close" role="button" @click="show = false" aria-label="close"></a>
        @endif
    </div>
</div>