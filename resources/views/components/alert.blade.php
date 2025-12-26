<div {{ $attributes->merge(['class' => 'alert ' . $alertClass()]) }} role="alert">
    <div class="d-flex">
        <div>
            {{ $slot }}
        </div>
        @if($dismissible)
            <a class="btn-close" data-bs-dismiss="alert" aria-label="close"></a>
        @endif
    </div>
</div>