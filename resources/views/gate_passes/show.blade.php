<x-tabler-layout>
    <x-slot name="header">
        <div class="row align-items-center">
            <div class="col">
                <div class="mb-1">
                    <x-breadcrumb>
                        <x-breadcrumb-item href="{{ route('dashboard') }}">Dashboard</x-breadcrumb-item>
                        <x-breadcrumb-item href="{{ route('gate-passes.index') }}">Gate Passes</x-breadcrumb-item>
                        <x-breadcrumb-item active>#{{ $gatePass->gate_pass_number }}</x-breadcrumb-item>
                    </x-breadcrumb>
                </div>
                <h2 class="page-title">Gate Pass #{{ $gatePass->gate_pass_number }}</h2>
                <div class="page-subtitle">
                    Recorded on {{ $gatePass->date->format('d M Y, h:i A') }}
                </div>
            </div>
            <div class="col-auto">
                <div class="btn-list">
                    @if($gatePass->status->value !== 'cancelled')
                        <a href="{{ route('gate-passes.edit', $gatePass) }}" class="btn btn-primary">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-edit" width="24"
                                height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path d="M9 7h-3a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-3" />
                                <path d="M9 15h3l8.5 -8.5a1.5 1.5 0 0 0 -3 -3l-8.5 8.5v3" />
                                <line x1="16" y1="5" x2="19" y2="8" />
                            </svg>
                            Edit
                        </a>
                    @endif
                    <a href="{{ route('gate-passes.index') }}" class="btn btn-secondary">
                        Back to List
                    </a>
                </div>
            </div>
        </div>
    </x-slot>

    <div class="row row-cards">
        <!-- Main Status & Quick Info -->
        <div class="col-md-4">
            <div class="card card-sm">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-auto">
                            <span
                                class="bg-{{ $gatePass->status->value === 'completed' ? 'success' : ($gatePass->status->value === 'pending' ? 'warning' : 'danger') }} text-white avatar">
                                @if($gatePass->status->value === 'completed')
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-check"
                                        width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
                                        fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                        <path d="M5 12l5 5l10 -10" />
                                    </svg>
                                @elseif($gatePass->status->value === 'pending')
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-clock"
                                        width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
                                        fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                        <path d="M3 12a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" />
                                        <path d="M12 7v5l3 3" />
                                    </svg>
                                @else
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-x"
                                        width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
                                        fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                        <path d="M18 6l-12 12" />
                                        <path d="M6 6l12 12" />
                                    </svg>
                                @endif
                            </span>
                        </div>
                        <div class="col">
                            <div class="font-weight-medium">
                                Status: {{ ucfirst($gatePass->status->value) }}
                            </div>
                            <div class="text-muted">
                                {{ $gatePass->activity_type->value }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <x-card class="mt-3">
                <div class="card-header">
                    <h3 class="card-title">Vehicle & Driver</h3>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label text-muted">Vehicle Number</label>
                        <div class="fw-bold fs-2 text-uppercase">
                            {{ $gatePass->vehicle->registration_number ?? $gatePass->manual_vehicle_number }}
                        </div>
                        @if(!$gatePass->vehicle_id)
                            <span class="badge bg-warning-lt">Manual Entry</span>
                        @endif
                    </div>
                    @if($gatePass->driver_name)
                        <div class="mb-0">
                            <label class="form-label text-muted">Driver Name</label>
                            <div class="fw-bold">{{ $gatePass->driver_name }}</div>
                        </div>
                    @endif
                </div>
                <div class="card-table">
                    <table class="table table-vcenter">
                        <tr>
                            <td class="text-muted">Trips</td>
                            <td class="text-end fw-bold">{{ $gatePass->trips }}</td>
                        </tr>
                        @if($gatePass->sourceUnit)
                            <tr>
                                <td class="text-muted">Source</td>
                                <td class="text-end fw-bold">{{ $gatePass->sourceUnit->name }}</td>
                            </tr>
                        @endif
                        @if($gatePass->destinationUnit)
                            <tr>
                                <td class="text-muted">Destination</td>
                                <td class="text-end fw-bold">{{ $gatePass->destinationUnit->name }}</td>
                            </tr>
                        @endif
                    </table>
                </div>
            </x-card>

            <x-card class="mt-3">
                <div class="card-header">
                    <h3 class="card-title">Movement Type</h3>
                </div>
                <div class="card-body">
                    <div
                        class="alert alert-{{ $gatePass->activity_type->value === 'Sales' ? 'success' : ($gatePass->activity_type->value === 'Internal Movement' ? 'info' : 'azure') }} mb-0">
                        <div class="d-flex align-items-center">
                            <div class="me-3">
                                @if($gatePass->activity_type->value === 'Sales')
                                    <svg xmlns="http://www.w3.org/2000/svg"
                                        class="icon icon-tabler icon-tabler-shopping-cart" width="24" height="24"
                                        viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                        stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                        <path d="M6 19m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0" />
                                        <path d="M17 19m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0" />
                                        <path d="M17 17h-11v-14h-2" />
                                        <path d="M6 5l14 1l-1 7h-13" />
                                    </svg>
                                @elseif($gatePass->activity_type->value === 'Internal Movement')
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-refresh"
                                        width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
                                        fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                        <path d="M20 11a8.1 8.1 0 0 0 -15.5 -2m-.5 -4v4h4" />
                                        <path d="M4 13a8.1 8.1 0 0 0 15.5 2m.5 4v-4h-4" />
                                    </svg>
                                @else
                                    <svg xmlns="http://www.w3.org/2000/svg"
                                        class="icon icon-tabler icon-tabler-truck-delivery" width="24" height="24"
                                        viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                        stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                        <path d="M7 17m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0" />
                                        <path d="M17 17m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0" />
                                        <path d="M5 17h-2v-4m-1 -8h11v12m-4 0h6m4 0h2v-6h-8m0 -5h5l3 5" />
                                        <path d="M3 9l4 0" />
                                    </svg>
                                @endif
                            </div>
                            <div>
                                <div class="fw-bold">{{ $gatePass->activity_type->value }}</div>
                                <div class="small">
                                    @if($gatePass->activity_type->value === 'Sales')
                                        Revenue generating movement.
                                    @elseif($gatePass->activity_type->value === 'Internal Movement')
                                        Usage within own units/projects.
                                    @else
                                        Moving material between stock points.
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </x-card>
        </div>

        <!-- Details & Financials -->
        <div class="col-md-8">
            <div class="row row-cards">
                <!-- Customer & Project -->
                <div class="col-12">
                    <x-card>
                        <div class="card-header">
                            <h3 class="card-title">Customer / Client Details</h3>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <label class="form-label text-muted">Customer Name</label>
                                    <div class="fw-bold fs-3">
                                        @if($gatePass->client)
                                            <a
                                                href="{{ route('clients.show', $gatePass->client) }}">{{ $gatePass->client->name }}</a>
                                        @else
                                            {{ $gatePass->manual_customer_name ?: 'N/A' }}
                                            @if($gatePass->village_area)
                                                <div class="small text-muted mt-1">
                                                    <svg xmlns="http://www.w3.org/2000/svg"
                                                        class="icon icon-tabler icon-tabler-map-pin" width="16" height="16"
                                                        viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                                        stroke-linecap="round" stroke-linejoin="round">
                                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                        <path d="M9 11a3 3 0 1 0 6 0a3 3 0 1 0 -6 0" />
                                                        <path
                                                            d="M17.657 16.657l-4.243 4.243a2 2 0 0 1 -2.827 0l-4.244 -4.243a8 8 0 1 1 11.314 0z" />
                                                    </svg>
                                                    {{ $gatePass->village_area }}
                                                </div>
                                            @endif
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label text-muted">Project</label>
                                    <div class="fw-bold fs-3">
                                        @if($gatePass->project)
                                            {{ $gatePass->project->name }}
                                            @if($gatePass->project->is_internal)
                                                <span class="badge bg-info-lt ms-2">Internal Project</span>
                                            @endif
                                        @else
                                            <span class="text-muted">Direct Sale / No Project</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </x-card>
                </div>

                <!-- Material Detail -->
                <div class="col-md-6">
                    <x-card>
                        <div class="card-header">
                            <h3 class="card-title">Material & Quantity</h3>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="form-label text-muted">Material Type</label>
                                <div class="fw-bold fs-2">{{ $gatePass->metalType->name ?? 'N/A' }}</div>
                            </div>
                            <div class="row g-2">
                                <div class="col-6">
                                    <div class="p-3 bg-light rounded-2 text-center">
                                        <div class="text-muted small mb-1 text-uppercase">Net Qty</div>
                                        <div class="fw-bold fs-2 text-primary">
                                            {{ number_format($gatePass->loading_quantity ?: $gatePass->net_weight, 2) }}
                                        </div>
                                        <div class="small text-muted">CFT</div>
                                    </div>
                                </div>
                                @if($gatePass->gross_weight > 0)
                                    <div class="col-6">
                                        <div class="p-3 bg-light rounded-2 text-center">
                                            <div class="text-muted small mb-1 text-uppercase">Tons</div>
                                            <div class="fw-bold fs-2">
                                                {{ number_format($gatePass->net_weight, 2) }}
                                            </div>
                                            <div class="small text-muted">Calculated</div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                        @if($gatePass->gross_weight > 0)
                            <div class="card-table">
                                <table class="table table-vcenter table-sm">
                                    <tr>
                                        <td class="text-muted small ps-3">Gross Weight</td>
                                        <td class="text-end fw-bold pe-3">{{ number_format($gatePass->gross_weight, 2) }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted small ps-3">Tare Weight</td>
                                        <td class="text-end fw-bold pe-3">{{ number_format($gatePass->tare_weight, 2) }}
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        @endif
                    </x-card>
                </div>

                <!-- Financial Summary -->
                <div class="col-md-6">
                    <x-card>
                        <div class="card-header">
                            <h3 class="card-title">Financial Summary</h3>
                        </div>
                        <div class="card-body p-0">
                            <table class="table table-vcenter">
                                <tr>
                                    <td class="text-muted ps-3">Rate per CFT</td>
                                    <td class="text-end fw-bold pe-3">₹{{ number_format($gatePass->rate_per_ton, 2) }}
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-muted ps-3">Material Cost</td>
                                    <td class="text-end fw-bold pe-3">
                                        ₹{{ number_format(($gatePass->loading_quantity ?: $gatePass->net_weight) * $gatePass->rate_per_ton, 2) }}
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-muted ps-3">Diesel Allowance</td>
                                    <td class="text-end fw-bold text-azure pe-3">+
                                        ₹{{ number_format($gatePass->diesel_amount, 2) }}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted ps-3">
                                        Transport Cost
                                        @if($gatePass->transport_is_billable)
                                            <span class="badge bg-success-lt ms-1">Billed</span>
                                        @else
                                            <span class="badge bg-secondary-lt ms-1">Internal</span>
                                        @endif
                                    </td>
                                    <td class="text-end fw-bold text-azure pe-3">
                                        + ₹{{ number_format($gatePass->transport_cost, 2) }}
                                    </td>
                                </tr>
                                <tr class="bg-primary-lt">
                                    <td class="ps-3 fw-bold text-primary">Total Amount</td>
                                    <td class="text-end fw-bold text-primary pe-3 fs-3">
                                        ₹{{ number_format($gatePass->total_amount, 2) }}</td>
                                </tr>
                            </table>
                        </div>
                        @if($gatePass->status->value === 'completed')
                            <div class="card-footer p-2 text-center">
                                @if($gatePass->paid_amount >= $gatePass->total_amount)
                                    <span class="text-success fw-bold"><svg xmlns="http://www.w3.org/2000/svg"
                                            class="icon icon-tabler icon-tabler-circle-check" width="24" height="24"
                                            viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                            stroke-linecap="round" stroke-linejoin="round">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                            <path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" />
                                            <path d="M9 12l2 2l4 -4" />
                                        </svg> Fully Paid</span>
                                @elseif($gatePass->paid_amount > 0)
                                    <div class="progress mb-1" style="height: 4px;">
                                        <div class="progress-bar bg-warning"
                                            style="width: {{ ($gatePass->paid_amount / $gatePass->total_amount) * 100 }}%">
                                        </div>
                                    </div>
                                    <span class="text-warning small fw-bold">Partially Paid:
                                        ₹{{ number_format($gatePass->paid_amount, 2) }}</span>
                                @else
                                    <span class="text-danger fw-bold">Unpaid</span>
                                @endif
                            </div>
                        @endif
                    </x-card>
                </div>

                <!-- Logistics & Delivery -->
                <div class="col-12">
                    <x-card>
                        <div class="card-header">
                            <h3 class="card-title">Logistics & Delivery details</h3>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label text-muted small">Delivery Location</label>
                                    <div class="fw-medium">{{ $gatePass->delivery_location ?: 'Not specified' }}</div>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label text-muted small">Distance (KM)</label>
                                    <div class="fw-medium">{{ $gatePass->distance_km ?: 0 }} KM</div>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label text-muted small">Diesel Quantity</label>
                                    <div class="fw-medium">{{ number_format($gatePass->diesel_qty, 2) }} Liters</div>
                                </div>
                                @if($gatePass->remarks)
                                    <div class="col-12 border-top pt-3">
                                        <label class="form-label text-muted small">Remarks / Notes</label>
                                        <div class="bg-light p-3 rounded-2 text-dark italic">
                                            "{{ $gatePass->remarks }}"
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </x-card>
                </div>

                <!-- Associated Transaction -->
                @if($gatePass->transaction)
                    <div class="col-12">
                        <div class="card border-0 bg-teal-lt">
                            <div class="card-body p-3">
                                <div class="d-flex align-items-center">
                                    <span class="avatar bg-teal-lt me-3">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-receipt"
                                            width="24" height="24" viewBox="0 0 24 24" stroke-width="2"
                                            stroke="currentColor" fill="none" stroke-linecap="round"
                                            stroke-linejoin="round">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                            <path
                                                d="M5 21v-16a2 2 0 0 1 2 -2h10a2 2 0 0 1 2 2v16l-3 -2l-2 2l-2 -2l-2 2l-2 -2l-3 2" />
                                            <path d="M9 7h6" />
                                            <path d="M9 11h6" />
                                            <path d="M13 15h2" />
                                        </svg>
                                    </span>
                                    <div>
                                        <div class="fw-bold">Ledger Entry Created</div>
                                        <div class="small">This gate pass is linked to transaction
                                            <b>#{{ $gatePass->transaction->id }}</b>.
                                        </div>
                                    </div>
                                    <div class="ms-auto">
                                        <a href="#" class="btn btn-sm btn-teal">View Entry</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-tabler-layout>