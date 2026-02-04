@props(['title', 'subtitle' => null, 'breadcrumbs' => []])

<div class="row align-items-center">
    <div class="col">
        @if(count($breadcrumbs) > 0)
            <div class="mb-1">
                <ol class="breadcrumb" aria-label="breadcrumbs">
                    @foreach($breadcrumbs as $crumb)
                        @if($loop->last)
                            <li class="breadcrumb-item active" aria-current="page">{{ $crumb['label'] }}</li>
                        @else
                            <li class="breadcrumb-item"><a href="{{ route($crumb['route']) }}">{{ $crumb['label'] }}</a></li>
                        @endif
                    @endforeach
                </ol>
            </div>
        @endif
        <h2 class="page-title">
            {{ $title }}
        </h2>
        @if ($subtitle)
            <div class="text-muted mt-1">
                {{ $subtitle }}
            </div>
        @endif
    </div>
    @if (isset($actions))
        <div class="col-auto ms-auto d-print-none">
            <div class="btn-list">
                {{ $actions }}
            </div>
        </div>
    @endif
</div>