<x-tabler-layout>
    <x-slot name="header">
        <div class="row g-2 align-items-center">
            <div class="col">
                <div class="page-pretitle">
                    Project Details
                </div>
                <h2 class="page-title">
                    {{ $project->name }}
                </h2>
            </div>
            <div class="col-auto ms-auto d-print-none">
                <div class="btn-list">
                    <a href="{{ route('projects.edit', $project) }}" class="btn btn-primary d-none d-sm-inline-block">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24"
                            stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round"
                            stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                            <path d="M4 20h4l10.5 -10.5a1.5 1.5 0 0 0 -4 -4l-10.5 10.5v4" />
                            <path d="M13.5 6.5l4 4" />
                        </svg>
                        Edit Project
                    </a>
                    <a href="{{ route('projects.index') }}" class="btn btn-secondary d-none d-sm-inline-block">
                        Back to List
                    </a>
                </div>
            </div>
        </div>
    </x-slot>

    <div class="row row-cards">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Overview</h3>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <div class="form-label">Client</div>
                            <div>{{ $project->client->name }}</div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-label">Location</div>
                            <div>{{ $project->location ?? 'Not specified' }}</div>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <div class="form-label">Start Date</div>
                            <div>{{ $project->start_date ? $project->start_date->format('d M, Y') : '-' }}</div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-label">End Date</div>
                            <div>{{ $project->end_date ? $project->end_date->format('d M, Y') : '-' }}</div>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <div class="form-label">Estimated Quantity</div>
                            <div>{{ number_format($project->estimated_quantity) }} Tons</div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-label">Status</div>
                            <div>
                                <span
                                    class="badge bg-{{ $project->status === 'active' ? 'green' : ($project->status === 'completed' ? 'blue' : 'secondary') }}-lt uppercase">
                                    {{ $project->status }}
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="form-label">Description</div>
                        <div class="text-muted">
                            {{ $project->description ?? 'No description provided.' }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Progress</h3>
                </div>
                <div class="card-body">
                    <div class="mb-2">
                        <label class="form-label">Completion</label>
                        <div class="progress progress-lg">
                            <div class="progress-bar bg-primary" style="width: {{ $project->progress ?? 0 }}%"
                                role="progressbar" aria-valuenow="{{ $project->progress ?? 0 }}" aria-valuemin="0"
                                aria-valuemax="100">
                                {{ $project->progress ?? 0 }}%
                            </div>
                        </div>
                    </div>
                    <p class="text-muted mt-3">
                        Update the progress bar via the "Edit Project" button to track milestones.
                    </p>
                </div>
            </div>
        </div>
    </div>

</x-tabler-layout>