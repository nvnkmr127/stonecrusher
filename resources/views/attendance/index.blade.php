<x-tabler-layout>
    <x-slot name="header">
        <div class="row g-2 align-items-center">
            <div class="col">
                <h2 class="page-title">
                    {{ __('Attendance Log') }}
                </h2>
            </div>
            <div class="col-auto ms-auto d-print-none">
                <div class="btn-list">
                    <a href="{{ route('attendance.report.daily') }}"
                        class="btn btn-secondary d-none d-sm-inline-block me-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24"
                            stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round"
                            stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                            <path d="M14 3v4a1 1 0 0 0 1 1h4" />
                            <path d="M17 21h-10a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2z" />
                            <path d="M9 9l1 0" />
                            <path d="M9 13l6 0" />
                            <path d="M9 17l6 0" />
                        </svg>
                        {{ __('Daily Report') }}
                    </a>
                    <a href="{{ route('attendance.create') }}" class="btn btn-primary d-none d-sm-inline-block">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24"
                            stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round"
                            stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                            <path d="M12 5l0 14" />
                            <path d="M5 12l14 0" />
                        </svg>
                        {{ __('Add New') }}
                    </a>
                </div>
            </div>
        </div>
    </x-slot>

    <div class="row row-cards">
        <div class="col-12">
            <x-card>
                <div class="card-header">
                    <h3 class="card-title">Filter Attendance</h3>
                </div>
                <div class="card-body border-bottom py-3">
                    <form method="GET" action="{{ route('attendance.index') }}" class="d-flex gap-2">
                        <div>
                            <input type="date" name="date" value="{{ request('date', date('Y-m-d')) }}"
                                class="form-control" />
                        </div>
                        <div>
                            <select name="user_id" class="form-select">
                                <option value="">All Users</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                                        {{ $user->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <button type="submit" class="btn btn-secondary">Filter</button>
                    </form>
                </div>

                <div class="table-responsive">
                    <table class="table card-table table-vcenter text-nowrap datatable">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>User</th>
                                <th>Check In</th>
                                <th>Check Out</th>
                                <th>Status</th>
                                <th>Total Time</th>
                                <th>Remarks</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($employees as $employee)
                                @php
                                    $attendance = $employee->attendances->first();
                                @endphp
                                <tr>
                                    <td>{{ $date }}</td>
                                    <td>{{ $employee->name }}</td>
                                    <td>{{ $attendance && $attendance->check_in ? \Carbon\Carbon::parse($attendance->check_in)->format('H:i') : '-' }}
                                    </td>
                                    <td>{{ $attendance && $attendance->check_out ? \Carbon\Carbon::parse($attendance->check_out)->format('H:i') : '-' }}
                                    </td>
                                    <td>
                                        @if($attendance)
                                            <span
                                                class="badge bg-{{ match ($attendance->status) { 'present' => 'green', 'late' => 'yellow', 'absent' => 'red', 'leave' => 'blue', 'half_day' => 'orange', default => 'secondary'} }}-lt">
                                                {{ ucfirst(str_replace('_', ' ', $attendance->status)) }}
                                            </span>
                                        @else
                                            <span class="badge bg-red-lt">Absent</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($attendance && $attendance->check_in && $attendance->check_out)
                                            {{ \Carbon\Carbon::parse($attendance->check_in)->diff(\Carbon\Carbon::parse($attendance->check_out))->format('%H:%I') }}
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td class="text-truncate" style="max-width: 150px;">
                                        {{ $attendance ? $attendance->remarks : '-' }}
                                    </td>
                                    <td>
                                        @if($attendance)
                                            <a href="{{ route('attendance.edit', $attendance) }}"
                                                class="btn btn-sm btn-outline-primary">Edit</a>
                                            <form action="{{ route('attendance.destroy', $attendance) }}" method="POST"
                                                class="d-inline-block" onsubmit="return confirm('Are you sure?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                                            </form>
                                        @else
                                            <a href="{{ route('attendance.create', ['user_id' => $employee->id, 'date' => $date]) }}"
                                                class="btn btn-sm btn-primary">Mark</a>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center">No employees found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </x-card>
        </div>
    </div>
</x-tabler-layout>