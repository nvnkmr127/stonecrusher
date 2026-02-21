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
                    <a href="{{ route('attendance.calendar') }}" class="btn btn-info d-none d-sm-inline-block me-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-calendar" width="24"
                            height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                            stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                            <path d="M4 7a2 2 0 0 1 2 -2h12a2 2 0 0 1 2 2v12a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2v-12z" />
                            <path d="M16 3v4" />
                            <path d="M8 3v4" />
                            <path d="M4 11h16" />
                            <path d="M11 15h1" />
                            <path d="M12 15v3" />
                        </svg>
                        {{ __('Calendar View') }}
                    </a>
                    <a href="{{ route('attendance.bulk') }}" class="btn btn-warning d-none d-sm-inline-block me-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-checkbox" width="24"
                            height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                            stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                            <path d="M9 11l3 3l8 -8" />
                            <path d="M20 12v6a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2v-12a2 2 0 0 1 2 -2h9" />
                        </svg>
                        {{ __('Bulk Entry') }}
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
                <x-slot name="header">Filter Attendance</x-slot>
                <div class="border-bottom py-3">
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

                <x-table>
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>User</th>
                            <th>Status</th>
                            <th>Remarks</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($employees as $employee)
                            @php
                                $attendance = $employee->attendances->first();
                                $isLocked = \App\Models\PayrollPeriod::isLocked(\Carbon\Carbon::parse($date)->month, \Carbon\Carbon::parse($date)->year);
                            @endphp
                            <tr>
                                <td>{{ $date }}</td>
                                <td>{{ $employee->name }}</td>
                                <td>
                                    @if($attendance)
                                        <x-status-badge :status="$attendance->status" />
                                    @else
                                        <x-status-badge status="absent" />
                                    @endif
                                </td>
                                <td class="text-truncate" style="max-width: 250px;">
                                    {{ $attendance ? $attendance->remarks : '-' }}
                                </td>
                                <td>
                                    @if(!$isLocked)
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
                                        <a href="{{ route('salary-advances.create', ['user_id' => $employee->id]) }}"
                                            class="btn btn-sm btn-outline-warning ms-1" title="Salary Advance">₹</a>
                                    @else
                                        <span class="badge bg-secondary">Locked</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center">No employees found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </x-table>
            </x-card>
        </div>
    </div>
</x-tabler-layout>