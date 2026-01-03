<x-tabler-layout>
    <x-slot name="header">
        <h2 class="page-title">Create Client</h2>
        <div class="page-subtitle">Add a new client</div>
    </x-slot>

    <div class="row row-deck row-cards">
        <div class="col-md-8 mx-auto">
            <x-card>
                <x-slot name="header">
                    Client Information
                </x-slot>

                <form action="{{ route('clients.store') }}" method="POST">
                    @csrf

                    <x-form.input name="name" label="Client Name" required />
                    <x-form.input name="email" label="Email" type="email" />
                    <x-form.input name="phone" label="Phone" />
                    <x-form.textarea name="address" label="Address" rows="3" />
                    <x-form.input name="credit_limit" label="Credit Limit" type="number" step="0.01" />
                    <x-form.textarea name="notes" label="Notes" rows="3" />
                    <x-form.checkbox name="is_active" label="Active" :checked="true" />

                    <hr class="my-4">

                    <div class="d-flex">
                        <x-button variant="primary" type="submit">Create Client</x-button>
                        <a href="{{ route('clients.index') }}" class="btn btn-secondary ms-2">Cancel</a>
                    </div>
                </form>
            </x-card>
        </div>
    </div>
</x-tabler-layout>