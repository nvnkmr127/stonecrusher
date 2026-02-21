<x-tabler-layout>
    <x-slot name="header">
        <x-page-header title="Diesel Stock (Daily Tank)" subtitle="Manage tank inventory and refills" :breadcrumbs="[
        ['label' => 'Dashboard', 'route' => 'dashboard'],
        ['label' => 'Diesel Stock', 'active' => true],
    ]">
            <x-slot name="actions">
                <a href="{{ route('diesel.index') }}" class="btn btn-outline-primary me-2">
                    Refill Register
                </a>
                <a href="{{ route('diesel-stocks.create') }}" class="btn btn-primary">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24"
                        stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round"
                        stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                        <path d="M12 5l0 14" />
                        <path d="M5 12l14 0" />
                    </svg>
                    New Stock Record
                </a>
            </x-slot>
        </x-page-header>
    </x-slot>

    <div class="row row-cards">
        <div class="col-12">
            <x-card>
                <div class="card-body border-bottom py-3">
                    <form method="GET" action="{{ route('diesel-stocks.index') }}" class="row g-2">
                        <div class="col-md-2">
                            <input type="date" name="start_date" value="{{ $startDate }}"
                                class="form-control form-control-sm">
                        </div>
                        <div class="col-md-2">
                            <input type="date" name="end_date" value="{{ $endDate }}"
                                class="form-control form-control-sm">
                        </div>
                        <div class="col-md-auto">
                            <button type="submit" class="btn btn-sm btn-primary">Filter</button>
                        </div>
                    </form>
                </div>
                <div class="table-responsive">
                    <table class="table table-vcenter card-table table-hover">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th class="text-end">Opening Stock</th>
                                <th class="text-end">Purchased (Refilled)</th>
                                <th class="text-end bg-light-subtle">Total Available</th>
                                <th class="text-end text-primary">Consumed (Refilled to Vehicles)</th>
                                <th class="text-end bg-primary-lt">Closing Balance</th>
                                <th>Remarks</th>
                                <th class="w-1"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($stocks as $stock)
                                <tr>
                                    <td class="fw-bold">{{ $stock->date->format('d M tags, Y') }}</td>
                                    <td class="text-end">{{ number_format($stock->opening_liters, 2) }} L</td>
                                    <td class="text-end text-success">+ {{ number_format($stock->purchased_liters, 2) }} L
                                    </td>
                                    <td class="text-end bg-light-subtle fw-bold">
                                        {{ number_format($stock->total_available, 2) }} L</td>
                                    <td class="text-end text-danger">- {{ number_format($stock->consumed_liters, 2) }} L
                                    </td>
                                    <td class="text-end bg-primary-lt fw-bold">
                                        {{ number_format($stock->closing_liters, 2) }} L</td>
                                    <td class="text-muted small">{{ $stock->remarks }}</td>
                                    <td>
                                        <div class="btn-list flex-nowrap">
                                            <a href="{{ route('diesel-stocks.edit', $stock) }}"
                                                class="btn btn-sm btn-white">Edit</a>
                                            <form action="{{ route('diesel-stocks.destroy', $stock) }}" method="POST"
                                                onsubmit="return confirm('Delete this record?')">
                                                @csrf @method('DELETE')
                                                <button type="submit"
                                                    class="btn btn-sm btn-link text-danger">Delete</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center py-4 text-muted fst-italic">No stock records found
                                        for this range.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="card-footer d-flex align-items-center">
                    {{ $stocks->links() }}
                </div>
            </x-card>
        </div>
    </div>
</x-tabler-layout>