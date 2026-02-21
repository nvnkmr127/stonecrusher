<x-tabler-layout>
    <x-slot name="header">
        <div class="row align-items-center">
            <div class="col">
                <div class="mb-1">
                    <x-breadcrumb>
                        <x-breadcrumb-item href="{{ route('dashboard') }}">Dashboard</x-breadcrumb-item>
                        <x-breadcrumb-item href="{{ route('vehicles.index') }}">Vehicles</x-breadcrumb-item>
                        <x-breadcrumb-item active>{{ $vehicle->registration_number }}</x-breadcrumb-item>
                    </x-breadcrumb>
                </div>
                <h2 class="page-title">{{ $vehicle->registration_number }}</h2>
                <div class="page-subtitle">{{ $vehicle->type }} | {{ $vehicle->model }}</div>
            </div>
            <div class="col-auto">
                <div class="btn-list">
                    <a href="{{ route('vehicles.edit', $vehicle) }}" class="btn btn-primary">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-edit" width="24"
                            height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                            stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                            <path d="M9 7h-3a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-3" />
                            <path d="M9 15h3l8.5 -8.5a1.5 1.5 0 0 0 -3 -3l-8.5 8.5v3" />
                            <line x1="16" y1="5" x2="19" y2="8" />
                        </svg>
                        Edit Details
                    </a>
                    <a href="{{ route('vehicles.index') }}" class="btn btn-secondary">
                        Back to List
                    </a>
                </div>
            </div>
        </div>
    </x-slot>

    <div class="row row-cards">
        <!-- Quick Stats -->
        <div class="col-md-3">
            <x-card>
                <div class="card-body text-center py-4">
                    <div class="mb-3">
                        <span class="badge bg-{{ $vehicle->is_active ? 'success' : 'secondary' }} rounded-pill px-3">
                            {{ $vehicle->is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </div>
                    <div class="avatar avatar-xl mb-3 bg-primary-lt">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-truck" width="24"
                            height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                            stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                            <circle cx="7" cy="17" r="2" />
                            <circle cx="17" cy="17" r="2" />
                            <path d="M5 17h-2v-11a1 1 0 0 1 1 -1h9v12m-4 0h6m4 0h2v-6h-8m0 -5h5l3 5" />
                        </svg>
                    </div>
                    <h3 class="mb-1">{{ $vehicle->registration_number }}</h3>
                    <div class="text-muted">{{ $vehicle->type }}</div>
                </div>
                <div class="card-table border-top">
                    <table class="table table-vcenter">
                        <tbody>
                            <tr>
                                <td class="text-muted">Model</td>
                                <td class="text-end fw-bold">{{ $vehicle->model ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Unit</td>
                                <td class="text-end fw-bold">
                                    {{ $vehicle->operationalUnit->name ?? 'Unassigned' }}
                                </td>
                            </tr>
                            <tr>
                                <td class="text-muted">Ops Status</td>
                                <td class="text-end fw-bold">
                                    <span
                                        class="badge bg-{{ match ($vehicle->operational_status) { 'Operational' => 'success', 'Under Maintenance' => 'azure', 'Broken Down' => 'danger', default => 'secondary'} }}">
                                        {{ $vehicle->operational_status }}
                                    </span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </x-card>
        </div>

        <!-- History & Details -->
        <div class="col-md-9">
            <div class="card">
                <div class="card-header">
                    <ul class="nav nav-tabs card-header-tabs" data-bs-toggle="tabs">
                        <li class="nav-item">
                            <a href="#tabs-recent-trips" class="nav-link active" data-bs-toggle="tab">Recent Trips</a>
                        </li>
                        <li class="nav-item">
                            <a href="#tabs-diesel" class="nav-link" data-bs-toggle="tab">Diesel History</a>
                        </li>
                        <li class="nav-item">
                            <a href="#tabs-maintenance" class="nav-link" data-bs-toggle="tab">Maintenance</a>
                        </li>
                    </ul>
                </div>
                <div class="card-body">
                    <div class="tab-content">
                        <!-- Recent Trips -->
                        <div class="tab-pane active" id="tabs-recent-trips">
                            <h4 class="mb-3">Latest 5 Trips</h4>
                            <div class="table-responsive">
                                <table class="table table-vcenter">
                                    <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th>GP No</th>
                                            <th>Activity</th>
                                            <th>Client/Project</th>
                                            <th class="text-end">Qty</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($vehicle->gatePasses as $gp)
                                            <tr>
                                                <td>{{ $gp->date ? $gp->date->format('d/m/Y') : '-' }}</td>
                                                <td><a
                                                        href="{{ route('gate-passes.edit', $gp) }}">{{ $gp->gate_pass_number }}</a>
                                                </td>
                                                <td>{{ $gp->activity_type->value ?? 'N/A' }}</td>
                                                <td>{{ $gp->client->name ?? $gp->manual_customer_name ?? $gp->project->name ?? '-' }}
                                                </td>
                                                <td class="text-end fw-bold">{{ number_format($gp->net_weight, 2) }}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="text-center py-4 text-muted small">No recent trips
                                                    recorded</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                            <div class="mt-3">
                                <a href="{{ route('gate-passes.index', ['search' => $vehicle->registration_number]) }}"
                                    class="btn btn-ghost-primary btn-sm">View Full History</a>
                            </div>
                        </div>

                        <!-- Diesel History -->
                        <div class="tab-pane" id="tabs-diesel">
                            <h4 class="mb-3">Latest 5 Diesel Issues</h4>
                            <div class="table-responsive">
                                <table class="table table-vcenter">
                                    <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th>Liters</th>
                                            <th>Work Type</th>
                                            <th>Driver</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($vehicle->dieselEntries as $diesel)
                                            <tr>
                                                <td>{{ $diesel->date ? $diesel->date->format('d/m/Y') : '-' }}</td>
                                                <td class="fw-bold text-azure">{{ number_format($diesel->liters, 2) }} L
                                                </td>
                                                <td><span class="badge bg-secondary-lt">{{ $diesel->work_type }}</span></td>
                                                <td>{{ $diesel->driver_name }}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="4" class="text-center py-4 text-muted small">No recent diesel
                                                    logs</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                            <div class="mt-3">
                                <a href="{{ route('diesel.index', ['vehicle_id' => $vehicle->id]) }}"
                                    class="btn btn-ghost-primary btn-sm">View All Diesel Logs</a>
                            </div>
                        </div>

                        <!-- Maintenance -->
                        <div class="tab-pane" id="tabs-maintenance">
                            <h4 class="mb-3">Latest 5 Maintenance Records</h4>
                            <div class="table-responsive">
                                <table class="table table-vcenter">
                                    <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th>Type</th>
                                            <th>Description</th>
                                            <th>Cost</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($vehicle->maintenances as $maint)
                                            <tr>
                                                <td>{{ $maint->date ? $maint->date->format('d/m/Y') : '-' }}</td>
                                                <td>{{ $maint->type }}</td>
                                                <td>{{ $maint->description }}</td>
                                                <td class="fw-bold">₹{{ number_format($maint->cost ?? 0, 0) }}</td>
                                                <td>
                                                    <span
                                                        class="badge bg-{{ $maint->status === 'completed' ? 'success' : 'warning' }}">
                                                        {{ ucfirst($maint->status ?? 'Pending') }}
                                                    </span>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="text-center py-4 text-muted small">No recent
                                                    maintenance records</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                            <div class="mt-3">
                                <a href="{{ route('vehicle-maintenance.index', ['vehicle_id' => $vehicle->id]) }}"
                                    class="btn btn-ghost-primary btn-sm">Manage Maintenance</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-tabler-layout>