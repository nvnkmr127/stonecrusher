<x-tabler-layout>
    <x-slot name="header">
        <div class="row align-items-center">
            <div class="col">
                <h2 class="page-title">User Management</h2>
                <div class="page-subtitle">Manage system users and their roles</div>
            </div>
            <div class="col-auto">
                <a href="{{ route('users.create') }}" class="btn btn-primary">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24"
                        stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round"
                        stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                        <path d="M12 5l0 14" />
                        <path d="M5 12l14 0" />
                    </svg>
                    Add User
                </a>
            </div>
        </div>
    </x-slot>

    <div class="row row-deck row-cards">
        <!-- Filters -->
        <div class="col-12">
            <x-card>
                <form method="GET" action="{{ route('users.index') }}">
                    <div class="row g-2">
                        <div class="col-md-3">
                            <input type="text" name="search" class="form-control"
                                placeholder="Search by name or email..." value="{{ request('search') }}">
                        </div>
                        <div class="col-md-2">
                            <select name="role" class="form-select">
                                <option value="">All Roles</option>
                                @foreach($roles as $role)
                                    <option value="{{ $role->name }}" {{ request('role') == $role->name ? 'selected' : '' }}>
                                        {{ ucfirst($role->name) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select name="department" class="form-select">
                                <option value="">All Departments</option>
                                @foreach($departments as $dept)
                                    <option value="{{ $dept }}" {{ request('department') == $dept ? 'selected' : '' }}>
                                        {{ $dept }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select name="status" class="form-select">
                                <option value="">All Status</option>
                                <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active
                                </option>
                                <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive
                                </option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <button type="submit" class="btn btn-primary">Filter</button>
                            <a href="{{ route('users.index') }}" class="btn btn-secondary">Clear</a>
                        </div>
                    </div>
                </form>
            </x-card>
        </div>

        <!-- Users Table -->
        <div class="col-12">
            <x-card>
                <x-slot name="header">
                    Users ({{ $users->total() }})
                </x-slot>

                <x-table>
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Department</th>
                            <th>Status</th>
                            <th>Created</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $user)
                            <tr>
                                <td>{{ $user->name }}</td>
                                <td>{{ $user->email }}</td>
                                <td>
                                    @foreach($user->roles as $role)
                                        <span class="badge bg-{{ $role->name === 'admin' ? 'danger' : 'primary' }}">
                                            {{ ucfirst($role->name) }}
                                        </span>
                                    @endforeach
                                </td>
                                <td>{{ $user->department ?? '-' }}</td>
                                <td>
                                    @if($user->is_active)
                                        <span class="badge bg-success">Active</span>
                                    @else
                                        <span class="badge bg-secondary">Inactive</span>
                                    @endif
                                </td>
                                <td>{{ $user->created_at->format('M d, Y') }}</td>
                                <td>
                                    <div class="btn-list">
                                        <a href="{{ route('users.edit', $user) }}" class="btn btn-sm btn-primary">Edit</a>

                                        @if($user->id !== auth()->id())
                                            <form action="{{ route('users.toggle-status', $user) }}" method="POST"
                                                class="d-inline">
                                                @csrf
                                                <button type="submit"
                                                    class="btn btn-sm btn-{{ $user->is_active ? 'warning' : 'success' }}">
                                                    {{ $user->is_active ? 'Deactivate' : 'Activate' }}
                                                </button>
                                            </form>

                                            <form action="{{ route('users.destroy', $user) }}" method="POST" class="d-inline"
                                                onsubmit="return confirm('Are you sure you want to delete this user?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center">No users found</td>
                            </tr>
                        @endforelse
                    </tbody>
                </x-table>

                <div class="mt-3">
                    {{ $users->links() }}
                </div>
            </x-card>
        </div>
    </div>
</x-tabler-layout>