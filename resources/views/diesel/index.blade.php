<x-tabler-layout>
    <x-slot name="header">
        <x-page-header title="Diesel Issue Entry" subtitle="Log fuel issued to vehicles and machines" :breadcrumbs="[
        ['label' => 'Dashboard', 'route' => 'dashboard'],
        ['label' => 'Diesel Issues', 'active' => true],
    ]">
            <x-slot name="actions">
                <a href="{{ route('operational-units.index') }}" class="btn btn-outline-primary me-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24"
                        stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round"
                        stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                        <path d="M12 10l0 4" />
                        <path d="M10 12l4 0" />
                        <path d="M12 3c7.2 0 9 1.8 9 9s-1.8 9 -9 9s-9 -1.8 -9 -9s1.8 -9 9 -9z" />
                    </svg>
                    Manage Units
                </a>
                <a href="{{ route('diesel.create') }}" class="btn btn-primary">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24"
                        stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round"
                        stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                        <path d="M12 5l0 14" />
                        <path d="M5 12l14 0" />
                    </svg>
                    New Entry
                </a>
            </x-slot>
        </x-page-header>
    </x-slot>

    <div class="row row-cards">
        <!-- Summary Cards -->
        <div class="col-md-4">
            <x-card class="bg-primary text-primary-fg">
                <div class="card-body">
                    <div class="subheader text-primary-fg opacity-50">Total Diesel (Selected Range)</div>
                    <div class="h1 mb-3">{{ number_format($totalDiesel, 2) }} L</div>
                </div>
            </x-card>
        </div>
        <div class="col-md-4">
            <x-card>
                <div class="card-body">
                    <div class="subheader">Current Month Consumption</div>
                    <div class="h1 mb-3">
                        @php
                            $currentMonth = date('m');
                            $monthTotal = $monthlyConsumption[$currentMonth] ?? 0;
                        @endphp
                        {{ number_format($monthTotal, 2) }} L
                    </div>
                </div>
            </x-card>
        </div>
        <div class="col-md-4">
            <x-card>
                <div class="card-body">
                    <div class="subheader">Highest Consumer (Range)</div>
                    <div class="h1 mb-3">
                        @php
                            $highest = $perVehicle->sortByDesc('total_liters')->first();
                        @endphp
                        @if($highest)
                            {{ $highest->vehicle->registration_number }} ({{ number_format($highest->total_liters, 1) }}L)
                        @else
                            -
                        @endif
                    </div>
                </div>
            </x-card>
        </div>

        <!-- Filter and Table -->
        <div class="col-lg-8">
            <x-card>
                <x-slot name="header">
                    <h3 class="card-title">Diesel Entries</h3>
                </x-slot>

                <div class="card-body border-bottom py-3">
                    <form method="GET" action="{{ route('diesel.index') }}" class="row g-2">
                        <div class="col-md-2">
                            <input type="date" name="start_date" value="{{ $startDate }}"
                                class="form-control form-control-sm">
                        </div>
                        <div class="col-md-2">
                            <input type="date" name="end_date" value="{{ $endDate }}"
                                class="form-control form-control-sm">
                        </div>
                        <div class="col-md-3">
                            <select name="vehicle_id" class="form-select form-select-sm">
                                <option value="">All Vehicles</option>
                                @foreach($vehicles as $vehicle)
                                    <option value="{{ $vehicle->id }}" {{ request('vehicle_id') == $vehicle->id ? 'selected' : '' }}>
                                        {{ $vehicle->registration_number }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select name="operational_unit_id" class="form-select form-select-sm">
                                <option value="">All Units</option>
                                @foreach($locations as $location)
                                    <option value="{{ $location->id }}" {{ request('operational_unit_id') == $location->id ? 'selected' : '' }}>
                                        {{ $location->name }} ({{ $location->code }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-sm btn-primary w-100">Filter</button>
                        </div>
                    </form>
                </div>

                <x-table>
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Unit</th>
                            <th>Vehicle / Machine</th>
                            <th class="text-end">Liters Issued</th>
                            <th>Work Type</th>
                            <th>Driver</th>
                            <th>Linked Trip</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($dieselEntries as $entry)
                            <tr>
                                <td>{{ $entry->date->format('d/m/Y') }}</td>
                                <td>
                                    <span class="badge bg-azure-lt">{{ $entry->operationalUnit->code ?? 'N/A' }}</span>
                                </td>
                                <td>{{ $entry->vehicle->registration_number }}</td>
                                <td class="text-end fw-bold text-primary">{{ number_format($entry->liters, 2) }} L</td>
                                <td>
                                    <span class="text-uppercase small fw-medium">{{ $entry->work_type }}</span>
                                </td>
                                <td>{{ $entry->driver_name }}</td>
                                <td>
                                    @if($entry->gate_pass_id)
                                        <a href="{{ route('gate-passes.show', $entry->gate_pass_id) }}" class="small">
                                            #{{ $entry->gatePass->gate_pass_number }}
                                        </a>
                                    @else
                                        -
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-list">
                                        <a href="{{ route('diesel.edit', $entry) }}" class="btn btn-sm btn-white">Edit</a>
                                        <form action="{{ route('diesel.destroy', $entry) }}" method="POST"
                                            onsubmit="return confirm('Are you sure?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-ghost-danger">Delete</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-4 text-muted">No entries found for the selected range.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </x-table>

                <div class="card-footer d-flex align-items-center">
                    {{ $dieselEntries->links() }}
                </div>
            </x-card>
        </div>

        <!-- Sidebar Summary -->
        <div class="col-lg-4">
            <x-card class="mb-3">
                <x-slot name="header">
                    <h3 class="card-title">Per Unit Summary</h3>
                </x-slot>
                <div class="table-responsive">
                    <table class="table table-vcenter card-table">
                        <thead>
                            <tr>
                                <th>Unit</th>
                                <th class="text-end">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($perLocation as $row)
                                <tr>
                                    <td>
                                        <span class="badge bg-azure-lt">{{ $row->operationalUnit->code ?? 'N/A' }}</span>
                                        <small class="text-muted ms-1">{{ $row->operationalUnit->name ?? '' }}</small>
                                    </td>
                                    <td class="text-end fw-bold">{{ number_format($row->total_liters, 2) }} L</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </x-card>

            <x-card class="mb-3">
                <x-slot name="header">
                    <h3 class="card-title">Per Vehicle Summary</h3>
                </x-slot>
                <div class="table-responsive">
                    <table class="table table-vcenter card-table">
                        <thead>
                            <tr>
                                <th>Vehicle</th>
                                <th class="text-end">Total Liters</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($perVehicle->sortByDesc('total_liters') as $row)
                                <tr>
                                    <td>{{ $row->vehicle->registration_number }}</td>
                                    <td class="text-end fw-bold">{{ number_format($row->total_liters, 2) }} L</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </x-card>

            <x-card class="mb-3">
                <x-slot name="header">
                    <h3 class="card-title">Daily Consumption ({{ \Carbon\Carbon::parse($startDate)->format('M d') }} -
                        {{ \Carbon\Carbon::parse($endDate)->format('M d') }})
                    </h3>
                </x-slot>
                <div class="table-responsive" style="max-height: 300px; overflow-y: auto;">
                    <table class="table table-vcenter card-table">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th class="text-end">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($dailySummary as $row)
                                <tr>
                                    <td>{{ \Carbon\Carbon::parse($row->date)->format('d M, Y') }}</td>
                                    <td class="text-end fw-bold">{{ number_format($row->daily_total, 2) }} L</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </x-card>

            <x-card>
                <x-slot name="header">
                    <h3 class="card-title">Monthly Consumption ({{ date('Y') }})</h3>
                </x-slot>
                <div class="table-responsive">
                    <table class="table table-vcenter card-table">
                        <thead>
                            <tr>
                                <th>Month</th>
                                <th class="text-end">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @for($i = 1; $i <= 12; $i++)
                                @php
                                    $m = str_pad($i, 2, '0', STR_PAD_LEFT);
                                    $total = $monthlyConsumption[$m] ?? 0;
                                @endphp
                                @if($total > 0 || $i <= date('m'))
                                    <tr>
                                        <td>{{ DateTime::createFromFormat('!m', $i)->format('F') }}</td>
                                        <td class="text-end fw-bold">{{ number_format($total, 2) }} L</td>
                                    </tr>
                                @endif
                            @endfor
                        </tbody>
                    </table>
                </div>
            </x-card>
        </div>
    </div>
</x-tabler-layout>