<x-tabler-layout>
    <x-slot name="header">
        <h2 class="page-title">Edit Client</h2>
        <div class="page-subtitle">Update client information</div>
    </x-slot>

    <div class="row row-deck row-cards">
        <div class="col-md-8 mx-auto">
            <x-card>
                <x-slot name="header">
                    Client Information
                </x-slot>

                <form action="{{ route('clients.update', $client) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <x-form.input name="name" label="Client Name" :value="$client->name" required />
                    <x-form.input name="email" label="Email" type="email" :value="$client->email" />
                    <x-form.input name="phone" label="Phone" :value="$client->phone" />
                    <x-form.textarea name="address" label="Address" :value="$client->address" rows="3" />
                    <x-form.checkbox name="is_active" label="Active" :checked="$client->is_active" />

                    <hr class="my-4">

                    <div class="d-flex">
                        <x-button variant="primary" type="submit">Update Client</x-button>
                        <a href="{{ route('clients.index') }}" class="btn btn-secondary ms-2">Cancel</a>
                    </div>
                </form>
            </x-card>
        </div>
    </div>
</x-tabler-layout>