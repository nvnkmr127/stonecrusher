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
                    <x-form.input name="model" label="Model" />
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