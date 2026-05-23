<x-tabler-layout title="Attendance Matrix">
    <style>
        .calendar-table th,
        .calendar-table td {
            width: 45px;
            height: 45px;
            padding: 0 !important;
            text-align: center;
            vertical-align: middle;
            border: 1px solid #e6e7e9;
        }

        .emp-name-col {
            width: 180px;
            min-width: 180px;
            position: sticky;
            left: 0;
            background: #fff;
            z-index: 10;
            padding: 8px 12px !important;
            text-align: left !important;
            font-weight: 600;
        }

        .status-btn {
            width: 100%;
            height: 100%;
            border: none;
            cursor: pointer;
            font-weight: bold;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s;
            font-size: 0.8rem;
        }

        .status-btn:hover {
            filter: brightness(0.9);
        }

        .btn-p {
            background-color: #d1e7dd;
            color: #0f5132;
        }

        .btn-a {
            background-color: #f8d7da;
            color: #842029;
        }

        .btn-l {
            background-color: #fff3cd;
            color: #664d03;
        }

        .btn-h {
            background-color: #ffe5d0;
            color: #9a4e0f;
        }

        .btn-v {
            background-color: #cfe2ff;
            color: #084298;
        }

        .btn-empty {
            background-color: #ffffff;
            color: #dee2e6;
        }

        .saving {
            position: relative;
        }

        .saving::after {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(255, 255, 255, 0.5);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .sticky-header th {
            position: sticky;
            top: 0;
            background: #f1f3f5;
            z-index: 11;
            padding: 8px 2px !important;
        }

        .table-responsive-matrix {
            max-height: 75vh;
            overflow: auto;
        }
    </style>

    <div class="page-header d-print-none">
        <div class="container-xl">
            <div class="row g-2 align-items-center">
                <div class="col">
                    <h2 class="page-title">Interactive Attendance Matrix</h2>
                    <div class="text-muted mt-1">Click cells to rotate status. Saves automatically via AJAX.</div>
                </div>
                <div class="col-auto ms-auto">
                    <form action="{{ route('attendance.bulk') }}" method="GET" class="d-flex gap-2">
                        <select name="month" class="form-select w-auto">
                            @foreach(range(1, 12) as $m)
                                <option value="{{ $m }}" {{ $month == $m ? 'selected' : '' }}>
                                    {{ date('F', mktime(0, 0, 0, $m, 1)) }}
                                </option>
                            @endforeach
                        </select>
                        <select name="year" class="form-select w-auto">
                            @foreach(range(now()->year - 1, now()->year + 1) as $y)
                                <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                            @endforeach
                        </select>
                        <button type="submit" class="btn btn-primary">Go</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="page-body">
        <div class="container-xl">
            <div class="card">
                <div class="table-responsive table-responsive-matrix">
                    <table class="table card-table table-vcenter calendar-table">
                        <thead class="sticky-header">
                            <tr>
                                <th class="emp-name-col">Employee</th>
                                @for($i = 1; $i <= $daysInMonth; $i++)
                                    @php
                                        $d = \Carbon\Carbon::create($year, $month, $i);
                                        $isWeekend = $d->isWeekend();
                                    @endphp
                                    <th class="{{ $isWeekend ? 'bg-light text-muted' : '' }}">
                                        {{ $i }}<br><small>{{ substr($d->format('D'), 0, 1) }}</small>
                                    </th>
                                @endfor
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($employees as $employee)
                                <tr>
                                    <td class="emp-name-col">{{ $employee->name }}</td>
                                    @for($i = 1; $i <= $daysInMonth; $i++)
                                        @php
                                            $dateStr = \Carbon\Carbon::create($year, $month, $i)->format('Y-m-d');
                                            $att = $employee->attendances->first(fn($a) => \Carbon\Carbon::parse($a->date)->format('Y-m-d') === $dateStr);
                                            $status = $att ? ($att->status instanceof \BackedEnum ? $att->status->value : $att->status) : 'absent';

                                            $btnClass = 'btn-empty';
                                            $label = '-';
                                            if ($att) {
                                                switch ($status) {
                                                    case 'present':
                                                        $btnClass = 'btn-p';
                                                        $label = 'P';
                                                        break;
                                                    case 'absent':
                                                        $btnClass = 'btn-a';
                                                        $label = 'A';
                                                        break;
                                                    case 'late':
                                                        $btnClass = 'btn-l';
                                                        $label = 'L';
                                                        break;
                                                    case 'half_day':
                                                        $btnClass = 'btn-h';
                                                        $label = 'H';
                                                        break;
                                                    case 'leave':
                                                        $btnClass = 'btn-v';
                                                        $label = 'V';
                                                        break;
                                                }
                                            }
                                        @endphp
                                        <td class="p-0">
                                            <button class="status-btn {{ $btnClass }}" data-employee="{{ $employee->id }}"
                                                data-date="{{ $dateStr }}"
                                                data-status="{{ $status == 'absent' && !$att ? '' : $status }}"
                                                onclick="toggleStatus(this)">
                                                {{ $label }}
                                            </button>
                                        </td>
                                    @endfor
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="mt-3 d-flex gap-3 justify-content-center">
                <span class="badge btn-p">P</span> Present
                <span class="badge btn-a">A</span> Absent
                <span class="badge btn-l">L</span> Late
                <span class="badge btn-h">H</span> Half Day
                <span class="badge btn-v">V</span> Leave
            </div>

            <!-- Employee Attendance Summary Table -->
            <div class="card my-4 shadow-sm border-0">
                <div class="card-header border-bottom bg-white py-3">
                    <h3 class="card-title fw-bold m-0">Monthly Staff Summary</h3>
                </div>
                <div class="table-responsive">
                    <table class="table table-vcenter table-hover mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="text-uppercase text-muted font-weight-bold">Employee</th>
                                <th class="text-center text-uppercase text-muted font-weight-bold">Present (P)</th>
                                <th class="text-center text-uppercase text-muted font-weight-bold">Late (L)</th>
                                <th class="text-center text-uppercase text-muted font-weight-bold">Half Day (H)</th>
                                <th class="text-center text-uppercase text-muted font-weight-bold">Absent (A)</th>
                                <th class="text-center text-uppercase text-muted font-weight-bold">Leave (V)</th>
                                <th class="text-center text-uppercase text-dark font-weight-bold bg-primary-lt">Total
                                    Days (P + H/2)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($employees as $emp)
                                @php
                                    $empStats = [
                                        'present' => 0,
                                        'late' => 0,
                                        'half_day' => 0,
                                        'absent' => 0,
                                        'leave' => 0,
                                    ];
                                    foreach ($emp->attendances as $att) {
                                        $s = $att->status instanceof \BackedEnum ? $att->status->value : $att->status;
                                        if (isset($empStats[$s])) {
                                            $empStats[$s]++;
                                        }
                                    }
                                @endphp
                                <tr>
                                    <td class="fw-bold">{{ $emp->name }}</td>
                                    <td class="text-center">
                                        @if($empStats['present'] > 0)
                                            <span class="badge bg-green-lt">{{ $empStats['present'] }}</span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if($empStats['late'] > 0)
                                            <span class="badge bg-yellow-lt">{{ $empStats['late'] }}</span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if($empStats['half_day'] > 0)
                                            <span class="badge bg-orange-lt">{{ $empStats['half_day'] }}</span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if($empStats['absent'] > 0)
                                            <span class="badge bg-red-lt">{{ $empStats['absent'] }}</span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if($empStats['leave'] > 0)
                                            <span class="badge bg-blue-lt">{{ $empStats['leave'] }}</span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td class="text-center bg-primary-lt fw-bold">
                                        @php
                                            $totalDays = $empStats['present'] + ($empStats['half_day'] * 0.5);
                                        @endphp
                                        <span class="total-days-{{$emp->id}}">{{ $totalDays > 0 ? $totalDays : '-' }}</span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script>
        const statuses = ['', 'present', 'late', 'half_day', 'absent', 'leave'];
        const labels = { '': '-', 'present': 'P', 'late': 'L', 'half_day': 'H', 'absent': 'A', 'leave': 'V' };
        const classes = { '': 'btn-empty', 'present': 'btn-p', 'late': 'btn-l', 'half_day': 'btn-h', 'absent': 'btn-a', 'leave': 'btn-v' };

        function toggleStatus(btn) {
            let current = btn.getAttribute('data-status');
            let nextIndex = (statuses.indexOf(current) + 1) % statuses.length;
            let nextStatus = statuses[nextIndex];

            if (nextStatus === '') {
                // If it's empty, we might actually want to delete or skip, but for now let's just rotate
                // If the user wants to clear, they can.
            }

            // UI feedback
            btn.classList.add('saving');
            updateBtnUI(btn, nextStatus);

            // AJAX Save
            fetch("{{ route('attendance.ajax.store') }}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": "{{ csrf_token() }}"
                },
                body: JSON.stringify({
                    employee_id: btn.getAttribute('data-employee'),
                    date: btn.getAttribute('data-date'),
                    status: nextStatus || 'absent' // Default to absent if empty during rotate
                })
            })
                .then(res => res.json())
                .then(data => {
                    btn.classList.remove('saving');
                    if (!data.success) {
                        alert('Error saving attendance');
                        updateBtnUI(btn, current); // Revert
                    } else {
                        btn.setAttribute('data-status', data.status);
                        updateBtnUI(btn, data.status);
                    }
                })
                .catch(err => {
                    btn.classList.remove('saving');
                    alert('Connection error');
                    updateBtnUI(btn, current); // Revert
                });
        }

        function updateBtnUI(btn, status) {
            // Remove all possible status classes
            Object.values(classes).forEach(c => btn.classList.remove(c));
            btn.classList.add(classes[status] || 'btn-empty');
            btn.innerText = labels[status] || '-';
        }
    </script>
</x-tabler-layout>