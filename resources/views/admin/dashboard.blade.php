<x-tabler-layout>
    <x-slot name="header">
        <h2 class="page-title">
            {{ __('Admin Dashboard') }}
        </h2>
        <div class="page-subtitle">
            <div class="row align-items-center">
                <div class="col-auto">
                    Welcome back, {{ Auth::user()->name }}
                </div>
            </div>
        </div>
    </x-slot>

    <!-- Project Stats Row -->
    <div class="row row-deck row-cards mb-3">
        <div class="col-sm-6 col-lg-3">
            <x-card>
                <div class="subheader">Total Projects</div>
                <div class="h1 mb-3">{{ $projectStats['total'] }}</div>
                <div class="d-flex mb-2">
                    <div>All projects</div>
                </div>
            </x-card>
        </div>
        <div class="col-sm-6 col-lg-3">
            <x-card>
                <div class="subheader">Active Projects</div>
                <div class="h1 mb-3 text-primary">{{ $projectStats['active'] }}</div>
                <div class="d-flex mb-2">
                    <div>Currently in progress</div>
                </div>
            </x-card>
        </div>
        <div class="col-sm-6 col-lg-3">
            <x-card>
                <div class="subheader">Completed Projects</div>
                <div class="h1 mb-3 text-success">{{ $projectStats['completed'] }}</div>
                <div class="d-flex mb-2">
                    <div>Successfully finished</div>
                </div>
            </x-card>
        </div>
        <div class="col-sm-6 col-lg-3">
            <x-card>
                <div class="subheader">Pending Projects</div>
                <div class="h1 mb-3 text-secondary">{{ $projectStats['pending'] }}</div>
                <div class="d-flex mb-2">
                    <div>Awaiting start</div>
                </div>
            </x-card>
        </div>
    </div>

    <!-- Recent Projects -->
    <div class="row row-deck row-cards mb-3">
        <div class="col-12">
            <x-card>
                <x-slot name="header">
                    <div class="d-flex align-items-center">
                        <div>Recent Projects</div>
                        <div class="ms-auto">
                            <a href="{{ route('projects.index') }}" class="btn btn-sm btn-primary">View All</a>
                        </div>
                    </div>
                </x-slot>

                <x-table>
                    <thead>
                        <tr>
                            <th>Project</th>
                            <th>Client</th>
                            <th>Location</th>
                            <th>Progress</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentProjects as $project)
                        <tr>
                            <td><strong>{{ $project->name }}</strong></td>
                            <td>{{ $project->client->name }}</td>
                            <td>{{ $project->location ?? '-' }}</td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="progress flex-fill" style="height: 6px; width: 100px;">
                                        <div class="progress-bar" style="width: {{ $project->progress ?? 0 }}%"></div>
                                    </div>
                                    <span class="ms-2 text-muted small">{{ $project->progress ?? 0 }}%</span>
                                </div>
                            </td>
                            <td>
                                @php
                                    $statusColors = [
                                        'pending' => 'secondary',
                                        'active' => 'primary',
                                        'completed' => 'success',
                                        'cancelled' => 'danger'
                                    ];
                                @endphp
                                <span class="badge bg-{{ $statusColors[$project->status] ?? 'secondary' }}">
                                    {{ ucfirst($project->status) }}
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center">No projects yet</td>
                        </tr>
                        @endforelse
                    </tbody>
                </x-table>
            </x-card>
        </div>
    </div>

    <!-- Component Examples -->
    <div class="row row-deck row-cards">
        <div class="col-12">
            <x-card>
                <x-slot name="header">
                    Component Library Examples
                </x-slot>

                <!-- Alert Examples -->
                <h3 class="mb-3">Alerts</h3>
                <x-alert type="success" dismissible>
                    <strong>Success!</strong> Your action was completed successfully.
                </x-alert>
                <x-alert type="info">
                    <strong>Info:</strong> This is an informational message.
                </x-alert>
                <x-alert type="warning" dismissible>
                    <strong>Warning:</strong> Please review this carefully.
                </x-alert>
                <x-alert type="danger">
                    <strong>Error:</strong> Something went wrong.
                </x-alert>

                <hr class="my-4">

                <!-- Button Examples -->
                <h3 class="mb-3">Buttons</h3>
                <div class="btn-list">
                    <x-button variant="primary">Primary Button</x-button>
                    <x-button variant="secondary">Secondary</x-button>
                    <x-button variant="success">Success</x-button>
                    <x-button variant="danger">Danger</x-button>
                    <x-button variant="warning">Warning</x-button>
                    <x-button variant="info">Info</x-button>
                </div>

                <hr class="my-4">

                <!-- Table Example -->
                <h3 class="mb-3">Data Table</h3>
                <x-table>
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach(\App\Models\User::limit(5)->get() as $user)
                        <tr>
                            <td>{{ $user->name }}</td>
                            <td>{{ $user->email }}</td>
                            <td>
                                @foreach($user->roles as $role)
                                    <span class="badge bg-primary">{{ $role->name }}</span>
                                @endforeach
                            </td>
                            <td>
                                <x-button variant="primary" size="sm">Edit</x-button>
                                <x-button variant="danger" size="sm">Delete</x-button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </x-table>
            </x-card>
        </div>

        <!-- Form Example -->
        <div class="col-md-6">
            <x-card>
                <x-slot name="header">
                    Form Components Example
                </x-slot>

                <form>
                    <x-form.input 
                        name="username" 
                        label="Username" 
                        required 
                        placeholder="Enter username"
                    />

                    <x-form.input 
                        name="email" 
                        label="Email Address" 
                        type="email" 
                        required 
                    />

                    <x-form.select 
                        name="role" 
                        label="User Role" 
                        :options="['admin' => 'Administrator', 'user' => 'Regular User']"
                        required
                    />

                    <x-form.textarea 
                        name="bio" 
                        label="Biography" 
                        rows="4"
                    />

                    <x-form.checkbox 
                        name="active" 
                        label="Active User"
                        :checked="true"
                    />

                    <div class="mt-3">
                        <x-button variant="primary" type="submit">Submit Form</x-button>
                        <x-button variant="secondary">Cancel</x-button>
                    </div>
                </form>
            </x-card>
        </div>

        <div class="col-md-6">
            <x-card>
                <x-slot name="header">
                    Quick Actions
                </x-slot>

                <div class="list-group list-group-flush">
                    <a href="#" class="list-group-item list-group-item-action">
                        <div class="row align-items-center">
                            <div class="col-auto">
                                <span class="avatar">U</span>
                            </div>
                            <div class="col text-truncate">
                                <div class="text-reset d-block">Manage Users</div>
                                <div class="text-muted text-truncate mt-n1">
                                    View and manage all users
                                </div>
                            </div>
                        </div>
                    </a>
                    <a href="#" class="list-group-item list-group-item-action">
                        <div class="row align-items-center">
                            <div class="col-auto">
                                <span class="avatar">R</span>
                            </div>
                            <div class="col text-truncate">
                                <div class="text-reset d-block">Roles & Permissions</div>
                                <div class="text-muted text-truncate mt-n1">
                                    Configure access control
                                </div>
                            </div>
                        </div>
                    </a>
                    <a href="#" class="list-group-item list-group-item-action">
                        <div class="row align-items-center">
                            <div class="col-auto">
                                <span class="avatar">S</span>
                            </div>
                            <div class="col text-truncate">
                                <div class="text-reset d-block">System Settings</div>
                                <div class="text-muted text-truncate mt-n1">
                                    Application configuration
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            </x-card>
        </div>
    </div>
</x-tabler-layout>