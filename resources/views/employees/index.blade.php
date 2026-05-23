<x-tabler-layout title="Employee Management">
    <x-slot name="header">
        <div class="row align-items-center">
            <div class="col">
                <h2 class="page-title">Employee Management</h2>
                <div class="page-subtitle">Manage staff, payroll metrics, and operational unit assignments</div>
            </div>
            <div class="col-auto">
                <a href="{{ route('employees.create') }}" class="btn btn-primary">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24"
                        stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round"
                        stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                        <path d="M12 5l0 14" />
                        <path d="M5 12l14 0" />
                    </svg>
                    Add Employee
                </a>
            </div>
        </div>
    </x-slot>

    <div class="row row-deck row-cards">
        <!-- Filters -->
        <div class="col-12">
            <x-card>
                <form method="GET" action="{{ route('employees.index') }}">
                    <div class="row g-2">
                        <div class="col-md-3">
                            <input type="text" name="search" class="form-control"
                                placeholder="Search by name..." value="{{ request('search') }}">
                        </div>
                        <div class="col-md-2">
                            <select name="role" class="form-select">
                                <option value="">All Roles</option>
                                @foreach($roles as $key => $value)
                                    <option value="{{ $key }}" {{ request('role') == $key ? 'selected' : '' }}>
                                        {{ $value }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select name="operational_unit_id" class="form-select">
                                <option value="">All Units</option>
                                @foreach($operationalUnits as $unit)
                                    <option value="{{ $unit->id }}" {{ request('operational_unit_id') == $unit->id ? 'selected' : '' }}>
                                        {{ $unit->name }} ({{ ucfirst($unit->type) }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select name="status" class="form-select">
                                <option value="">All Status</option>
                                <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                                <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <button type="submit" class="btn btn-primary">Filter</button>
                            <a href="{{ route('employees.index') }}" class="btn btn-secondary">Clear</a>
                        </div>
                    </div>
                </form>
            </x-card>
        </div>

        <!-- Employees Table -->
        <div class="col-12">
            <x-card>
                <x-slot name="header">
                    Employees ({{ $employees->total() }})
                </x-slot>

                <x-table>
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Role</th>
                            <th>Base Salary</th>
                            <th>Daily Rate</th>
                            <th>Operational Unit</th>
                            <th>Linked User</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($employees as $employee)
                            <tr>
                                <td>
                                    <a href="{{ route('employees.show', $employee) }}" class="fw-bold">
                                        {{ $employee->name }}
                                    </a>
                                </td>
                                <td>
                                    <span class="badge bg-purple-lt">
                                        {{ $roles[$employee->role] ?? ucfirst($employee->role) }}
                                    </span>
                                </td>
                                <td>₹ {{ number_format($employee->base_salary, 2) }}</td>
                                <td>₹ {{ number_format($employee->daily_rate, 2) }}</td>
                                <td>
                                    @if($employee->operationalUnit)
                                        <span class="badge bg-blue-lt">
                                            {{ $employee->operationalUnit->name }}
                                        </span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    @if($employee->user)
                                        <span class="text-indigo">{{ $employee->user->name }}</span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    @if($employee->is_active)
                                        <span class="badge bg-success">Active</span>
                                    @else
                                        <span class="badge bg-secondary">Inactive</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-list">
                                        <a href="{{ route('employees.show', $employee) }}"
                                            class="btn btn-sm btn-ghost-primary">View Profile</a>
                                        <a href="{{ route('employees.edit', $employee) }}" class="btn btn-sm btn-primary">Edit</a>

                                        <form action="{{ route('employees.toggle-status', $employee) }}" method="POST"
                                            class="d-inline">
                                            @csrf
                                            <button type="submit"
                                                class="btn btn-sm btn-{{ $employee->is_active ? 'warning' : 'success' }} text-white">
                                                {{ $employee->is_active ? 'Deactivate' : 'Activate' }}
                                            </button>
                                        </form>

                                        <form action="{{ route('employees.destroy', $employee) }}" method="POST" class="d-inline"
                                            onsubmit="return confirm('Are you sure you want to delete this employee?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center">No employees found</td>
                            </tr>
                        @endforelse
                    </tbody>
                </x-table>

                <div class="mt-3">
                    {{ $employees->links() }}
                </div>
            </x-card>
        </div>
    </div>
</x-tabler-layout>
