<x-tabler-layout>
    <div class="page page-center">
        <div class="container-tight py-4">
            <x-empty-state title="404 - Page Not Found"
                description="We searched everywhere but couldn't find the page you were looking for." icon="search"
                action='<a href="{{ route("dashboard") }}" class="btn btn-primary">Take me home</a>' />
        </div>
    </div>
</x-tabler-layout>