<x-tabler-layout>
    <x-slot name="header">
        <div class="row g-2 align-items-center">
            <div class="col">
                <div class="page-pretitle">Report</div>
                <h2 class="page-title">Client Outstanding Report</h2>
            </div>
            <div class="col-auto ms-auto">
                <a href="{{ route('clients.reports.outstanding.export') }}" class="btn btn-success">
                    <!-- SVG Download Icon -->
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24"
                        stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round"
                        stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                        <path d="M4 17v2a2 2 0 0 0 2 2h12a2 2 0 0 0 2 -2v-2" />
                        <path d="M7 11l5 5l5 -5" />
                        <path d="M12 4l0 12" />
                    </svg>
                    Export CSV
                </a>
                <a href="{{ route('clients.reports.outstanding.export-pdf') }}" class="btn btn-danger ms-2">
                    <!-- SVG Download Icon -->
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-file-type-pdf"
                        width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                        stroke-linecap="round" stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                        <path d="M14 3v4a1 1 0 0 0 1 1h4" />
                        <path d="M17 21h-10a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2z" />
                        <path d="M5 12v-7a2 2 0 0 1 2 -2h7l5 5v4" />
                        <path d="M5 18h1.5a1.5 1.5 0 0 0 0 -3h-1.5v6" />
                    </svg>
                    Export PDF
                </a>
            </div>
        </div>
    </x-slot>

    <!-- Summary Cards -->
    <div class="row row-cards mb-4">
        <div class="col-sm-6 col-lg-4">
            <div class="card card-sm">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-auto">
                            <span class="bg-primary text-white avatar">₹</span>
                        </div>
                        <div class="col">
                            <div class="font-weight-medium">Total Sales</div>
                            <div class="text-muted">{{ number_format($totalSales, 2) }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-4">
            <div class="card card-sm">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-auto">
                            <span class="bg-green text-white avatar">₹</span>
                        </div>
                        <div class="col">
                            <div class="font-weight-medium">Total Advances</div>
                            <div class="text-muted">{{ number_format($totalAdvances, 2) }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-4">
            <div class="card card-sm">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-auto">
                            <span class="bg-red text-white avatar">₹</span>
                        </div>
                        <div class="col">
                            <div class="font-weight-medium">Total Outstanding</div>
                            <div class="text-muted">{{ number_format($totalOutstanding, 2) }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Report Table -->
    <div class="card">
        <div class="table-responsive">
            <table class="table card-table table-vcenter text-nowrap datatable">
                <thead>
                    <tr>
                        <th>Client Name</th>
                        <th>Contact</th>
                        <th class="text-end">Total Sales</th>
                        <th class="text-end">Total Advance</th>
                        <th class="text-end">Net Balance</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($clients as $client)
                        @php
                            $bal = $client->balance;
                        @endphp
                        <tr>
                            <td><a href="{{ route('clients.show', $client) }}">{{ $client->name }}</a></td>
                            <td>{{ $client->phone ?? '-' }}</td>
                            <td class="text-end">
                                {{ number_format($client->total_debit ?? $client->transactions->where('transaction_type', 'debit')->sum('amount'), 2) }}
                            </td>
                            <td class="text-end">
                                {{ number_format($client->total_credit ?? $client->transactions->where('transaction_type', 'credit')->sum('amount'), 2) }}
                            </td>
                            <td class="text-end font-weight-bold {{ $bal >= 0 ? 'text-green' : 'text-red' }}">
                                {{ number_format(abs($bal), 2) }}
                            </td>
                            <td>
                                @if($bal >= 0)
                                    <span class="badge bg-green-lt">Advance</span>
                                @else
                                    <span class="badge bg-red-lt">Outstanding</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('clients.show', $client) }}" class="btn btn-sm btn-outline-secondary">View
                                    Ledger</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

</x-tabler-layout>