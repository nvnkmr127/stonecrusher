<x-tabler-layout>
    <x-slot name="header">
        <h2 class="page-title">Create New User</h2>
        <div class="page-subtitle">Add a new user to the system</div>
    </x-slot>

    <div class="row row-deck row-cards">
        <div class="col-md-8 mx-auto">
            <x-card>
                <x-slot name="header">
                    User Information
                </x-slot>

                <form action="{{ route('users.store') }}" method="POST">
                    @csrf

                    <x-form.input name="name" label="Full Name" required />

                    <x-form.input name="email" label="Email Address" type="email" required />

                    <x-form.input name="password" label="Password" type="password" required />

                    <x-form.input name="password_confirmation" label="Confirm Password" type="password" required />

                    <x-form.select name="role" label="Role" :options="$roles->pluck('name', 'name')->map(fn($r) => ucfirst($r))->toArray()" required />

                    <x-form.input name="department" label="Department" placeholder="e.g., Sales, Operations, Finance" />

                    <x-form.checkbox name="is_active" label="Active User" :checked="true" />

                    <hr class="my-4">

                    <div class="d-flex">
                        <x-button variant="primary" type="submit">Create User</x-button>
                        <a href="{{ route('users.index') }}" class="btn btn-secondary ms-2">Cancel</a>
                    </div>
                </form>
            </x-card>
        </div>
    </div>
</x-tabler-layout>