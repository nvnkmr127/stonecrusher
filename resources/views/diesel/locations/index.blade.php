<x-tabler-layout>
    <x-slot name="header">
        <x-page-header title="Diesel Locations" subtitle="Manage locations for diesel consumption" :breadcrumbs="[
        ['label' => 'Dashboard', 'route' => 'dashboard'],
        ['label' => 'Diesel Register', 'route' => 'diesel.index'],
        ['label' => 'Locations', 'active' => true],
    ]">
            <x-slot name="actions">
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addLocationModal">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24"
                        stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round"
                        stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                        <path d="M12 5l0 14" />
                        <path d="M5 12l14 0" />
                    </svg>
                    Add Location
                </button>
            </x-slot>
        </x-page-header>
    </x-slot>

    <div class="row row-cards">
        <div class="col-md-12">
            <x-card>
                <div class="table-responsive">
                    <x-table>
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Status</th>
                                <th>Created At</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($locations as $location)
                                <tr>
                                    <td>{{ $location->name }}</td>
                                    <td>
                                        @if($location->is_active)
                                            <span class="badge bg-success">Active</span>
                                        @else
                                            <span class="badge bg-secondary">Inactive</span>
                                        @endif
                                    </td>
                                    <td>{{ $location->created_at->format('d M, Y') }}</td>
                                    <td class="text-end">
                                        <div class="btn-list justify-content-end">
                                            <button type="button" class="btn btn-sm btn-ghost-primary"
                                                data-bs-toggle="modal"
                                                data-bs-target="#editLocationModal{{ $location->id }}">
                                                Edit
                                            </button>
                                            <form action="{{ route('diesel-locations.destroy', $location) }}" method="POST"
                                                onsubmit="return confirm('Are you sure?')">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-ghost-danger">Delete</button>
                                            </form>
                                        </div>

                                        <!-- Edit Modal -->
                                        <div class="modal modal-blur fade" id="editLocationModal{{ $location->id }}"
                                            tabindex="-1" role="dialog" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered" role="document">
                                                <div class="modal-content text-start">
                                                    <form action="{{ route('diesel-locations.update', $location) }}"
                                                        method="POST">
                                                        @csrf @method('PUT')
                                                        <div class="modal-header">
                                                            <h5 class="modal-title">Edit Location</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                                aria-label="Close"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <div class="mb-3">
                                                                <label class="form-label required">Location Name</label>
                                                                <input type="text" name="name" class="form-control"
                                                                    value="{{ $location->name }}" required>
                                                            </div>
                                                            <div class="mb-3">
                                                                <label class="form-check form-switch">
                                                                    <input class="form-check-input" type="checkbox"
                                                                        name="is_active" value="1" {{ $location->is_active ? 'checked' : '' }}>
                                                                    <span class="form-check-label">Is Active</span>
                                                                </label>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-link link-secondary"
                                                                data-bs-dismiss="modal">Cancel</button>
                                                            <button type="submit" class="btn btn-primary ms-auto">Update
                                                                Location</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center py-4 text-muted">No locations found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </x-table>
                </div>
            </x-card>
        </div>
    </div>

    <!-- Add Modal -->
    <div class="modal modal-blur fade" id="addLocationModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <form action="{{ route('diesel-locations.store') }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">New Location</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label required">Location Name</label>
                            <input type="text" name="name" class="form-control" placeholder="e.g. Site A, Workshop"
                                required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-link link-secondary"
                            data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary ms-auto">Add Location</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-tabler-layout>