<x-tabler-layout>
    <x-slot name="header">
        <div class="row align-items-center">
            <div class="col">
                <h2 class="page-title">Our Vehicles</h2>
                <div class="page-subtitle">Manage our owned vehicle fleet</div>
            </div>
            <div class="col-auto">
                <a href="{{ route('vehicles.create') }}" class="btn btn-primary">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24"
                        stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round"
                        stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                        <path d="M12 5l0 14" />
                        <path d="M5 12l14 0" />
                    </svg>
                    Add Owned Vehicle
                </a>
            </div>
        </div>
    </x-slot>

    <div class="row row-deck row-cards">
        <div class="col-12">
            <x-card>
                <x-slot name="header">
                    Our Owned Vehicles ({{ $vehicles->total() }})
                </x-slot>

                <x-table>
                    <thead>
                        <tr>
                            <th>Registration Number</th>
                            <th>Type</th>
                            <th>Model</th>
                            <th>Assigned Unit</th>
                            <th>Status</th>
                            <th>Ops Status</th>
                            <th>Created</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($vehicles as $vehicle)
                            <tr>
                                <td>
                                    <a href="{{ route('vehicles.show', $vehicle) }}" class="text-reset fw-bold">
                                        {{ $vehicle->registration_number }}
                                    </a>
                                </td>
                                <td>{{ $vehicle->type ?? '-' }}</td>
                                <td>{{ $vehicle->model ?? '-' }}</td>
                                <td>
                                    @if($vehicle->operationalUnit)
                                        <span class="badge bg-azure text-azure-fg">
                                            {{ $vehicle->operationalUnit->name }} ({{ $vehicle->operationalUnit->code }})
                                        </span>
                                    @else
                                        <span class="text-muted small">Not assigned</span>
                                    @endif
                                </td>
                                <td>
                                    @if($vehicle->is_active)
                                        <span class="badge bg-success">Active</span>
                                    @else
                                        <span class="badge bg-secondary">Inactive</span>
                                    @endif
                                </td>
                                <td>
                                    <span
                                        class="badge bg-{{ match ($vehicle->operational_status) { 'Operational' => 'success', 'Under Maintenance' => 'azure', 'Broken Down' => 'danger', default => 'secondary'} }}">
                                        {{ $vehicle->operational_status }}
                                    </span>
                                </td>
                                <td>{{ $vehicle->created_at->format('M d, Y') }}</td>
                                <td>
                                    <div class="btn-list">
                                        <a href="{{ route('vehicles.edit', $vehicle) }}"
                                            class="btn btn-sm btn-outline-primary">Edit</a>

                                        <div class="dropdown">
                                            <button class="btn btn-sm btn-ghost-secondary dropdown-toggle" type="button"
                                                data-bs-toggle="dropdown">
                                                History
                                            </button>
                                            <div class="dropdown-menu">
                                                <a class="dropdown-item"
                                                    href="{{ route('vehicle-maintenance.index', ['vehicle_id' => $vehicle->id]) }}">
                                                    Maintenance Records
                                                </a>
                                                <a class="dropdown-item"
                                                    href="{{ route('diesel.index', ['vehicle_id' => $vehicle->id]) }}">
                                                    Diesel Entries
                                                </a>
                                            </div>
                                        </div>

                                        <form action="{{ route('vehicles.destroy', $vehicle) }}" method="POST"
                                            class="d-inline" onsubmit="return confirm('Are you sure?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-ghost-danger">Delete</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center">No vehicles found</td>
                            </tr>
                        @endforelse
                    </tbody>
                </x-table>

                <div class="mt-3">
                    {{ $vehicles->links() }}
                </div>
            </x-card>
        </div>
    </div>
</x-tabler-layout>