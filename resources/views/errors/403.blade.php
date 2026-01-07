<x-tabler-layout>
    <div class="page page-center">
        <div class="container-tight py-4">
            <div class="empty">
                <div class="empty-header">403</div>
                <p class="empty-title">Forbidden</p>
                <p class="empty-subtitle text-muted">
                    You don't have permission to access this resource.
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