<x-tabler-layout>
    <x-slot name="header">
        <h2 class="page-title">Edit Vehicle</h2>
        <div class="page-subtitle">Update vehicle information</div>
    </x-slot>

    <div class="row row-deck row-cards">
        <div class="col-md-8 mx-auto">
            <x-card>
                <x-slot name="header">
                    Vehicle Information
                </x-slot>

                <form action="{{ route('vehicles.update', $vehicle) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <x-form.input name="registration_number" label="Registration Number"
                        :value="$vehicle->registration_number" required />
                    <x-form.input name="type" label="Type" :value="$vehicle->type" placeholder="e.g., Truck, Dumper" />

                    <div class="mb-3">
                        <label class="form-label">Default/Current Location</label>
                        <select name="diesel_location_id"
                            class="form-select @error('diesel_location_id') is-invalid @enderror">
                            <option value="">None (Select Location)</option>
                            @foreach($locations as $location)
                                <option value="{{ $location->id }}" {{ old('diesel_location_id', $vehicle->diesel_location_id) == $location->id ? 'selected' : '' }}>
                                    {{ $location->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('diesel_location_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        <small class="form-hint">Assigning a location will auto-select it during diesel entry.</small>
                    </div>
                    <x-form.input name="model" label="Model" :value="$vehicle->model" />
                    <x-form.input name="transport_multiplier" label="Transport Multiplier" type="number" step="0.01"
                        :value="$vehicle->transport_multiplier" />
                    <x-form.checkbox name="is_active" label="Active" :checked="$vehicle->is_active" />

                    <hr class="my-4">

                    <div class="d-flex">
                        <x-button variant="primary" type="submit">Update Vehicle</x-button>
                        <a href="{{ route('vehicles.index') }}" class="btn btn-secondary ms-2">Cancel</a>
                    </div>
                </form>
            </x-card>
        </div>
    </div>
</x-tabler-layout>