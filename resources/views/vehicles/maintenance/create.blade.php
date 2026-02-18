<x-tabler-layout>
    <x-slot name="header">
        <x-page-header title="New Maintenance Record" subtitle="Log repair or service for a vehicle" :breadcrumbs="[
        ['label' => 'Dashboard', 'route' => 'dashboard'],
        ['label' => 'Maintenance Register', 'route' => 'vehicle-maintenance.index'],
        ['label' => 'New Record', 'active' => true],
    ]" />
    </x-slot>

    <div class="row row-cards">
        <div class="col-md-8 mx-auto">
            <x-card>
                <form action="{{ route('vehicle-maintenance.store') }}" method="POST">
                    @csrf
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label required">Start Date</label>
                                <input type="date" name="date" value="{{ old('date', date('Y-m-d')) }}"
                                    class="form-control @error('date') is-invalid @enderror" required>
                                @error('date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Est./Actual Completion Date</label>
                                <input type="date" name="completion_date" value="{{ old('completion_date') }}"
                                    class="form-control @error('completion_date') is-invalid @enderror">
                                @error('completion_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label required">Vehicle</label>
                                <select name="vehicle_id" class="form-select @error('vehicle_id') is-invalid @enderror"
                                    required>
                                    <option value="">Select Vehicle</option>
                                    @foreach($vehicles as $vehicle)
                                        <option value="{{ $vehicle->id }}" {{ old('vehicle_id') == $vehicle->id ? 'selected' : '' }}>
                                            {{ $vehicle->registration_number }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('vehicle_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label required">Current Status</label>
                                <select name="status" class="form-select @error('status') is-invalid @enderror"
                                    required>
                                    @foreach($statuses as $status)
                                        <option value="{{ $status }}" {{ old('status') == $status ? 'selected' : '' }}>
                                            {{ $status }}</option>
                                    @endforeach
                                </select>
                                @error('status') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                <small class="form-hint">Setting to 'In Progress' will mark vehicle as Under
                                    Maintenance.</small>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label required">Maintenance Type</label>
                                <select name="type" class="form-select @error('type') is-invalid @enderror" required>
                                    <option value="">Select Type</option>
                                    @foreach($types as $type)
                                        <option value="{{ $type }}" {{ old('type') == $type ? 'selected' : '' }}>{{ $type }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('type') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label required">Total Cost (Est./Actual)</label>
                                <input type="number" step="0.01" name="cost" value="{{ old('cost') }}"
                                    class="form-control @error('cost') is-invalid @enderror" placeholder="0.00"
                                    required>
                                @error('cost') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Odometer Reading</label>
                                <input type="number" name="odometer_reading" value="{{ old('odometer_reading') }}"
                                    class="form-control @error('odometer_reading') is-invalid @enderror"
                                    placeholder="Current km/hours">
                                @error('odometer_reading') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Workshop / Mechanic</label>
                                <input type="text" name="workshop_name" value="{{ old('workshop_name') }}"
                                    class="form-control @error('workshop_name') is-invalid @enderror"
                                    placeholder="Where was it serviced?">
                                @error('workshop_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-12 mb-3">
                                <label class="form-label">Work Done Description</label>
                                <textarea name="description" rows="4"
                                    class="form-control @error('description') is-invalid @enderror"
                                    placeholder="List parts changed or repairs made...">{{ old('description') }}</textarea>
                                @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Performed By</label>
                                <input type="text" name="performed_by" value="{{ old('performed_by') }}"
                                    class="form-control @error('performed_by') is-invalid @enderror"
                                    placeholder="Mechanic name">
                                @error('performed_by') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>
                    </div>
                    <div class="card-footer text-end">
                        <div class="d-flex">
                            <a href="{{ route('vehicle-maintenance.index') }}" class="btn btn-link">Cancel</a>
                            <button type="submit" class="btn btn-primary ms-auto">Save Record</button>
                        </div>
                    </div>
                </form>
            </x-card>
        </div>
    </div>
</x-tabler-layout>