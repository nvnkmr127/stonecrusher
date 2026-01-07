<x-tabler-layout>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center">
            <h2 class="page-title">
                {{ __('Backups') }}
            </h2>
            <div class="d-flex gap-2">
                <a href="{{ route('google-drive.redirect') }}" class="btn btn-outline-secondary">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 15l-3.5 -5h7z" /><path d="M9 7l-3.5 5h7z" /><path d="M15 7l-3.5 5h7z" /><path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" /></svg>
                    Connect Google Drive
                </a>
                <form action="{{ route('backups.create') }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-primary"
                        onclick="return confirm('Are you sure you want to start a new backup? This might take a while.')">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24"
                            stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round"
                            stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                            <path d="M12 5l0 14" />
                            <path d="M5 12l14 0" />
                        </svg>
                        {{ __('Create New Backup') }}
                    </button>
                </form>
            </div>
        </div>
    </x-slot>

    <div class="row row-cards">
        <div class="col-12">
            <!-- Messages -->
            @if (session('success'))
                <div class="alert alert-success alert-dismissible" role="alert">
                    {{ session('success') }}
                    <a href="#" class="btn-close" data-bs-dismiss="alert" aria-label="close"></a>
                </div>
            @endif
            @if (session('error'))
                <div class="alert alert-danger alert-dismissible" role="alert">
                    {{ session('error') }}
                    <a href="#" class="btn-close" data-bs-dismiss="alert" aria-label="close"></a>
                </div>
            @endif

            <div class="card">
                <div class="table-responsive">
                    <table class="table table-vcenter card-table">
                        <thead>
                            <tr>
                                <th>File Name</th>
                                <th>Size</th>
                                <th>Date</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($backups as $backup)
                                <tr>
                                    <td class="fw-medium text-break">
                                        {{ $backup['name'] }}
                                    </td>
                                    <td class="text-muted">
                                        {{ $backup['size'] }}
                                    </td>
                                    <td class="text-muted">
                                        {{ $backup['date'] }}
                                    </td>
                                    <td class="text-end">
                                        <div class="d-flex justify-content-end gap-2">
                                            <a href="{{ route('backups.download', ['disk' => $backup['disk'], 'path' => $backup['path']]) }}"
                                                class="btn btn-sm btn-outline-primary">Download</a>

                                            <!-- Restore Button -->
                                            <form action="{{ route('backups.restore') }}" method="POST"
                                                onsubmit="return confirm('DANGER: This will overwrite your current database with the backup. Are you sure?');">
                                                @csrf
                                                <input type="hidden" name="disk" value="{{ $backup['disk'] }}">
                                                <input type="hidden" name="path" value="{{ $backup['path'] }}">
                                                <button type="submit" class="btn btn-sm btn-outline-warning">
                                                    Restore
                                                </button>
                                            </form>

                                            <form action="{{ route('backups.destroy') }}" method="POST"
                                                onsubmit="return confirm('Are you sure you want to delete this backup?');">
                                                @csrf
                                                @method('DELETE')
                                                <input type="hidden" name="disk" value="{{ $backup['disk'] }}">
                                                <input type="hidden" name="path" value="{{ $backup['path'] }}">
                                                <button type="submit" class="btn btn-sm btn-outline-danger">
                                                    Delete
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted">No backups found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-tabler-layout>