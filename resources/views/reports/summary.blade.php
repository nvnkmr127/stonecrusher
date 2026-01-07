<x-tabler-layout>
    <x-slot name="header">
        <h2 class="page-title">
            {{ $title }}
        </h2>
    </x-slot>

    <div class="row row-cards">
        <div class="col-12">
            <div class="card">
                <div class="card-body border-bottom py-3">
                    
                    <form action="{{ route('reports.summary', $type) }}" method="GET" class="d-flex gap-2 align-items-end mb-3">
                        <div class="mb-3">
                            <label class="form-label">{{ __('Start Date') }}</label>
                            <input type="date" name="start_date" value="{{ $startDate }}" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">{{ __('End Date') }}</label>
                            <input type="date" name="end_date" value="{{ $endDate }}" class="form-control">
                        </div>
                        <div class="mb-3">
                            <button type="submit" class="btn btn-primary">{{ __('Filter') }}</button>
                        </div>
                        <div class="ms-auto mb-3 d-flex gap-2">
                            <a href="{{ route('reports.summary.export', ['type' => $type, 'start_date' => $startDate, 'end_date' => $endDate, 'format' => 'csv']) }}" class="btn btn-success">Export CSV</a>
                            <a href="{{ route('reports.summary.export', ['type' => $type, 'start_date' => $startDate, 'end_date' => $endDate, 'format' => 'pdf']) }}" class="btn btn-danger">Export PDF</a>
                        </div>
                    </form>

                    <div class="table-responsive">
                        <table class="table table-vcenter card-table">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th class="text-end">Count</th>
                                    
                                    @if($type === 'metal')
                                    <th class="text-end">Total Quantity</th>
                                    @endif
                                    
                                    @if($type === 'vehicle')
                                    <th class="text-end">Total KM</th>
                                    @endif
                                    
                                    <th class="text-end">Total Sales</th>
                                    
                                    @if($type === 'client')
                                    <th class="text-end">Transport Cost</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($data as $row)
                                    <tr>
                                        <td class="fw-medium">
                                            @if($type === 'metal')
                                                {{ $row->metalType->name ?? 'Unknown' }}
                                            @elseif($type === 'client')
                                                {{ $row->client->name ?? 'Unknown' }}
                                            @elseif($type === 'vehicle')
                                                {{ $row->vehicle->registration_number ?? 'Unknown' }}
                                            @endif
                                        </td>
                                        <td class="text-end text-muted">{{ $row->count }}</td>
                                        
                                        @if($type === 'metal')
                                        <td class="text-end text-muted">{{ $row->total_qty }}</td>
                                        @endif
                                        
                                        @if($type === 'vehicle')
                                        <td class="text-end text-muted">{{ $row->total_km }}</td>
                                        @endif
                                        
                                        <td class="text-end fw-bold text-primary">{{ number_format($row->total_sales, 2) }}</td>
                                        
                                        @if($type === 'client')
                                        <td class="text-end text-muted">{{ number_format($row->transport, 2) }}</td>
                                        @endif
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center text-muted">No data found for this period.</td>
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
