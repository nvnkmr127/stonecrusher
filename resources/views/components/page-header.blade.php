@props(['title', 'subtitle' => null, 'breadcrumbs' => []])

<div class="row align-items-center d-print-none">
    <div class="col">
        @if(isset($breadcrumbs) && count($breadcrumbs) > 0)
            <div class="mb-1">
                <ol class="breadcrumb" aria-label="breadcrumbs">
                    @foreach($breadcrumbs as $crumb)
                        @if($loop->last)
                            <li class="breadcrumb-item active fs-5" aria-current="page">{{ $crumb['label'] }}</li>
                        @else
                            <li class="breadcrumb-item"><a href="{{ route($crumb['route']) }}">{{ $crumb['label'] }}</a></li>
                        @endif
                    @endforeach
                </ol>
            </div>
        @endif
        <div class="page-pretitle text-uppercase fw-extrabold text-muted mb-1" style="letter-spacing: 0.1em; font-size: 0.65rem;">{{ $subtitle ?? 'Overview' }}</div>
        <h2 class="page-title h1 fw-black text-dark tracking-tight">
            {{ $title }}
        </h2>
    </div>
    @if (isset($actions))
        <div class="col-auto ms-auto">
            <div class="btn-list">
                {{ $actions }}
            </div>
        </div>
    @endif
</div>