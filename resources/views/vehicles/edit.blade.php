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
                    <x-form.input name="model" label="Model" :value="$vehicle->model" />
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