<x-tabler-layout>
    <x-slot name="header">
        <div class="row g-2 align-items-center">
            <div class="col">
                <div class="page-pretitle">
                    Detail
                </div>
                <h2 class="page-title">
                    {{ $client->name }}
                </h2>
            </div>
            <div class="col-auto ms-auto d-print-none">
                <div class="btn-list">
                    <a href="{{ route('clients.transactions.create', $client) }}"
                        class="btn btn-primary d-none d-sm-inline-block">
                        <!-- Download SVG icon from http://tabler-icons.io/i/plus -->
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24"
                            stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round"
                            stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                            <path d="M12 5l0 14" />
                            <path d="M5 12l14 0" />
                        </svg>
                        Record Transaction
                    </a>
                </div>
            </div>
        </div>
    </x-slot>

    <div class="row row-cards">
        <!-- Client Details & Balance -->
        <div class="col-md-6 col-lg-4">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <span class="avatar me-3 rounded">{{ substr($client->name, 0, 2) }}</span>
                        <div>
                            <p class="mb-0 font-weight-bold">{{ $client->name }}</p>
                            <small class="text-muted">{{ $client->email ?? 'No Email' }}</small>
                        </div>
                    </div>
                    <div>
                        <p class="mb-1"><strong>Phone:</strong> {{ $client->phone ?? '-' }}</p>
                        <p class="mb-1"><strong>Address:</strong> {{ $client->address ?? '-' }}</p>
                    </div>
                    <div class="mt-4">
                        @php
                            $balance = $client->balance;
                            $color = $balance >= 0 ? 'green' : 'red';
                            $label = $balance >= 0 ? 'Advance / Excess' : 'Outstanding / Due';
                        @endphp
                        <div class="card card-sm bg-{{ $color }}-lt">
                            <div class="card-body">
                                <div class="row align-items-center">
                                    <div class="col-auto">
                                        <span
                                            class="bg-{{ $color }} text-white avatar"><!-- Icon here if needed -->₹</span>
                                    </div>
                                    <div class="col">
                                        <div class="font-weight-medium">
                                            Net Balance
                                        </div>
                                        <div class="text-muted">
                                            {{ number_format(abs($balance), 2) }} ({{ $label }})
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-3">
                        <div class="row row-cards">
                            <div class="col-12">
                                <div class="card card-sm">
                                    <div class="card-body">
                                        <div class="row align-items-center">
                                            <div class="col-auto">
                                                <span class="bg-primary text-white avatar">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24"
                                                        height="24" viewBox="0 0 24 24" stroke-width="2"
                                                        stroke="currentColor" fill="none" stroke-linecap="round"
                                                        stroke-linejoin="round">
                                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                        <path d="M7 17m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0" />
                                                        <path d="M17 17m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0" />
                                                        <path
                                                            d="M5 17h-2v-4m-1 -8h11v12m-4 0h6m4 0h2v-6h-8m0 -5h5l3 5" />
                                                        <line x1="3" y1="9" x2="7" y2="9" />
                                                    </svg>
                                                </span>
                                            </div>
                                            <div class="col">
                                                <div class="font-weight-medium">
                                                    {{ $totalTrips }} Total Trips
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="card card-sm">
                                    <div class="card-body">
                                        <div class="row align-items-center">
                                            <div class="col-auto">
                                                <span class="bg-success text-white avatar">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24"
                                                        height="24" viewBox="0 0 24 24" stroke-width="2"
                                                        stroke="currentColor" fill="none" stroke-linecap="round"
                                                        stroke-linejoin="round">
                                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                        <path d="M12 3v18" />
                                                        <path d="M16 7h-6a2 2 0 0 0 0 4h4a2 2 0 0 1 0 4h-6" />
                                                        <path d="M12 21v-2" />
                                                        <path d="M12 3v2" />
                                                    </svg>
                                                </span>
                                            </div>
                                            <div class="col">
                                                <div class="font-weight-medium">
                                                    {{ number_format($totalCft, 2) }} CFT
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Ledger Table -->
        <div class="col-md-12 col-lg-8">
            <!-- Monthly Stats Section -->
            <div class="row row-cards mb-3">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-calendar-stats me-1" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                    <path d="M11.795 21h-6.795a2 2 0 0 1 -2 -2v-12a2 2 0 0 1 2 -2h12a2 2 0 0 1 2 2v4"></path>
                                    <path d="M18 14v4h4"></path>
                                    <circle cx="18" cy="18" r="4"></circle>
                                    <path d="M15 3v4"></path>
                                    <path d="M7 3v4"></path>
                                    <path d="M3 11h16"></path>
                                </svg>
                                Stats for {{ \Carbon\Carbon::parse($selectedMonth)->format('F Y') }}
                            </h4>
                            <div class="card-actions">
                                <form method="GET" action="{{ route('clients.show', $client) }}" id="monthFilterForm">
                                    <select name="month" class="form-select form-select-sm" onchange="this.form.submit()">
                                        @foreach($monthList as $value => $label)
                                            <option value="{{ $value }}" {{ $selectedMonth == $value ? 'selected' : '' }}>{{ $label }}</option>
                                        @endforeach
                                    </select>
                                </form>
                            </div>
                        </div>
                        <div class="card-body p-2">
                            <div class="row row-cards g-2">
                                <div class="col-sm-6 col-md-3">
                                    <div class="card card-sm bg-blue-lt">
                                        <div class="card-body">
                                            <div class="row align-items-center">
                                                <div class="col-auto">
                                                    <span class="bg-blue text-white avatar avatar-sm">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M7 17m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0" /><path d="M17 17m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0" /><path d="M5 17h-2v-4m-1 -8h11v12m-4 0h6m4 0h2v-6h-8m0 -5h5l3 5" /></svg>
                                                    </span>
                                                </div>
                                                <div class="col">
                                                    <div class="font-weight-medium small text-muted">Month Trips</div>
                                                    <div class="h3 mb-0">{{ $monthlyStats['trips'] }}</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-6 col-md-3">
                                    <div class="card card-sm bg-green-lt">
                                        <div class="card-body">
                                            <div class="row align-items-center">
                                                <div class="col-auto">
                                                    <span class="bg-green text-white avatar avatar-sm">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 3l8 4.5l0 9l-8 4.5l-8 -4.5l0 -9l8 -4.5" /><path d="M12 12l8 -4.5" /><path d="M12 12l0 9" /><path d="M12 12l-8 -4.5" /></svg>
                                                    </span>
                                                </div>
                                                <div class="col">
                                                    <div class="font-weight-medium small text-muted">Month CFT</div>
                                                    <div class="h3 mb-0">{{ number_format($monthlyStats['quantity'], 2) }}</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-6 col-md-3">
                                    <div class="card card-sm bg-orange-lt">
                                        <div class="card-body">
                                            <div class="row align-items-center">
                                                <div class="col-auto">
                                                    <span class="bg-orange text-white avatar avatar-sm">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon text-white" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M14 3v4a1 1 0 0 0 1 1h4" /><path d="M17 21h-10a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2z" /><path d="M9 15l2 2l4 -4" /></svg>
                                                    </span>
                                                </div>
                                                <div class="col">
                                                    <div class="font-weight-medium small text-muted">Month Bill</div>
                                                    <div class="h3 mb-0">₹ {{ number_format($monthlyStats['bill'], 2) }}</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-6 col-md-3">
                                    <div class="card card-sm bg-azure-lt">
                                        <div class="card-body">
                                            <div class="row align-items-center">
                                                <div class="col-auto">
                                                    <span class="bg-azure text-white avatar avatar-sm">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M17 8v-3a1 1 0 0 0 -1 -1h-10a2 2 0 0 0 0 4h12a1 1 0 0 1 1 1v3m0 4v3a1 1 0 0 1 -1 1h-12a2 2 0 0 1 -2 -2v-12" /><path d="M20 12v4h-4a2 2 0 0 1 0 -4h4" /></svg>
                                                    </span>
                                                </div>
                                                <div class="col">
                                                    <div class="font-weight-medium small text-muted">Month Paid</div>
                                                    <div class="h3 mb-0">₹ {{ number_format($monthlyStats['paid'], 2) }}</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Transaction Ledger</h3>
                    <div class="card-actions d-flex gap-2 align-items-center">
                        <form method="GET" action="{{ route('clients.show', $client) }}" class="d-flex gap-2">
                            <input type="date" name="start_date" class="form-control form-control-sm"
                                value="{{ request('start_date') }}" placeholder="Start Date">
                            <input type="date" name="end_date" class="form-control form-control-sm"
                                value="{{ request('end_date') }}" placeholder="End Date">
                            <button type="submit" class="btn btn-sm btn-ghost-primary">
                                <!-- SVG Filter Icon -->
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24"
                                    viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                    stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                    <path
                                        d="M5.5 5h13a1 1 0 0 1 .5 1.5l-5 5.5l0 7l-4 -3l0 -4l-5 -5.5a1 1 0 0 1 .5 -1.5" />
                                </svg>
                                Filter
                            </button>
                            @if(request('start_date'))
                                <a href="{{ route('clients.show', $client) }}" class="btn btn-sm btn-ghost-secondary"
                                    title="Reset">x</a>
                            @endif
                        </form>
                        <button onclick="window.print()" class="btn btn-sm btn-outline-secondary d-print-none">
                            <!-- SVG Printer Icon -->
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24"
                                viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                stroke-linecap="round" stroke-linejoin="round">
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
                <div class="table-responsive">
                    <table class="table card-table table-vcenter text-nowrap datatable">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Type</th>
                                <th>Mode</th>
                                <th>Ref #</th>
                                <th>Description</th>
                                <th class="text-end">Amount</th>
                                @if(auth()->user()->hasRole('admin'))
                                <th>Actions</th> @endif
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($transactions as $txn)
                                <tr>
                                    <td>{{ $txn->transaction_date->format('d M, Y') }}</td>
                                    <td>
                                        <span
                                            class="badge bg-{{ $txn->transaction_type === 'credit' ? 'green' : 'red' }}-lt uppercase">
                                            {{ $txn->transaction_type }}
                                        </span>
                                    </td>
                                    <td>{{ $txn->payment_mode ?? '-' }}</td>
                                    <td>{{ $txn->reference_number ?? '-' }}</td>
                                    <td>{{ $txn->description ?? '-' }}</td>
                                    <td
                                        class="text-end font-weight-bold {{ $txn->transaction_type === 'credit' ? 'text-green' : 'text-red' }}">
                                        {{ $txn->transaction_type === 'credit' ? '+' : '-' }}
                                        {{ number_format($txn->amount, 2) }}
                                    </td>
                                    @if(auth()->user()->hasRole('admin'))
                                        <td>
                                            <div class="d-flex gap-1">
                                                <a href="{{ route('clients.transactions.edit', [$client, $txn]) }}"
                                                    class="btn btn-sm btn-outline-primary">Edit</a>
                                                <form action="{{ route('clients.transactions.destroy', [$client, $txn]) }}"
                                                    method="POST"
                                                    onsubmit="return confirm('Are you sure? This will adjust the client balance.')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                                                </form>
                                            </div>
                                        </td>
                                    @endif
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted">No transactions recorded yet.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="card-footer">
                    {{ $transactions->links() }}
                </div>
            </div>
        </div>
    </div>

    <!-- Trips / Gate Passes Table -->
    <div class="col-12">
        <x-card>
            <x-slot name="header">
                Trips / Gate Passes
            </x-slot>

            <x-table>
                <thead>
                    <tr>
                        <th>GP Number</th>
                        <th>Date</th>
                        <th>Vehicle Number</th>
                        <th>Material Type</th>
                        <th>Weight / Quantity (CFT)</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($gatePasses as $gp)
                        <tr>
                            <td>
                                <a href="{{ route('gate-passes.show', $gp) }}"
                                    class="text-reset fw-bold">{{ $gp->gate_pass_number }}</a>
                            </td>
                            <td>{{ $gp->date->format('d M, Y h:i A') }}</td>
                            <td>{{ $gp->vehicle->registration_number ?? $gp->manual_vehicle_number ?? '-' }}</td>
                            <td>{{ $gp->metalType->name ?? '-' }}</td>
                            <td>{{ $gp->net_weight }} CFT</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5">
                                <x-empty-state title="No Trips Found"
                                    description="There are currently no gate passes linked to this client." />
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </x-table>

            @if($gatePasses->hasPages())
                <div class="mt-3">
                    {{ $gatePasses->links() }}
                </div>
            @endif
        </x-card>
    </div>
    </div>

</x-tabler-layout>