<x-tabler-layout title="Attendance Hub">
    <style>
        .calendar-container {
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
            overflow: hidden;
            border: 1px solid #e6e7e9;
        }

        .calendar-grid {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            min-height: 800px;
            /* Big vertical presence */
        }

        .calendar-header-day {
            text-align: center;
            font-weight: 700;
            padding: 20px;
            background: #f8f9fa;
            border-bottom: 2px solid #eee;
            color: #495057;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-size: 0.85rem;
        }

        .calendar-day {
            min-height: 150px;
            /* Large boxes */
            padding: 15px;
            border-right: 1px solid #eee;
            border-bottom: 1px solid #eee;
            background: #fff;
            transition: all 0.3s ease;
            cursor: pointer;
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .calendar-grid>div:nth-child(7n) {
            border-right: none;
        }

        .calendar-day:hover {
            background-color: #fcfdfe;
            box-shadow: inset 0 0 15px rgba(32, 107, 196, 0.05);
            z-index: 1;
        }

        .day-number {
            font-weight: 800;
            font-size: 1.4rem;
            color: #1d273b;
            line-height: 1;
        }

        .day-label {
            font-size: 0.7rem;
            color: #adb5bd;
            font-weight: 600;
        }

        .day-weekend {
            background-color: #fafbfc;
        }

        .day-today {
            background-color: #f0f7ff;
        }

        .day-today .day-number {
            color: #206bc4;
        }

        .empty-day {
            background: #fcfcfc;
            cursor: default;
        }

        .staff-summary {
            display: flex;
            flex-direction: column;
            gap: 6px;
            margin-top: auto;
        }

        .summary-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 6px 10px;
            border-radius: 6px;
            font-size: 0.75rem;
            font-weight: 600;
            transition: transform 0.2s;
        }

        .summary-item:hover {
            transform: translateX(2px);
        }

        .summary-p {
            background: #e6fcf5;
            color: #099268;
            border-left: 3px solid #099268;
        }

        .summary-l {
            background: #fff9db;
            color: #f08c00;
            border-left: 3px solid #f08c00;
        }

        .summary-a {
            background: #fff5f5;
            color: #fa5252;
            border-left: 3px solid #fa5252;
        }

        .summary-v {
            background: #e7f5ff;
            color: #228be6;
            border-left: 3px solid #228be6;
        }

        .count-bubble {
            background: rgba(0, 0, 0, 0.05);
            padding: 1px 6px;
            border-radius: 10px;
            font-size: 0.7rem;
        }
    </style>

    <div class="page-header d-print-none mb-4">
        <div class="container-xl">
            <div class="row g-3 align-items-center">
                <div class="col">
                    <h2 class="page-title fw-bold text-primary">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-calendar-stats me-2"
                            width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
                            fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                            <path d="M11.795 21h-6.795a2 2 0 0 1 -2 -2v-12a2 2 0 0 1 2 -2h12a2 2 0 0 1 2 2v4" />
                            <path d="M18 14v4h4" />
                            <path d="M18 18m-4 0a4 4 0 1 0 8 0a4 4 0 1 0 -8 0" />
                            <path d="M15 3v4" />
                            <path d="M7 3v4" />
                            <path d="M3 11h16" />
                        </svg>
                        Staff Attendance Hub
                    </h2>
                    <div class="text-muted">Comprehensive monthly overview and planning.</div>
                </div>
                <div class="col-auto ms-auto d-flex gap-3">
                    <form action="{{ route('attendance.calendar') }}" method="GET"
                        class="d-flex gap-2 p-1 bg-white rounded-2 shadow-sm border">
                        <select name="month" class="form-select border-0 bg-transparent fw-bold">
                            @foreach(range(1, 12) as $m)
                                <option value="{{ $m }}" {{ $month == $m ? 'selected' : '' }}>
                                    {{ date('F', mktime(0, 0, 0, $m, 1)) }}
                                </option>
                            @endforeach
                        </select>
                        <select name="year" class="form-select border-0 bg-transparent fw-bold">
                            @foreach(range(now()->year - 2, now()->year + 1) as $y)
                                <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                            @endforeach
                        </select>
                        <button type="submit" class="btn btn-primary px-4">Jump</button>
                    </form>
                    <div class="btn-list">
                        <a href="{{ route('attendance.bulk') }}" class="btn btn-warning shadow-sm">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-edit" width="24"
                                height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path d="M9 7h-3a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-3" />
                                <path d="M9 15h3l8.5 -8.5a1.5 1.5 0 0 0 -3 -3l-8.5 8.5v3" />
                                <path d="M16 5l3 3" />
                            </svg>
                            Mark Monthly Attendance
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="page-body">
        <div class="container-xl">
            <div class="calendar-container shadow-lg">
                <div class="calendar-grid">
                    @foreach(['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'] as $dayName)
                        <div class="calendar-header-day">{{ $dayName }}</div>
                    @endforeach

                    @php
                        $firstDayOfMonth = \Carbon\Carbon::create($year, $month, 1);
                        $daysInMonth = $firstDayOfMonth->daysInMonth;
                        $startOfWeek = $firstDayOfMonth->dayOfWeek; // 0-Sun to 6-Sat
                        $today = now()->format('Y-m-d');
                    @endphp

                    @for($i = 0; $i < $startOfWeek; $i++)
                        <div class="calendar-day empty-day"></div>
                    @endfor

                    @for($day = 1; $day <= $daysInMonth; $day++)
                        @php
                            $date = \Carbon\Carbon::create($year, $month, $day);
                            $dateStr = $date->format('Y-m-d');
                            $isWeekend = $date->isWeekend();

                            $dayAttendances = collect();
                            foreach ($employees as $emp) {
                                $att = $emp->attendances->first(fn($a) => \Carbon\Carbon::parse($a->date)->format('Y-m-d') === $dateStr);
                                if ($att) {
                                    $statusVal = $att->status instanceof \BackedEnum ? $att->status->value : $att->status;
                                    $dayAttendances->push(['status' => $statusVal]);
                                }
                            }

                            $counts = [
                                'present' => $dayAttendances->where('status', 'present')->count(),
                                'late' => $dayAttendances->where('status', 'late')->count(),
                                'half_day' => $dayAttendances->where('status', 'half_day')->count(),
                                'absent' => $dayAttendances->where('status', 'absent')->count(),
                                'leave' => $dayAttendances->where('status', 'leave')->count(),
                            ];
                        @endphp
                        <div class="calendar-day {{ $isWeekend ? 'day-weekend' : '' }} {{ $dateStr == $today ? 'day-today' : '' }}"
                            onclick="window.location.href='{{ route('attendance.index', ['date' => $dateStr]) }}'">
                            <div class="d-flex justify-content-between align-items-start">
                                <span class="day-number">{{ $day }}</span>
                                @if($dateStr == $today)
                                    <span class="badge bg-primary-lt">TODAY</span>
                                @endif
                            </div>

                            <div class="staff-summary">
                                @if($counts['present'] > 0)
                                    <div class="summary-item summary-p">
                                        <span>Present</span>
                                        <span class="count-bubble">{{ $counts['present'] }}</span>
                                    </div>
                                @endif
                                @if($counts['late'] > 0)
                                    <div class="summary-item summary-l">
                                        <span>Late</span>
                                        <span class="count-bubble">{{ $counts['late'] }}</span>
                                    </div>
                                @endif
                                @if($counts['absent'] > 0)
                                    <div class="summary-item summary-a">
                                        <span>Absent</span>
                                        <span class="count-bubble">{{ $counts['absent'] }}</span>
                                    </div>
                                @endif
                                @if($counts['leave'] > 0)
                                    <div class="summary-item summary-v">
                                        <span>Leave</span>
                                        <span class="count-bubble">{{ $counts['leave'] }}</span>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endfor

                    {{-- Fill remaining grid cells to maintain border symmetry --}}
                    @php $remaining = (7 - (($startOfWeek + $daysInMonth) % 7)) % 7; @endphp
                    @for($i = 0; $i < $remaining; $i++)
                        <div class="calendar-day empty-day"></div>
                    @endfor
                </div>
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
                                        {{ $totalDays > 0 ? $totalDays : '-' }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="mt-4 p-4 card border-0 shadow-sm bg-white">
                <div class="row align-items-center">
                    <div class="col">
                        <h4 class="m-0 fw-bold">Understanding Statuses</h4>
                        <div class="text-muted small">Standard operating procedures for attendance.</div>
                    </div>
                    <div class="col-auto">
                        <div class="d-flex gap-4">
                            <div class="d-flex align-items-center gap-2">
                                <span class="badge badge-outline text-success"
                                    style="width:12px; height:12px; border-radius:50%; background:#099268;"></span>
                                <span class="small fw-bold">On Duty</span>
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <span class="badge badge-outline text-warning"
                                    style="width:12px; height:12px; border-radius:50%; background:#f08c00;"></span>
                                <span class="small fw-bold">Grace Applied</span>
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <span class="badge badge-outline text-danger"
                                    style="width:12px; height:12px; border-radius:50%; background:#fa5252;"></span>
                                <span class="small fw-bold">Unexcused</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-tabler-layout>