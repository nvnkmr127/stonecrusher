<x-tabler-layout>
    <x-slot name="header">
        <h2 class="page-title">
            {{ __('Outstanding & Advance Report') }}
        </h2>
    </x-slot>

    <div class="row row-cards">

        <!-- Outstanding Receivables (Values are Negative) -->
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title text-danger">Outstanding Receivables (Clients Owe Us)</h3>
                </div>
                <div class="card-body border-bottom py-3">
                    @if($outstandingClients->isEmpty())
                        <p class="text-muted fst-italic">No outstanding balances.</p>
                    @else
                        <div class="table-responsive">
                            <table class="table table-vcenter card-table">
                                <thead>
                                    <tr>
                                        <th>Client Name</th>
                                        <th>Contact</th>
                                        <th class="text-end">Outstanding Amount</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($outstandingClients as $client)
                                        <tr>
                                            <td class="fw-medium">{{ $client->name }}</td>
                                            <td class="text-muted">{{ $client->phone }}</td>
                                            <td class="text-end text-danger fw-bold">
                                                {{ number_format(abs($client->current_balance), 2) }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot class="bg-light">
                                    <tr>
                                        <th colspan="2" class="text-end">Total Outstanding</th>
                                        <th class="text-end text-danger fw-bold fs-4">
                                            {{ number_format(abs($outstandingClients->sum('current_balance')), 2) }}
                                        </th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Advance Balances (Values are Positive) -->
        <div class="col-12 mt-3">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title text-success">Advance Balances (Credit Available)</h3>
                </div>
                <div class="card-body border-bottom py-3">
                    @if($advanceClients->isEmpty())
                        <p class="text-muted fst-italic">No advance balances.</p>
                    @else
                        <div class="table-responsive">
                            <table class="table table-vcenter card-table">
                                <thead>
                                    <tr>
                                        <th>Client Name</th>
                                        <th>Contact</th>
                                        <th class="text-end">Credit Balance</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($advanceClients as $client)
                                        <tr>
                                            <td class="fw-medium">{{ $client->name }}</td>
                                            <td class="text-muted">{{ $client->phone }}</td>
                                            <td class="text-end text-success fw-bold">
                                                {{ number_format($client->current_balance, 2) }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot class="bg-light">
                                    <tr>
                                        <th colspan="2" class="text-end">Total Advances</th>
                                        <th class="text-end text-success fw-bold fs-4">
                                            {{ number_format($advanceClients->sum('current_balance'), 2) }}
                                        </th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>

    </div>
</x-tabler-layout>