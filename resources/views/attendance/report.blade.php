<x-tabler-layout>
    <x-slot name="header">
        <h2 class="page-title">
            {{ __('Monthly Attendance Report') }}
        </h2>
    </x-slot>

    <div class="row row-cards">
        <div class="col-12">
            <x-card>
                <div class="card-header">
                    <ul class="nav nav-pills card-header-pills">
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('attendance.report.daily') }}">Daily Report</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link active" href="#">Monthly Summary</a>
                        </li>
                    </ul>
                    <div class="card-actions">
                        <div class="d-flex gap-2">
                            <a href="{{ route('attendance.report.export', ['month' => $month, 'year' => $year]) }}"
                                class="btn btn-success">
                                <svg xmlns="http://www.w3.org/2000/svg"
                                    class="icon icon-tabler icon-tabler-file-spreadsheet" width="24" height="24"
                                    viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                    stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                    <path d="M14 3v4a1 1 0 0 0 1 1h4" />
                                    <path d="M17 21h-10a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2z" />
                                    <path d="M8 11h8v7h-8z" />
                                    <path d="M8 15h8" />
                                    <path d="M11 11v7" />
                                </svg>
                                Export CSV
                            </a>
                            <a href="{{ route('attendance.report.export-pdf', ['month' => $month, 'year' => $year]) }}"
                                class="btn btn-danger">
                                <svg xmlns="http://www.w3.org/2000/svg"
                                    class="icon icon-tabler icon-tabler-file-type-pdf" width="24" height="24"
                                    viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                    stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                    <path d="M14 3v4a1 1 0 0 0 1 1h4" />
                                    <path d="M17 21h-10a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2z" />
                                    <path d="M5 12v-7a2 2 0 0 1 2 -2h7l5 5v4" />
                                    <path d="M5 18h1.5a1.5 1.5 0 0 0 0 -3h-1.5v6" />
                                </svg>
                                Export PDF
                            </a>
                            <button onclick="window.print()" class="btn btn-secondary">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-printer"
                                    width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
                                    fill="none" stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                    <path
                                        d="M17 17h2a2 2 0 0 0 2 -2v-4a2 2 0 0 0 -2 -2h-14a2 2 0 0 0 -2 2v4a2 2 0 0 0 2 2h2" />
                                    <path d="M17 9v-4a2 2 0 0 0 -2 -2h-6a2 2 0 0 0 -2 2v4" />
                                    <path
                                        d="M7 13m0 2a2 2 0 0 1 2 -2h6a2 2 0 0 1 2 2v4a2 2 0 0 1 -2 2h-6a2 2 0 0 1 -2 -2z" />
                                </svg>
                                Print
                            </button>

                            @php
                                $canRelease = now()->greaterThanOrEqualTo(\Carbon\Carbon::createFromDate($year, $month, 1)->addMonths(2));
                            @endphp

                            @if(!$payrollPeriod?->is_locked)
                                <form action="{{ route('attendance.report.lock') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="month" value="{{ $month }}">
                                    <input type="hidden" name="year" value="{{ $year }}">
                                    <button type="submit" class="btn btn-warning" onclick="return confirm('Lock this payroll? Attendance and advances will no longer be editable.')">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-lock" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 11m0 2a2 2 0 0 1 2 -2h10a2 2 0 0 1 2 2v6a2 2 0 0 1 -2 2h-10a2 2 0 0 1 -2 -2z" /><path d="M12 11v-4a4 4 0 0 1 8 0v4" /><path d="M8 11v-4a4 4 0 0 1 8 0v4" /></svg>
                                        Lock Payroll
                                    </button>
                                </form>
                            @elseif(!$payrollPeriod?->is_released)
                                @if($canRelease)
                                    <form action="{{ route('attendance.report.release') }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="month" value="{{ $month }}">
                                        <input type="hidden" name="year" value="{{ $year }}">
                                        <button type="submit" class="btn btn-primary">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-cash" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M7 9m0 2a2 2 0 0 1 2 -2h10a2 2 0 0 1 2 2v6a2 2 0 0 1 -2 2h-10a2 2 0 0 1 -2 -2z" /><path d="M18 12h.01" /><path d="M11 12a2 2 0 1 0 4 0a2 2 0 0 0 -4 0" /><path d="M7 15v.01" /></svg>
                                            Release Salary
                                        </button>
                                    </form>
                                @else
                                    <button class="btn btn-secondary" title="Salary is held for 2 months" disabled>
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-hourglass-high" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M6.5 7h11" /><path d="M6.5 17h11" /><path d="M6 20v-2a6 6 0 1 1 12 0v2a1 1 0 0 1 -1 1h-10a1 1 0 0 1 -1 -1z" /><path d="M6 4v2a6 6 0 1 0 12 0v-2a1 1 0 0 0 -1 -1h-10a1 1 0 0 0 -1 1z" /></svg>
                                        Hold Ends in {{ \Carbon\Carbon::createFromDate($year, $month, 1)->addMonths(2)->format('F') }}
                                    </button>
                                @endif
                            @else
                                <button class="btn btn-outline-success" disabled>
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-check" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 12l5 5l10 -10" /></svg>
                                    Salary Released
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="card-body bg-azure-lt border-bottom">
                    <div class="row align-items-center">
                        <div class="col">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-info-circle me-1" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M3 12a9 9 0 1 0 18 0a9 9 0 0 0 -18 0" /><path d="M12 9h.01" /><path d="M12 13v4" /></svg>
                            Salary for <strong>{{ \Carbon\Carbon::createFromDate($year, $month, 1)->format('F Y') }}</strong> will be paid in <strong>{{ $payoutMonthName }}</strong> (2-month hold policy).
                        </div>
                        <div class="col-auto">
                            @php
                                $status = $payrollPeriod ? $payrollPeriod->getStatus() : 'Draft';
                                $badgeClass = match($status) {
                                    'Draft' => 'bg-secondary',
                                    'Locked' => 'bg-warning',
                                    'Pending' => 'bg-info',
                                    'Paid' => 'bg-success',
                                    default => 'bg-secondary'
                                };
                            @endphp
                            <span class="badge {{ $badgeClass }} text-white">{{ strtoupper($status) }}</span>
                        </div>
                    </div>
                </div>
                <div class="card-body border-bottom py-3">
                    <form method="GET" action="{{ route('attendance.report') }}" class="d-flex gap-2">
                        <select name="month" class="form-select w-auto">
                            @foreach(range(1, 12) as $m)
                                <option value="{{ $m }}" {{ $month == $m ? 'selected' : '' }}>
                                    {{ \Carbon\Carbon::create()->month($m)->format('F') }}
                                </option>
                            @endforeach
                        </select>
                        <select name="year" class="form-select w-auto">
                            @foreach(range(now()->year - 5, now()->year) as $y)
                                <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>
                                    {{ $y }}
                                </option>
                            @endforeach
                        </select>
                        <button type="submit" class="btn btn-primary">Filter</button>
                    </form>
                </div>

                <div class="table-responsive">
                    <table class="table card-table table-vcenter text-nowrap datatable">
                        <thead>
                            <tr>
                                <th>Employee</th>
                                <th class="text-center">Working Days</th>
                                <th class="text-center">Unpaid Absent</th>
                                <th class="text-center">Base Salary</th>
                                <th class="text-center">Advances</th>
                                <th class="text-center">Deductions</th>
                                <th class="text-center">Carry Forward</th>
                                <th class="text-center">Net Payable</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($reportData as $data)
                                <tr>
                                    <td>
                                        <div class="fw-bold">{{ $data['user']->name }}</div>
                                        <div class="small text-muted">Leave Used:
                                            {{ $data['leave_used'] }}/{{ $data['leave_allowed'] }}</div>
                                    </td>
                                    <td class="text-center text-success fw-bold">{{ $data['present'] }}</td>
                                    <td class="text-center text-danger">
                                        {{ ($data['absent'] + max(0, $data['leave'] - 4) + ($data['half_day'] * 0.5)) }}
                                    </td>
                                    <td class="text-center">₹ {{ number_format($data['base_salary'], 2) }}</td>
                                    <td class="text-center text-warning">₹ {{ number_format($data['advances'], 2) }}</td>
                                    <td class="text-center text-orange">₹ {{ number_format($data['absent_deduction'], 2) }}</td>
                                    <td class="text-center {{ $data['carry_forward'] < 0 ? 'text-danger' : 'text-muted' }}">
                                        ₹ {{ number_format($data['carry_forward'], 2) }}
                                    </td>
                                    <td class="text-center fw-bold text-primary" title="Payable in {{ $payoutMonthName }}">
                                        ₹ {{ number_format($data['remaining'], 2) }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center">No data found for this month.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </x-card>
        </div>
    </div>
</x-tabler-layout>