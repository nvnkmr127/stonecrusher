<x-tabler-layout>
    <x-slot name="header">
        <h2 class="page-title">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="row row-cards">
        <div class="col-12">
            <div class="card card-md">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col">
                            <h3 class="h3 mb-1">Welcome back, {{ Auth::user()->name }}!</h3>
                            <div class="text-secondary">This is your personal dashboard. Track your activity and manage
                                your profile here.</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Your Profile</h3>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <div class="row">
                            <div class="col-4 text-secondary">Name:</div>
                            <div class="col-8 fw-bold">{{ Auth::user()->name }}</div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="row">
                            <div class="col-4 text-secondary">Email:</div>
                            <div class="col-8 fw-bold">{{ Auth::user()->email }}</div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="row">
                            <div class="col-4 text-secondary">Role:</div>
                            <div class="col-8">
                                <span class="badge bg-green-lt">
                                    {{ ucfirst(Auth::user()->getRoleNames()->first() ?? 'User') }}
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="mt-4">
                        <a href="{{ route('profile.edit') }}" class="btn btn-primary">Edit Profile</a>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Quick Actions</h3>
                </div>
                <div class="list-group list-group-flush">
                    <a href="#" class="list-group-item list-group-item-action">
                        <div class="d-flex align-items-center">
                            <div class="col">
                                <div class="font-weight-medium text-primary">My Orders</div>
                                <div class="text-secondary small">View your order history and status</div>
                            </div>
                        </div>
                    </a>
                    <a href="#" class="list-group-item list-group-item-action">
                        <div class="d-flex align-items-center">
                            <div class="col">
                                <div class="font-weight-medium text-primary">Support</div>
                                <div class="text-secondary small">Contact admin for assistance</div>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-tabler-layout>