<x-tabler-layout title="Employee Profile">
    <x-slot name="header">
        <div class="row align-items-center">
            <div class="col">
                <h2 class="page-title">Employee Profile: {{ $employee->name }}</h2>
                <div class="page-subtitle">View payroll details and monthly salary/advance logs</div>
            </div>
            <div class="col-auto">
                <a href="{{ route('employees.edit', $employee) }}" class="btn btn-primary me-2">Edit Profile</a>
                <a href="{{ route('employees.index') }}" class="btn btn-secondary">Back to List</a>
            </div>
        </div>
    </x-slot>

    <div class="row row-cards">
        <!-- Overview Card -->
        <div class="col-md-4">
            <x-card>
                <x-slot name="header">Profile Details</x-slot>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label text-muted">Name</label>
                        <div class="fw-bold fs-3">{{ $employee->name }}</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-muted">Role</label>
                        <div>
                            <span class="badge bg-purple-lt fs-4">
                                {{ ucfirst($employee->role) }}
                            </span>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-muted">Status</label>
                        <div>
                            @if($employee->is_active)
                                <span class="badge bg-success">Active</span>
                            @else
                                <span class="badge bg-secondary">Inactive</span>
                            @endif
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-muted">Base Salary (Monthly)</label>
                        <div class="fw-bold fs-3">₹ {{ number_format($employee->base_salary, 2) }}</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-muted">Daily Rate</label>
                        <div class="fw-bold fs-3">₹ {{ number_format($employee->daily_rate, 2) }}</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-muted">Operational Unit</label>
                        <div>
                            @if($employee->operationalUnit)
                                <span class="badge bg-blue-lt">
                                    {{ $employee->operationalUnit->name }} ({{ ucfirst($employee->operationalUnit->type) }})
                                </span>
                            @else
                                <span class="text-muted">None</span>
                            @endif
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-muted">Linked System User</label>
                        <div>
                            @if($employee->user)
                                <span class="text-indigo fw-bold">{{ $employee->user->name }}</span>
                                <div class="small text-muted">{{ $employee->user->email }}</div>
                            @else
                                <span class="text-muted">None</span>
                            @endif
                        </div>
                    </div>
                </div>
            </x-card>
        </div>

        <!-- Payroll History Card -->
        <div class="col-md-8">
            <x-card>
                <x-slot name="header">Payroll & Disbursal History</x-slot>
                <div class="table-responsive">
                    <table class="table table-vcenter card-table">
                        <thead>
                            <tr>
                                <th>Work Month</th>
                                <th>Payout Month</th>
                                <th class="text-end">Base</th>
                                <th class="text-end">Deductions</th>
                                <th class="text-end">Advances</th>
                                <th class="text-end">Net Salary</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($history as $item)
                                <tr>
                                    <td class="fw-bold">{{ $item['month'] }}</td>
                                    <td>{{ $item['payable_month'] }}</td>
                                    <td class="text-end text-muted">₹ {{ number_format($item['base_salary'], 2) }}</td>
                                    <td class="text-end text-danger">₹ {{ number_format($item['deductions'], 2) }}</td>
                                    <td class="text-end text-orange">
                                        <span title="@foreach($item['advances_list'] as $adv) ₹{{ $adv->amount }} ({{ $adv->date->format('d M') }})@if(!$loop->last), @endif @endforeach">
                                            ₹ {{ number_format($item['advances'], 2) }}
                                        </span>
                                    </td>
                                    <td class="text-end fw-bold {{ $item['net_salary'] >= 0 ? 'text-success' : 'text-danger' }}">
                                        ₹ {{ number_format($item['net_salary'], 2) }}
                                    </td>
                                    <td>
                                        @php
                                            $color = match($item['status']) {
                                                'Paid' => 'success',
                                                'Locked' => 'warning',
                                                default => 'secondary'
                                            };
                                        @endphp
                                        <span class="badge bg-{{ $color }}">{{ $item['status'] }}</span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-4 text-muted">No payroll history found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </x-card>
        </div>
    </div>
</x-tabler-layout>
