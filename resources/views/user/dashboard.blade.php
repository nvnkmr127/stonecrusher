<x-tabler-layout>
    <x-slot name="header">
        <h2 class="page-title">
            {{ __('User Dashboard') }}
        </h2>
        <div class="page-subtitle">
            <div class="row align-items-center">
                <div class="col-auto">
                    Welcome, {{ Auth::user()->name }}
                </div>
            </div>
        </div>
    </x-slot>

    <div class="row row-deck row-cards">
        <!-- Welcome Card -->
        <div class="col-12">
            <x-alert type="info">
                <strong>Welcome!</strong> This is your personal dashboard. You can manage your profile and view your
                activity here.
            </x-alert>
        </div>

        <!-- Profile Card -->
        <div class="col-md-6">
            <x-card>
                <x-slot name="header">
                    My Profile
                </x-slot>

                <div class="row align-items-center mb-3">
                    <div class="col-auto">
                        <span class="avatar avatar-xl" style="background-image: url(...)">
                            {{ substr(Auth::user()->name, 0, 2) }}
                        </span>
                    </div>
                    <div class="col">
                        <h3 class="mb-0">{{ Auth::user()->name }}</h3>
                        <p class="text-muted mb-0">{{ Auth::user()->email }}</p>
                        <p class="text-muted small">
                            Role:
                            @foreach(Auth::user()->roles as $role)
                                <span class="badge bg-primary">{{ $role->name }}</span>
                            @endforeach
                        </p>
                    </div>
                </div>

                <x-slot name="footer">
                    <x-button variant="primary" size="sm">Edit Profile</x-button>
                    <x-button variant="secondary" size="sm">Change Password</x-button>
                </x-slot>
            </x-card>
        </div>

        <!-- Quick Stats -->
        <div class="col-md-6">
            <x-card>
                <x-slot name="header">
                    Activity Summary
                </x-slot>

                <div class="list-group list-group-flush">
                    <div class="list-group-item">
                        <div class="row align-items-center">
                            <div class="col-auto">
                                <span class="badge bg-success"></span>
                            </div>
                            <div class="col">
                                <div class="text-truncate">
                                    <strong>Account Status:</strong> Active
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="list-group-item">
                        <div class="row align-items-center">
                            <div class="col-auto">
                                <span class="badge bg-info"></span>
                            </div>
                            <div class="col">
                                <div class="text-truncate">
                                    <strong>Member Since:</strong> {{ Auth::user()->created_at->format('M d, Y') }}
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="list-group-item">
                        <div class="row align-items-center">
                            <div class="col-auto">
                                <span class="badge bg-warning"></span>
                            </div>
                            <div class="col">
                                <div class="text-truncate">
                                    <strong>Last Login:</strong> {{ Auth::user()->updated_at->diffForHumans() }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </x-card>
        </div>

        <!-- Recent Activity -->
        <div class="col-12">
            <x-card>
                <x-slot name="header">
                    Recent Activity
                </x-slot>

                <x-table>
                    <thead>
                        <tr>
                            <th>Activity</th>
                            <th>Date</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Profile Updated</td>
                            <td>{{ now()->subDays(2)->format('M d, Y') }}</td>
                            <td><span class="badge bg-success">Completed</span></td>
                        </tr>
                        <tr>
                            <td>Password Changed</td>
                            <td>{{ now()->subDays(5)->format('M d, Y') }}</td>
                            <td><span class="badge bg-success">Completed</span></td>
                        </tr>
                        <tr>
                            <td>Account Created</td>
                            <td>{{ Auth::user()->created_at->format('M d, Y') }}</td>
                            <td><span class="badge bg-info">Initial</span></td>
                        </tr>
                    </tbody>
                </x-table>
            </x-card>
        </div>

        <!-- Component Demo -->
        <div class="col-12">
            <x-card>
                <x-slot name="header">
                    Contact Support
                </x-slot>

                <form>
                    <x-form.input name="subject" label="Subject" required placeholder="Enter subject" />

                    <x-form.select name="category" label="Category" :options="['technical' => 'Technical Issue', 'billing' => 'Billing', 'general' => 'General Inquiry']" required />

                    <x-form.textarea name="message" label="Message" rows="5" required
                        placeholder="Describe your issue or question..." />

                    <x-form.checkbox name="urgent" label="Mark as urgent" />

                    <div class="mt-3">
                        <x-button variant="primary" type="submit">Send Message</x-button>
                        <x-button variant="secondary">Cancel</x-button>
                    </div>
                </form>
            </x-card>
        </div>
    </div>
</x-tabler-layout>