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
                            <div class="form-label">Client / Type</div>
                            <div>
                                @if($project->is_internal)
                                    <span class="badge bg-info-lt">Internal Project</span>
                                @else
                                    {{ $project->client->name ?? 'No Client Assigned' }}
                                @endif
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-label">Location</div>
                            <div>{{ $project->location ?? 'Not specified' }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="row row-cards">
                <div class="col-12">
                    <div class="card card-sm">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-auto">
                                    <span class="bg-primary text-white avatar">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24"
                                            viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                            stroke-linecap="round" stroke-linejoin="round">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                            <path d="M7 17m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0" />
                                            <path d="M17 17m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0" />
                                            <path d="M5 17h-2v-4m-1 -8h11v12m-4 0h6m4 0h2v-6h-8m0 -5h5l3 5" />
                                            <line x1="3" y1="9" x2="7" y2="9" />
                                        </svg>
                                    </span>
                                </div>
                                <div class="col">
                                    <div class="font-weight-medium">
                                        {{ $totalTrips }} Total Trips
                                    </div>
                                    <div class="text-muted">
                                        Recorded under project
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12">
                    <div class="card card-sm">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-auto">
                                    <span class="bg-success text-white avatar">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24"
                                            viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                            stroke-linecap="round" stroke-linejoin="round">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                            <path d="M12 3v18" />
                                            <path d="M16 7h-6a2 2 0 0 0 0 4h4a2 2 0 0 1 0 4h-6" />
                                            <path d="M12 21v-2" />
                                            <path d="M12 3v2" />
                                        </svg>
                                    </span>
                                </div>
                                <div class="col">
                                    <div class="font-weight-medium">
                                        {{ number_format($totalCft, 2) }} CFT
                                    </div>
                                    <div class="text-muted">
                                        Cumulative Volume
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12">
            <x-card>
                <x-slot name="header">
                    Trips / Gate Passes
                </x-slot>

                <x-table>
                    <thead>
                        <tr>
                            <th>GP Number</th>
                            <th>Date</th>
                            <th>Vehicle Number</th>
                            <th>Material Type</th>
                            <th>Weight / Quantity (CFT)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($gatePasses as $gp)
                            <tr>
                                <td>
                                    <a href="{{ route('gate-passes.show', $gp) }}"
                                        class="text-reset fw-bold">{{ $gp->gate_pass_number }}</a>
                                </td>
                                <td>{{ $gp->date->format('d M, Y h:i A') }}</td>
                                <td>{{ $gp->vehicle->registration_number ?? $gp->manual_vehicle_number ?? '-' }}</td>
                                <td>{{ $gp->metalType->name ?? '-' }}</td>
                                <td>{{ $gp->net_weight }} CFT</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5">
                                    <x-empty-state title="No Trips Found"
                                        description="There are currently no gate passes linked to this project." />
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </x-table>

                @if($gatePasses->hasPages())
                    <div class="mt-3">
                        {{ $gatePasses->links() }}
                    </div>
                @endif
            </x-card>
        </div>
    </div>

</x-tabler-layout>