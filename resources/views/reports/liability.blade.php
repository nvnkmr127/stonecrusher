<x-tabler-layout title="Payroll Liability Report">
    <div class="page-header d-print-none">
        <div class="container-xl">
            <div class="row g-2 align-items-center">
                <div class="col">
                    <h2 class="page-title">Payroll Liability Report</h2>
                    <div class="text-muted mt-1">Snapshot of company's financial obligations and outstanding employee
                        debts.</div>
                </div>
            </div>
        </div>
    </div>

    <div class="page-body">
        <div class="container-xl">
            <div class="row row-cards">
                <!-- Summary Stats -->
                <div class="col-sm-6 col-lg-3">
                    <x-card class="card-sm">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-auto">
                                    <span class="bg-primary text-white avatar">
                                        <svg xmlns="http://www.w3.org/2000/svg"
                                            class="icon icon-tabler icon-tabler-currency-rupee" width="24" height="24"
                                            viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                            stroke-linecap="round" stroke-linejoin="round">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                            <path d="M18 5h-11h3a4 4 0 0 1 0 8h-3l6 6" />
                                            <path d="M7 9l11 0" />
                                        </svg>
                                    </span>
                                </div>
                                <div class="col">
                                    <div class="font-weight-medium">₹ {{ number_format($totalPendingSalary, 2) }}</div>
                                    <div class="text-muted text-uppercase fw-bold" style="font-size: 0.65rem;">Total
                                        Pending Salary</div>
                                </div>
                            </div>
                        </div>
                    </x-card>
                </div>
                <div class="col-sm-6 col-lg-3">
                    <x-card class="card-sm">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-auto">
                                    <span class="bg-info text-white avatar">
                                        <svg xmlns="http://www.w3.org/2000/svg"
                                            class="icon icon-tabler icon-tabler-calendar-event" width="24" height="24"
                                            viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                            stroke-linecap="round" stroke-linejoin="round">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                            <path
                                                d="M4 5m0 2a2 2 0 0 1 2 -2h12a2 2 0 0 1 2 2v12a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2z" />
                                            <path d="M16 3l0 4" />
                                            <path d="M8 3l0 4" />
                                            <path d="M4 11l16 0" />
                                            <path d="M8 15h2v2h-2z" />
                                        </svg>
                                    </span>
                                </div>
                                <div class="col">
                                    <div class="font-weight-medium">{{ $upcomingReleaseMonth }}</div>
                                    <div class="text-muted text-uppercase fw-bold" style="font-size: 0.65rem;">Next
                                        Release Month</div>
                                </div>
                            </div>
                        </div>
                    </x-card>
                </div>
                <div class="col-sm-6 col-lg-3">
                    <x-card class="card-sm">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-auto">
                                    <span class="bg-warning text-white avatar">
                                        <svg xmlns="http://www.w3.org/2000/svg"
                                            class="icon icon-tabler icon-tabler-wallet" width="24" height="24"
                                            viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                            stroke-linecap="round" stroke-linejoin="round">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                            <path
                                                d="M17 8v-3a1 1 0 0 0 -1 -1h-10a2 2 0 0 0 0 4h12a1 1 0 0 1 1 1v3m0 4v3a1 1 0 0 1 -1 1h-12a2 2 0 0 1 -2 -2v-12" />
                                            <path d="M20 12v4h-4a2 2 0 0 1 0 -4h4z" />
                                        </svg>
                                    </span>
                                </div>
                                <div class="col">
                                    <div class="font-weight-medium">₹ {{ number_format($totalAdvancesOutstanding, 2) }}
                                    </div>
                                    <div class="text-muted text-uppercase fw-bold" style="font-size: 0.65rem;">Advances
                                        Outstanding</div>
                                </div>
                            </div>
                        </div>
                    </x-card>
                </div>
                <div class="col-sm-6 col-lg-3">
                    <x-card class="card-sm">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-auto">
                                    <span class="bg-danger text-white avatar">
                                        <svg xmlns="http://www.w3.org/2000/svg"
                                            class="icon icon-tabler icon-tabler-trending-down" width="24" height="24"
                                            viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                            stroke-linecap="round" stroke-linejoin="round">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                            <path d="M3 7l6 6l4 -4l8 8" />
                                            <path d="M21 10l0 7l-7 0" />
                                        </svg>
                                    </span>
                                </div>
                                <div class="col">
                                    <div class="font-weight-medium">{{ count($negativeCases) }} Employees</div>
                                    <div class="text-muted text-uppercase fw-bold" style="font-size: 0.65rem;">Negative
                                        Carry Forward</div>
                                </div>
                            </div>
                        </div>
                    </x-card>
                </div>

                <!-- Debtors Detail -->
                <div class="col-12">
                    <x-card>
                        <x-slot name="header">
                            <h3 class="card-title">Employee Debt Details (Negative Carry Forward)</h3>
                        </x-slot>
                        <div class="table-responsive">
                            <table class="table card-table table-vcenter">
                                <thead>
                                    <tr>
                                        <th>Employee</th>
                                        <th>Status</th>
                                        <th class="text-end">Current Debt Amount</th>
                                        <th class="text-end">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($negativeCases as $case)
                                        <tr>
                                            <td>
                                                <div class="fw-bold">{{ $case['user']->name }}</div>
                                                <div class="small text-muted">{{ $case['user']->email }}</div>
                                            </td>
                                            <td>
                                                <span class="badge bg-danger">IN DEBT</span>
                                            </td>
                                            <td class="text-end text-danger fw-bold">
                                                ₹ {{ number_format(abs($case['balance']), 2) }}
                                            </td>
                                            <td class="text-end">
                                                <a href="{{ route('attendance.report', ['employee_id' => $case['user']->id]) }}"
                                                    class="btn btn-sm btn-ghost-primary">View History</a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center py-4">No employees currently in debt.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </x-card>
                </div>
            </div>
        </div>
    </div>
</x-tabler-layout>