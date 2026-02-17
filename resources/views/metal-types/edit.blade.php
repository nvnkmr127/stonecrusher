<x-tabler-layout>
    <x-slot name="header">
        <h2 class="page-title">Edit Metal Type</h2>
        <div class="page-subtitle">Update metal type information</div>
    </x-slot>

    <div class="row row-deck row-cards">
        <div class="col-md-8 mx-auto">
            <x-card>
                <x-slot name="header">
                    Metal Type Information
                </x-slot>

                <form action="{{ route('metal-types.update', $metalType) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <x-form.input name="name" label="Metal Type Name" :value="$metalType->name" required />
                    <x-form.textarea name="description" label="Description" :value="$metalType->description" rows="3" />

                    <x-form.checkbox name="is_active" label="Active" :checked="$metalType->is_active" />

                    <hr class="my-4">

                    <div class="d-flex">
                        <x-button variant="primary" type="submit">Update Metal Type</x-button>
                        <a href="{{ route('metal-types.index') }}" class="btn btn-secondary ms-2">Cancel</a>
                    </div>
                </form>
            </x-card>
        </div>
    </div>
</x-tabler-layout>