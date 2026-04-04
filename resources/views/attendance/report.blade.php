<x-tabler-layout>
    <x-slot name="header">
        <div class="row align-items-center d-print-none">
            <div class="col">
                <div class="page-pretitle text-uppercase fw-bold text-muted" style="letter-spacing: 0.05em;">Human Resources / Payroll</div>
                <h2 class="page-title h1 fw-bold">{{ __('Monthly Attendance Report') }}</h2>
            </div>
            <div class="col-auto ms-auto">
                <div class="btn-list">
                    <a href="{{ route('attendance.report.export-pdf', ['month' => $month, 'year' => $year]) }}" class="btn btn-white shadow-sm">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-file-download" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"></path><path d="M14 3v4a1 1 0 0 0 1 1h4"></path><path d="M17 21h-10a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2z"></path><path d="M12 17v-6"></path><path d="M9 14l3 3l3 -3"></path></svg>
                        Export PDF
                    </a>
                </div>
            </div>
        </div>
    </x-slot>

    <div class="premium-header-card mb-4 overflow-hidden">
        <div class="row align-items-center">
            <div class="col-md-7">
                <form action="{{ route('attendance.report') }}" method="GET" class="d-flex align-items-center">
                    <span class="avatar avatar-lg bg-white-lt text-white me-3 border border-white-subtle" style="backdrop-filter: blur(4px);">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-calendar-user" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 21h-6a2 2 0 0 1 -2 -2v-12a2 2 0 0 1 2 -2h12a2 2 0 0 1 2 2v4.5" /><path d="M16 3v4" /><path d="M8 3v4" /><path d="M4 11h16" /><circle cx="19" cy="19" r="3" /><path d="M17 21v-2a1 1 0 0 1 1 -1h2a1 1 0 0 1 1 1v2" /></svg>
                    </span>
                    <div>
                        <div class="text-white-50 small text-uppercase fw-bold" style="letter-spacing: 1px;">PAYROLL PERIOD</div>
                        <div class="d-flex align-items-center gap-2">
                           <select name="month" class="form-select form-select-flush fw-bold fs-2 text-white bg-transparent border-0 p-0" onchange="this.form.submit()" style="width: auto; cursor: pointer;">
                                @foreach(range(1, 12) as $m)
                                    <option value="{{ $m }}" {{ $m == $month ? 'selected' : '' }} class="text-dark">
                                        {{ date('F', mktime(0, 0, 0, $m, 1)) }}
                                    </option>
                                @endforeach
                           </select>
                           <select name="year" class="form-select form-select-flush fw-bold fs-3 text-white-50 bg-transparent border-0 p-0" onchange="this.form.submit()" style="width: auto; cursor: pointer;">
                                @foreach(range(date('Y') - 5, date('Y')) as $y)
                                    <option value="{{ $y }}" {{ $y == $year ? 'selected' : '' }} class="text-dark">{{ $y }}</option>
                                @endforeach
                           </select>
                        </div>
                    </div>
                </form>
            </div>
            <div class="col-md-5 text-md-end mt-4 mt-md-0">
                <div class="p-3 bg-white-lt rounded-4 border border-white-subtle d-inline-block text-start" style="backdrop-filter: blur(8px); min-width: 250px;">
                    <div class="text-white-50 small text-uppercase fw-bold mb-1">Estimated Payout Total</div>
                    <div class="h1 mb-0 fw-bold">₹ {{ number_format($reportData->sum('remaining'), 2) }}</div>
                </div>
            </div>
        </div>

        <div class="mt-4 pt-3 border-top border-white-subtle">
            <div class="row align-items-center">
                <div class="col">
                    <div class="d-flex align-items-center gap-3">
                        <div class="badge bg-white-lt text-white text-uppercase px-2 py-1" style="font-size: 0.65rem; border: 1px solid rgba(255,255,255,0.2);">
                            Payout Cycle: 2 Months Hold
                        </div>
                        <div class="text-white-50 small">
                            Salaries for <strong>{{ \Carbon\Carbon::createFromDate($year, $month, 1)->format('F Y') }}</strong> will be released in <strong>{{ $payoutMonthName }}</strong>.
                        </div>
                    </div>
                </div>
                <div class="col-auto">
                    @php
                        $status = $payrollPeriod ? $payrollPeriod->getStatus() : 'Draft';
                        $statusColor = match($status) {
                            'Draft' => 'azure',
                            'Locked' => 'orange',
                            'Paid' => 'green',
                            default => 'secondary'
                        };
                    @endphp
                    <span class="badge bg-{{ $statusColor }} text-white text-uppercase px-3 py-2 fw-bold" style="letter-spacing: 1px;">
                        Status: {{ $status }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="premium-stats-grid mb-4">
        <div class="stat-premium-card">
            <div class="stat-icon-wrapper bg-blue-lt">
                <svg xmlns="http://www.w3.org/2000/svg" class="icon text-blue" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><circle cx="9" cy="7" r="4" /><path d="M3 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2" /><path d="M16 3.13a4 4 0 0 1 0 7.75" /><path d="M21 21v-2a4 4 0 0 0 -3 -3.85" /></svg>
            </div>
            <div class="text-muted small fw-bold text-uppercase mb-1">Active Staff</div>
            <div class="h2 mb-0 fw-bold text-blue">{{ $reportData->count() }} Employees</div>
        </div>

        <div class="stat-premium-card">
            <div class="stat-icon-wrapper bg-red-lt">
                <svg xmlns="http://www.w3.org/2000/svg" class="icon text-red" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M3 13h1" /><path d="M20 13h1" /><path d="M12 3v1" /><path d="M12 20v1" /></svg>
            </div>
            <div class="text-muted small fw-bold text-uppercase mb-1">Total Absents</div>
            <div class="h2 mb-0 fw-bold text-red">{{ $reportData->sum('absent') }} Days</div>
        </div>

        <div class="stat-premium-card">
            <div class="stat-icon-wrapper bg-orange-lt">
                <svg xmlns="http://www.w3.org/2000/svg" class="icon text-orange" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M17 8v-3a1 1 0 0 0 -1 -1h-10a2 2 0 0 0 0 4h12a1 1 0 0 1 1 1v3m0 4v3a1 1 0 0 1 -1 1h-12a2 2 0 0 1 -2 -2v-12" /></svg>
            </div>
            <div class="text-muted small fw-bold text-uppercase mb-1">Advance Deductions</div>
            <div class="h2 mb-0 fw-bold text-orange">₹ {{ number_format($reportData->sum('advances'), 2) }}</div>
        </div>
    </div>

    <!-- Data Table -->
    <div class="card border-0 shadow-sm overflow-hidden mb-5">
        <div class="card-header bg-transparent border-0 py-3 d-flex justify-content-between align-items-center">
            <h3 class="card-title fw-bold">Payroll Details</h3>
            <div class="card-actions">
                @if(!$payrollPeriod?->is_locked)
                    <form action="{{ route('attendance.report.lock') }}" method="POST" class="d-inline">
                        @csrf
                        <input type="hidden" name="month" value="{{ $month }}">
                        <input type="hidden" name="year" value="{{ $year }}">
                        <button type="submit" class="btn btn-warning shadow-sm fw-bold" onclick="return confirm('Lock this payroll?')">
                            LOCK PAYROLL
                        </button>
                    </form>
                @elseif(!$payrollPeriod?->is_released)
                    <form action="{{ route('attendance.report.release') }}" method="POST" class="d-inline">
                        @csrf
                        <input type="hidden" name="month" value="{{ $month }}">
                        <input type="hidden" name="year" value="{{ $year }}">
                        <button type="submit" class="btn btn-primary shadow-sm fw-bold">
                            RELEASE SALARY
                        </button>
                    </form>
                @endif
            </div>
        </div>
        <div class="table-responsive">
            <table class="table table-vcenter table-premium card-table border-top">
                <thead class="bg-light">
                    <tr>
                        <th>Employee Profile</th>
                        <th class="text-center">Work Days</th>
                        <th class="text-center">Unpaid Days</th>
                        <th class="text-end">Base Salary</th>
                        <th class="text-end">Adv / Ded</th>
                        <th class="text-end">Net Payable</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($reportData as $data)
                        <tr>
                            <td>
                                <div class="fw-bold fs-3 text-dark">{{ $data['user']->name }}</div>
                                <div class="small text-muted">{{ $data['user']->email }}</div>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-green-lt text-green border-0 px-2 py-1 fw-bold fs-3">{{ $data['present'] }}</span>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-red-lt text-red border-0 px-2 py-1 fw-bold fs-4">
                                    {{ ($data['absent'] + ($data['half_day'] * 0.5)) }}
                                </span>
                            </td>
                            <td class="text-end fw-bold">₹ {{ number_format($data['base_salary'], 2) }}</td>
                            <td class="text-end">
                                <div class="fw-bold text-orange">₹ {{ number_format($data['advances'] + $data['absent_deduction'], 2) }}</div>
                                <div class="small text-muted">Advances: ₹{{ number_format($data['advances'], 2) }}</div>
                            </td>
                            <td class="text-end bg-primary-lt">
                                <div class="fw-bold fs-2 text-primary">₹ {{ number_format($data['remaining'], 2) }}</div>
                                <div class="small text-muted fw-bold">PAY IN {{ strtoupper($payoutMonthName) }}</div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">No attendance data logs found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-tabler-layout>