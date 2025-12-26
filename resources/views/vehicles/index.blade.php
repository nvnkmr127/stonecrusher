<x-tabler-layout>
    <x-slot name="header">
        <div class="row align-items-center">
            <div class="col">
                <h2 class="page-title">Vehicles</h2>
                <div class="page-subtitle">Manage vehicle fleet</div>
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
                    Add Vehicle
                </a>
            </div>
        </div>
    </x-slot>

    <div class="row row-deck row-cards">
        <div class="col-12">
            <x-card>
                <x-slot name="header">
                    Vehicles ({{ $vehicles->total() }})
                </x-slot>

                <x-table>
                    <thead>
                        <tr>
                            <th>Registration Number</th>
                            <th>Type</th>
                            <th>Model</th>
                            <th>Status</th>
                            <th>Created</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($vehicles as $vehicle)
                            <tr>
                                <td><strong>{{ $vehicle->registration_number }}</strong></td>
                                <td>{{ $vehicle->type ?? '-' }}</td>
                                <td>{{ $vehicle->model ?? '-' }}</td>
                                <td>
                                    @if($vehicle->is_active)
                                        <span class="badge bg-success">Active</span>
                                    @else
                                        <span class="badge bg-secondary">Inactive</span>
                                    @endif
                                </td>
                                <td>{{ $vehicle->created_at->format('M d, Y') }}</td>
                                <td>
                                    <div class="btn-list">
                                        <a href="{{ route('vehicles.edit', $vehicle) }}"
                                            class="btn btn-sm btn-primary">Edit</a>
                                        <form action="{{ route('vehicles.destroy', $vehicle) }}" method="POST"
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