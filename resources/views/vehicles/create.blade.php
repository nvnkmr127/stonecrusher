<x-tabler-layout>
    <x-slot name="header">
        <h2 class="page-title">Create Vehicle</h2>
        <div class="page-subtitle">Add a new vehicle</div>
    </x-slot>

    <div class="row row-deck row-cards">
        <div class="col-md-8 mx-auto">
            <x-card>
                <x-slot name="header">
                    Vehicle Information
                </x-slot>

                <form action="{{ route('vehicles.store') }}" method="POST">
                    @csrf

                    <x-form.input name="registration_number" label="Registration Number" required />
                    <x-form.input name="type" label="Type" placeholder="e.g., Truck, Dumper" />

                    <div class="mb-3">
                        <label class="form-label">Default/Current Operational Unit</label>
                        <select name="operational_unit_id"
                            class="form-select @error('operational_unit_id') is-invalid @enderror">
                            <option value="">None (Select Unit)</option>
                            @foreach($locations as $location)
                                <option value="{{ $location->id }}" {{ old('operational_unit_id') == $location->id ? 'selected' : '' }}>
                                    {{ $location->name }} ({{ $location->code }})
                                </option>
                            @endforeach
                        </select>
                        @error('operational_unit_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        <small class="form-hint">Assigning a unit will auto-select it during diesel entry.</small>
                    </div>
                    <x-form.input name="model" label="Model" />
                    <x-form.input name="cft" label="Vehicle CFT" type="number" step="0.01" :value="old('cft')" />
                    <x-form.checkbox name="is_active" label="Active" :checked="true" />

                    <hr class="my-4">

                    <div class="d-flex">
                        <x-button variant="primary" type="submit">Create Vehicle</x-button>
                        <a href="{{ route('vehicles.index') }}" class="btn btn-secondary ms-2">Cancel</a>
                    </div>
                </form>
            </x-card>
        </div>
    </div>
</x-tabler-layout>