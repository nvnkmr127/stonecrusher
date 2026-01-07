<x-tabler-layout>
    <x-slot name="header">
        <h2 class="page-title">
            {{ __('Custom Date Range Report') }}
        </h2>
    </x-slot>

    <div class="row row-cards">
        <div class="col-12">
            <div class="card">
                <div class="card-body border-bottom py-3">

                    <form action="{{ route('reports.custom') }}" method="GET" class="d-flex gap-2 align-items-end mb-3">
                        <div class="mb-3">
                            <label class="form-label">{{ __('Start Date') }}</label>
                            <input type="date" name="start_date" value="{{ $startDate }}" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">{{ __('End Date') }}</label>
                            <input type="date" name="end_date" value="{{ $endDate }}" class="form-control">
                        </div>
                        <div class="mb-3">
                            <button type="submit" class="btn btn-primary">{{ __('Fetch Report') }}</button>
                        </div>
                        <div class="ms-auto mb-3 d-flex gap-2">
                            <a href="{{ route('reports.custom.export', ['start_date' => $startDate, 'end_date' => $endDate, 'format' => 'csv']) }}"
                                class="btn btn-success">Export CSV</a>
                            <a href="{{ route('reports.custom.export', ['start_date' => $startDate, 'end_date' => $endDate, 'format' => 'pdf']) }}"
                                class="btn btn-danger">Export PDF</a>
                        </div>
                    </form>

                    <div class="row mb-3 p-3 bg-light rounded border">
                        <div class="col-md-6">
                            <span class="d-block text-secondary small">Total Count</span>
                            <span class="h3">{{ $totalCount }}</span>
                        </div>
                        <div class="col-md-6 text-md-end">
                            <span class="d-block text-secondary small">Total Sales Amount</span>
                            <span class="h3 text-primary">{{ number_format($totalSales, 2) }}</span>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-vcenter card-table">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>GP#</th>
                                    <th>Client</th>
                                    <th>Material</th>
                                    <th class="text-end">Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($sales as $sale)
                                    <tr>
                                        <td>{{ $sale->date->format('d M Y') }}</td>
                                        <td>{{ $sale->gate_pass_number }}</td>
                                        <td>{{ $sale->client->name }}</td>
                                        <td>{{ $sale->metalType->name }}</td>
                                        <td class="text-end fw-medium">
                                            {{ number_format($sale->total_amount, 2) }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center text-muted">No sales found in this range.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-tabler-layout>