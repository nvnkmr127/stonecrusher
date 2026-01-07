<x-tabler-layout>
    <div class="page page-center">
        <div class="container-tight py-4">
            <div class="empty">
                <div class="empty-header">500</div>
                <p class="empty-title">Server Error</p>
                <p class="empty-subtitle text-muted">
                    Whoops, something went wrong on our servers. We are working to fix it.
                </p>
                <div class="empty-action">
                    <a href="{{ route('dashboard') }}" class="btn btn-primary">
                        Take me home
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-tabler-layout>