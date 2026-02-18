<x-tabler-layout>
    <x-slot name="header">
        <x-page-header title="New Diesel Entry" subtitle="Register diesel consumption for a vehicle" :breadcrumbs="[
        ['label' => 'Dashboard', 'route' => 'dashboard'],
        ['label' => 'Diesel Register', 'route' => 'diesel.index'],
        ['label' => 'New Entry', 'active' => true],
    ]" />
    </x-slot>

    <div class="row row-cards">
        <div class="col-md-8 mx-auto">
            <x-card>
                <form action="{{ route('diesel.store') }}" method="POST">
                    @csrf
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label required">Date</label>
                                <input type="date" name="date" value="{{ old('date', date('Y-m-d')) }}"
                                    class="form-control @error('date') is-invalid @enderror" required>
                                @error('date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label required">Vehicle</label>
                                <select name="vehicle_id" id="vehicle_id" class="form-select @error('vehicle_id') is-invalid @enderror"
                                    required>
                                    <option value="">Select Vehicle</option>
                                    @foreach($vehicles as $vehicle)
                                        <option value="{{ $vehicle->id }}" 
                                            data-location="{{ $vehicle->diesel_location_id }}"
                                            {{ old('vehicle_id') == $vehicle->id ? 'selected' : '' }}>
                                            {{ $vehicle->registration_number }} ({{ $vehicle->type }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('vehicle_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label required">Liters</label>
                                <div class="input-group">
                                    <input type="number" step="0.01" name="liters" value="{{ old('liters') }}"
                                        class="form-control @error('liters') is-invalid @enderror" placeholder="0.00"
                                        required>
                                    <span class="input-group-text">L</span>
                                </div>
                                @error('liters') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label required">Driver</label>
                                <input type="text" name="driver_name" value="{{ old('driver_name') }}"
                                    class="form-control @error('driver_name') is-invalid @enderror"
                                    placeholder="Enter driver name" required>
                                @error('driver_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-12 mb-3">
                                <label class="form-label required">Location</label>
                                <select name="diesel_location_id" id="diesel_location_id"
                                    class="form-select @error('diesel_location_id') is-invalid @enderror" required>
                                    <option value="">Select Location</option>
                                    @foreach($locations as $location)
                                        <option value="{{ $location->id }}" {{ old('diesel_location_id') == $location->id ? 'selected' : '' }}>
                                            {{ $location->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('diesel_location_id') <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-hint">
                                    Don't see your location? <a href="{{ route('diesel-locations.index') }}">Manage
                                        Locations here</a>.
                                </small>
                            </div>

                            <div class="col-12 mb-3">
                                <label class="form-label required">Purpose</label>
                                <div class="form-selectgroup">
                                    <label class="form-selectgroup-item">
                                        <input type="radio" name="purpose" value="Plant" class="form-selectgroup-input"
                                            checked>
                                        <span class="form-selectgroup-label">Plant</span>
                                    </label>
                                    <label class="form-selectgroup-item">
                                        <input type="radio" name="purpose" value="JCB" class="form-selectgroup-input">
                                        <span class="form-selectgroup-label">JCB</span>
                                    </label>
                                    <label class="form-selectgroup-item">
                                        <input type="radio" name="purpose" value="Loading"
                                            class="form-selectgroup-input">
                                        <span class="form-selectgroup-label">Loading</span>
                                    </label>
                                    <label class="form-selectgroup-item">
                                        <input type="radio" name="purpose" value="Transport"
                                            class="form-selectgroup-input">
                                        <span class="form-selectgroup-label">Transport</span>
                                    </label>
                                    <label class="form-selectgroup-item">
                                        <input type="radio" name="purpose" value="Other" class="form-selectgroup-input">
                                        <span class="form-selectgroup-label">Other</span>
                                    </label>
                                </div>
                                @error('purpose') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>
                        </div>
                    </div>
                    <div class="card-footer text-end">
                        <div class="d-flex">
                            <a href="{{ route('diesel.index') }}" class="btn btn-link">Cancel</a>
                            <button type="submit" class="btn btn-primary ms-auto">Save Entry</button>
                        </div>
                    </div>
                </form>
            </x-card>
        </div>
    </div>

    @push('scripts')
    <script>
        document.getElementById('vehicle_id').addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            const locationId = selectedOption.getAttribute('data-location');
            
            if (locationId && locationId !== "") {
                document.getElementById('diesel_location_id').value = locationId;
            }
        });
    </script>
    @endpush
</x-tabler-layout>