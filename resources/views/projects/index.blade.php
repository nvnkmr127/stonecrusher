<x-tabler-layout>
    <x-slot name="header">
        <div class="row align-items-center">
            <div class="col">
                <h2 class="page-title">Projects</h2>
                <div class="page-subtitle">Manage won projects</div>
            </div>
            <div class="col-auto">
                <a href="{{ route('projects.create') }}" class="btn btn-primary">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24"
                        stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round"
                        stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                        <path d="M12 5l0 14" />
                        <path d="M5 12l14 0" />
                    </svg>
                    Add Project
                </a>
            </div>
        </div>
    </x-slot>

    <div class="row row-deck row-cards">
        <div class="col-12">
            <x-card>
                <x-slot name="header">
                    Projects ({{ $projects->total() }})
                </x-slot>

                <x-table>
                    <thead>
                        <tr>
                            <th>Project Name</th>
                            <th>Client</th>
                            <th>Location</th>
                            <th>Quantity (Tons)</th>
                            <th>Progress</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($projects as $project)
                            <tr>
                                <td>
                                    <a href="{{ route('projects.show', $project) }}"
                                        class="text-reset fw-bold">{{ $project->name }}</a>
                                </td>
                                <td>{{ $project->client->name }}</td>
                                <td>{{ $project->location ?? '-' }}</td>
                                <td>{{ $project->estimated_quantity ? number_format($project->estimated_quantity, 2) : '-' }}
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="progress flex-fill" style="height: 8px;">
                                            <div class="progress-bar" role="progressbar"
                                                style="width: {{ $project->progress ?? 0 }}%"
                                                aria-valuenow="{{ $project->progress ?? 0 }}" aria-valuemin="0"
                                                aria-valuemax="100"></div>
                                        </div>
                                        <span class="ms-2 text-muted">{{ $project->progress ?? 0 }}%</span>
                                    </div>
                                </td>
                                <td>
                                    <span
                                        class="badge bg-{{ match ($project->status) { 'pending' => 'secondary', 'active' => 'primary', 'completed' => 'success', 'cancelled' => 'danger', default => 'secondary'} }}">
                                        {{ ucfirst($project->status) }}
                                    </span>
                                </td>
                                <td>
                                    <div class="btn-list">
                                        <a href="{{ route('projects.edit', $project) }}"
                                            class="btn btn-sm btn-primary">Edit</a>
                                        <form action="{{ route('projects.destroy', $project) }}" method="POST"
                                            class="d-inline" onsubmit="return confirm('Are you sure?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center">No projects found</td>
                            </tr>
                        @endforelse
                    </tbody>
                </x-table>

                <div class="mt-3">
                    {{ $projects->links() }}
                </div>
            </x-card>
        </div>
    </div>
</x-tabler-layout>