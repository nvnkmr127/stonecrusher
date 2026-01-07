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
                </div>
            </div>
        </div>

        <!-- Ledger Table -->
        <div class="col-md-12 col-lg-8">
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

</x-tabler-layout>