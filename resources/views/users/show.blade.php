<x-tabler-layout title="User Profile: {{ $user->name }}">
    <div class="page-header d-print-none">
        <div class="container-xl">
            <div class="row g-2 align-items-center">
                <div class="col">
                    <h2 class="page-title">Employee Profile</h2>
                </div>
                <div class="col-auto ms-auto d-print-none">
                    <div class="btn-list">
                        <a href="{{ route('users.edit', $user) }}" class="btn btn-primary d-none d-sm-inline-block">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-edit" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M9 7h-3a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-3" /><path d="M9 15l10 -10l3 3l-10 10l-3 0l0 -3z" /></svg>
                            Edit Profile
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="page-body">
        <div class="container-xl">
            <div class="row row-cards">
                <!-- User Basic Info -->
                <div class="col-md-4">
                    <x-card>
                        <div class="card-body text-center">
                            <span class="avatar avatar-xl mb-3 rounded">{{ substr($user->name, 0, 1) }}</span>
                            <h3 class="m-0 mb-1 fw-bold">{{ $user->name }}</h3>
                            <div class="text-muted">{{ $user->email }}</div>
                            <div class="mt-3">
                                <span class="badge bg-outline-primary">{{ $user->roles->first()?->name ?? 'No Role' }}</span>
                                <span class="badge {{ $user->is_active ? 'bg-success-lt' : 'bg-danger-lt' }}">
                                    {{ $user->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </div>
                        </div>
                        <div class="card-footer px-0 py-0">
                            <div class="list-group list-group-flush">
                                <div class="list-group-item">
                                    <div class="row">
                                        <div class="col">Department</div>
                                        <div class="col-auto text-muted">{{ $user->department ?? 'N/A' }}</div>
                                    </div>
                                </div>
                                <div class="list-group-item">
                                    <div class="row">
                                        <div class="col">Base Salary</div>
                                        <div class="col-auto text-muted">₹ {{ number_format($user->base_salary, 2) }}</div>
                                    </div>
                                </div>
                                <div class="list-group-item">
                                    <div class="row">
                                        <div class="col">Daily Rate</div>
                                        <div class="col-auto text-muted">₹ {{ number_format($user->daily_rate, 2) }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </x-card>
                </div>

                <!-- Salary History -->
                <div class="col-md-8">
                    <x-card>
                        <x-slot name="header">
                            <h3 class="card-title">Salary & Payroll History</h3>
                        </x-slot>
                        <div class="table-responsive">
                            <table class="table table-vcenter table-mobile-md card-table">
                                <thead>
                                    <tr>
                                        <th>Work Month</th>
                                        <th>Status</th>
                                        <th>Base</th>
                                        <th>Adv/Ded</th>
                                        <th>Net Payable</th>
                                        <th>Paid Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($history as $item)
                                        <tr>
                                            <td data-label="Work Month">
                                                <div class="font-weight-medium">{{ $item['month'] }}</div>
                                                <div class="text-muted small">Payable: {{ $item['payable_month'] }}</div>
                                            </td>
                                            <td data-label="Status">
                                                @php
                                                    $badgeClass = match($item['status']) {
                                                        'Draft' => 'bg-secondary-lt',
                                                        'Locked' => 'bg-warning-lt',
                                                        'Pending' => 'bg-info-lt',
                                                        'Paid' => 'bg-success-lt',
                                                        default => 'bg-secondary-lt',
                                                    };
                                                @endphp
                                                <span class="badge {{ $badgeClass }}">{{ strtoupper($item['status']) }}</span>
                                            </td>
                                            <td data-label="Base">₹ {{ number_format($item['base_salary'], 0) }}</td>
                                            <td data-label="Adv/Ded">
                                                <div class="text-danger small">- ₹ {{ number_format($item['advances'] + $item['deductions'], 0) }}</div>
                                            </td>
                                            <td data-label="Net Payable">
                                                <div class="fw-bold {{ $item['net_salary'] < 0 ? 'text-danger' : '' }}">
                                                    ₹ {{ number_format($item['net_salary'], 2) }}
                                                </div>
                                            </td>
                                            <td data-label="Paid Date" class="text-muted small">
                                                {{ $item['paid_date'] }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </x-card>
                </div>
            </div>
        </div>
    </div>
</x-tabler-layout>
