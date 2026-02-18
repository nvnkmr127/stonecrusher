<x-tabler-layout>
    <x-slot name="header">
        <x-page-header title="Vehicle Maintenance Register" subtitle="Track repairs and service history" :breadcrumbs="[
        ['label' => 'Dashboard', 'route' => 'dashboard'],
        ['label' => 'Vehicles', 'route' => 'vehicles.index'],
        ['label' => 'Maintenance', 'active' => true],
    ]">
            <x-slot name="actions">
                <a href="{{ route('vehicle-maintenance.create') }}" class="btn btn-primary">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24"
                        stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round"
                        stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                        <path d="M12 5l0 14" />
                        <path d="M5 12l14 0" />
                    </svg>
                    New Record
                </a>
            </x-slot>
        </x-page-header>
    </x-slot>

    <div class="row row-cards">
        <!-- Summary Cards -->
        <div class="col-md-3">
            <x-card class="bg-primary text-primary-fg">
                <div class="card-body">
                    <div class="subheader text-primary-fg opacity-50">Total Maintenance (Range)</div>
                    <div class="h1 mb-3">{{ number_format($totalCost, 2) }}</div>
                </div>
            </x-card>
        </div>
        <div class="col-md-3">
            <x-card>
                <div class="card-body">
                    <div class="subheader">Highest Repair Cost</div>
                    <div class="h1 mb-3">
                        @php $highest = $perVehicle->sortByDesc('total_cost')->first(); @endphp
                        {{ $highest ? number_format($highest->total_cost, 0) : '0' }}
                    </div>
                </div>
            </x-card>
        </div>
        <div class="col-md-6">
            <x-card>
                <div class="card-body">
                    <div class="subheader">Top Maintenance Vehicle</div>
                    <div class="h3 mb-3">
                        {{ $highest ? $highest->vehicle->registration_number : '-' }}
                    </div>
                </div>
            </x-card>
        </div>

        <!-- Filter and Table -->
        <div class="col-lg-9">
            <x-card>
                <div class="card-body border-bottom py-3">
                    <form method="GET" action="{{ route('vehicle-maintenance.index') }}" class="row g-2">
                        <div class="col-md-2">
                            <input type="date" name="start_date" value="{{ $startDate }}"
                                class="form-control form-control-sm">
                        </div>
                        <div class="col-md-2">
                            <input type="date" name="end_date" value="{{ $endDate }}"
                                class="form-control form-control-sm">
                        </div>
                        <div class="col-md-2">
                            <select name="vehicle_id" class="form-select form-select-sm">
                                <option value="">All Vehicles</option>
                                @foreach($vehicles as $vehicle)
                                    <option value="{{ $vehicle->id }}" {{ request('vehicle_id') == $vehicle->id ? 'selected' : '' }}>
                                        {{ $vehicle->registration_number }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select name="type" class="form-select form-select-sm">
                                <option value="">All Types</option>
                                @foreach($types as $type)
                                    <option value="{{ $type }}" {{ request('type') == $type ? 'selected' : '' }}>{{ $type }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select name="status" class="form-select form-select-sm">
                                <option value="">All Statuses</option>
                                @foreach($statuses as $status)
                                    <option value="{{ $status }}" {{ request('status') == $status ? 'selected' : '' }}>
                                        {{ $status }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-sm btn-primary w-100">Filter</button>
                        </div>
                    </form>
                </div>

                <div class="table-responsive">
                    <x-table>
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Vehicle</th>
                                <th>Type</th>
                                <th>Status</th>
                                <th class="text-end">Cost</th>
                                <th>Workshop</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($maintenances as $record)
                                <tr>
                                    <td>
                                        {{ $record->date->format('d M, Y') }}
                                        @if($record->completion_date && $record->status != 'Completed')
                                            <div class="text-muted small">Est: {{ $record->completion_date->format('d M') }}
                                            </div>
                                        @endif
                                    </td>
                                    <td>{{ $record->vehicle->registration_number }}</td>
                                    <td><span class="badge bg-purple-lt">{{ $record->type }}</span></td>
                                    <td>
                                        <span
                                            class="badge bg-{{ match ($record->status) { 'Pending' => 'warning', 'In Progress' => 'azure', 'Completed' => 'success', default => 'secondary'} }}">
                                            {{ $record->status }}
                                        </span>
                                    </td>
                                    <td class="text-end fw-bold">{{ number_format($record->cost, 2) }}</td>
                                    <td>{{ $record->workshop_name ?? '-' }}</td>
                                    <td>
                                        <div class="btn-list">
                                            @if($record->status != 'Completed')
                                                <form action="{{ route('vehicle-maintenance.complete', $record) }}"
                                                    method="POST" class="d-inline">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-success"
                                                        title="Mark as Completed">
                                                        <svg xmlns="http://www.w3.org/2000/svg"
                                                            class="icon icon-tabler icon-tabler-check" width="24" height="24"
                                                            viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
                                                            fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                            <path d="M5 12l5 5l10 -10" />
                                                        </svg>
                                                        Done
                                                    </button>
                                                </form>
                                            @endif
                                            <a href="{{ route('vehicle-maintenance.edit', $record) }}"
                                                class="btn btn-sm btn-ghost-primary">Edit</a>
                                            <form action="{{ route('vehicle-maintenance.destroy', $record) }}" method="POST"
                                                onsubmit="return confirm('Are you sure?')">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-ghost-danger">Delete</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-4 text-muted">No maintenance records found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </x-table>
                </div>
                <div class="card-footer d-flex align-items-center">
                    {{ $maintenances->links() }}
                </div>
            </x-card>
        </div>

        <!-- Sidebar Summary -->
        <div class="col-lg-3">
            <x-card>
                <x-slot name="header">
                    <h3 class="card-title">Cost Per Vehicle</h3>
                </x-slot>
                <div class="table-responsive">
                    <table class="table table-vcenter card-table">
                        <thead>
                            <tr>
                                <th>Vehicle</th>
                                <th class="text-end">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($perVehicle->sortByDesc('total_cost') as $row)
                                <tr>
                                    <td>{{ $row->vehicle->registration_number }}</td>
                                    <td class="text-end fw-bold">{{ number_format($row->total_cost, 0) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </x-card>

            <x-card class="mt-3">
                <x-slot name="header">
                    <h3 class="card-title">Ongoing Maintenance</h3>
                </x-slot>
                <div class="list-group list-group-flush">
                    @foreach($ongoingMaintenance as $active)
                        <div class="list-group-item">
                            <div class="row align-items-center">
                                <div class="col text-truncate">
                                    <div class="text-reset d-block">{{ $active->vehicle->registration_number }}</div>
                                    <div class="d-block text-muted text-truncate mt-n1">
                                        {{ $active->type }} (Started {{ $active->date->format('M d') }})
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                    @if($ongoingMaintenance->isEmpty())
                        <div class="list-group-item text-center text-muted small">No ongoing repairs</div>
                    @endif
                </div>
            </x-card>
        </div>
    </div>
</x-tabler-layout>