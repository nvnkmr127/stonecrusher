<x-tabler-layout>
    <x-slot name="header">
        <h2 class="page-title">Create Metal Type</h2>
        <div class="page-subtitle">Add a new metal type</div>
    </x-slot>

    <div class="row row-deck row-cards">
        <div class="col-md-8 mx-auto">
            <x-card>
                <x-slot name="header">
                    Metal Type Information
                </x-slot>

                <form action="{{ route('metal-types.store') }}" method="POST">
                    @csrf

                    <x-form.input name="name" label="Metal Type Name" required />
                    <x-form.textarea name="description" label="Description" rows="3" />
                    <x-form.input name="unit_price" label="Unit Price" type="number" step="0.01" :value="'0.00'"
                        required />
                    <x-form.checkbox name="is_active" label="Active" :checked="true" />

                    <hr class="my-4">

                    <div class="d-flex">
                        <x-button variant="primary" type="submit">Create Metal Type</x-button>
                        <a href="{{ route('metal-types.index') }}" class="btn btn-secondary ms-2">Cancel</a>
                    </div>
                </form>
            </x-card>
        </div>
    </div>
</x-tabler-layout>