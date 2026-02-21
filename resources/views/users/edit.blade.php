<x-tabler-layout>
    <x-slot name="header">
        <h2 class="page-title">Edit User</h2>
        <div class="page-subtitle">Update user information</div>
    </x-slot>

    <div class="row row-deck row-cards">
        <div class="col-md-8 mx-auto">
            <x-card>
                <x-slot name="header">
                    User Information
                </x-slot>

                <form action="{{ route('users.update', $user) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <x-form.input name="name" label="Full Name" :value="$user->name" required />

                    <x-form.input name="email" label="Email Address" type="email" :value="$user->email" required />

                    <x-form.input name="password" label="New Password (leave blank to keep current)" type="password" />

                    <x-form.input name="password_confirmation" label="Confirm New Password" type="password" />

                    <x-form.select name="role" label="Role" :options="$roles->pluck('name', 'name')->map(fn($r) => ucfirst($r))->toArray()" :selected="$user->roles->first()?->name ?? ''" required />

                    <x-form.input name="department" label="Department" :value="$user->department"
                        placeholder="e.g., Sales, Operations, Finance" />

                    <div class="row">
                        <div class="col-md-6">
                            <x-form.input name="base_salary" label="Monthly Base Salary" type="number" step="0.01"
                                :value="$user->base_salary" />
                        </div>
                        <div class="col-md-6">
                            <x-form.input name="daily_rate" label="Daily Rate (optional)" type="number" step="0.01"
                                :value="$user->daily_rate" placeholder="Calculated if 0" />
                        </div>
                    </div>

                    <x-form.checkbox name="is_active" label="Active User" :checked="$user->is_active" />

                    <hr class="my-4">

                    <div class="d-flex">
                        <x-button variant="primary" type="submit">Update User</x-button>
                        <a href="{{ route('users.index') }}" class="btn btn-secondary ms-2">Cancel</a>
                    </div>
                </form>
            </x-card>

            <!-- Password Reset Card -->
            @if($user->id !== auth()->id())
                <x-card class="mt-3">
                    <x-slot name="header">
                        Admin Password Reset
                    </x-slot>

                    <form action="{{ route('users.reset-password', $user) }}" method="POST">
                        @csrf

                        <x-alert type="warning">
                            <strong>Warning:</strong> This will reset the user's password. They will need to use the new
                            password to login.
                        </x-alert>

                        <x-form.input name="new_password" label="New Password" type="password" required />

                        <x-form.input name="new_password_confirmation" label="Confirm New Password" type="password"
                            required />

                        <x-button variant="danger" type="submit">Reset Password</x-button>
                    </form>
                </x-card>
            @endif
        </div>
    </div>
</x-tabler-layout>