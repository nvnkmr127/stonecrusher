<div {{ $attributes->merge(['class' => 'table-responsive']) }}>
    <table class="table table-vcenter card-table">
        {{ $slot }}
    </table>
</div>