<x-tabler-layout>
    <x-slot name="header">
        <h2 class="page-title">
            {{ __('Daily Attendance Report') }}
        </h2>
    </x-slot>

    <div class="row row-cards">
        <div class="col-12">
            <x-card>
                <div class="card-header">
                    <ul class="nav nav-pills card-header-pills">
                        <li class="nav-item">
                            <a class="nav-link active" href="#">Daily Report</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('attendance.bulk') }}">Bulk Entry</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('attendance.report') }}">Monthly Summary</a>
                        </li>
                    </ul>
                    <div class="card-actions ms-auto">
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
                    </div>
                </div>
                <div class="card-body border-bottom py-3">
                    <form method="GET" action="{{ route('attendance.report.daily') }}" class="d-flex gap-2">
                        <input type="date" name="date" class="form-control w-auto" value="{{ $date }}">
                        <button type="submit" class="btn btn-primary">Filter</button>
                    </form>
                </div>

                <div class="table-responsive">
                    <table class="table card-table table-vcenter text-nowrap datatable">
                        <thead>
                            <tr>
                                <th>Employee</th>
                                <th class="text-center">Status</th>
                                <th>Remarks</th>
                                <th>Advance</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($dailyData as $data)
                                <tr>
                                    <td>{{ $data['user']->name }}</td>
                                    <td class="text-center">
                                        <span
                                            class="badge bg-{{ match (strtolower($data['status_label'])) { 'present' => 'success', 'late' => 'warning', 'half day' => 'orange', 'absent' => 'danger', 'leave' => 'primary', default => 'secondary'} }} text-white">
                                            {{ $data['status_label'] }}
                                        </span>
                                    </td>
                                    <td class="text-muted">{{ $data['remarks'] ?? '-' }}</td>
                                    <td>
                                        <a href="{{ route('salary-advances.create', ['user_id' => $data['user']->id, 'date' => $date]) }}"
                                            class="btn btn-sm btn-outline-warning">Record</a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center">No data found for this date.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </x-card>
        </div>
    </div>
</x-tabler-layout>