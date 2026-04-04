<x-tabler-layout>
    <x-slot name="header">
        <div class="row align-items-center d-print-none">
            <div class="col">
                <div class="page-pretitle text-uppercase fw-bold text-muted" style="letter-spacing: 0.05em;">Logistics / Gate Pass</div>
                <h2 class="page-title h1 fw-bold">Gate Pass #{{ $gatePass->gate_pass_number }}</h2>
            </div>
            <div class="col-auto ms-auto">
                <div class="btn-list">
                    <button onclick="window.print()" class="btn btn-white shadow-sm">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-printer" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"></path><path d="M17 17h2a2 2 0 0 0 2 -2v-4a2 2 0 0 0 -2 -2h-14a2 2 0 0 0 -2 2v4a2 2 0 0 0 2 2h2"></path><path d="M17 9v-4a2 2 0 0 0 -2 -2h-6a2 2 0 0 0 -2 2v4"></path><path d="M7 13m0 2a2 2 0 0 1 2 -2h6a2 2 0 0 1 2 2v4a2 2 0 0 1 -2 2h-6a2 2 0 0 1 -2 -2z"></path></svg>
                        Print Pass
                    </button>
                    @if($gatePass->status->value !== 'cancelled')
                        <a href="{{ route('gate-passes.edit', $gatePass) }}" class="btn btn-primary shadow-sm">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-pencil" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"></path><path d="M4 20h4l10.5 -10.5a1.5 1.5 0 0 0 -4 -4l-10.5 10.5v4"></path><line x1="13.5" y1="6.5" x2="17.5" y2="10.5"></line></svg>
                            Edit Pass
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </x-slot>

    @php
        $destType = 'registered';
        $destAlert = 'success';
        $destTitle = 'Selling to Client';
        $destDesc = 'Registered account';
        $destIcon = 'shopping-cart';

        if ($gatePass->activity_type->value === 'Material Transfer') {
            $destType = 'transfer';
            $destAlert = 'azure';
            $destTitle = 'Transfer';
            $destDesc = 'Quarry to Crusher';
            $destIcon = 'truck-delivery';
        } elseif ($gatePass->activity_type->value === 'Internal Movement' || ($gatePass->project && $gatePass->project->is_internal)) {
            $destType = 'internal';
            $destAlert = 'info';
            $destTitle = 'Internal Project';
            $destDesc = 'Own project usage';
            $destIcon = 'refresh';
        } elseif ($gatePass->manual_customer_name) {
            $destType = 'regular';
            $destTitle = 'Regular Sale';
            $destDesc = 'Manual customer';
        }
    @endphp

    <div class="premium-header-card">
        <div class="row align-items-center">
            <div class="col-md-7">
                <div class="d-flex align-items-center mb-3">
                    <span class="avatar avatar-lg bg-white-lt text-white me-3 border border-white-subtle" style="backdrop-filter: blur(4px);">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-truck" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><circle cx="7" cy="17" r="2" /><circle cx="17" cy="17" r="2" /><path d="M5 17h-2v-11a1 1 0 0 1 1 -1h9v12m-4 0h6m4 0h2v-6h-8m0 -5h5l3 5" /></svg>
                    </span>
                    <div>
                        <div class="text-white-50 small text-uppercase fw-bold" style="letter-spacing: 1px;">Vehicle & Logistics</div>
                        <h1 class="mb-0 fw-bold">{{ $gatePass->vehicle->registration_number ?? $gatePass->manual_vehicle_number }}</h1>
                    </div>
                </div>
                <div class="d-flex flex-wrap gap-4 mt-4">
                    <div class="bg-white-lt p-2 px-3 rounded-3" style="backdrop-filter: blur(4px);">
                        <div class="small opacity-50 mb-1">RECORDED ON</div>
                        <div class="fw-bold">{{ $gatePass->date->format('d M, Y h:i A') }}</div>
                    </div>
                    <div class="bg-white-lt p-2 px-3 rounded-3" style="backdrop-filter: blur(4px);">
                        <div class="small opacity-50 mb-1">ACTIVITY TYPE</div>
                        <div class="fw-bold">{{ $gatePass->activity_type->value }}</div>
                    </div>
                </div>
            </div>
            <div class="col-md-5 text-md-end mt-4 mt-md-0">
                <div class="p-4 bg-white-lt rounded-4 border border-white-subtle" style="backdrop-filter: blur(8px);">
                    <div class="text-white-50 small text-uppercase fw-bold mb-1">Status Overview</div>
                    <div class="h2 mb-2 fw-bold d-flex align-items-center justify-content-md-end">
                        <x-status-badge :status="$gatePass->status->value" />
                    </div>
                    @if($gatePass->total_amount > 0)
                        <div class="mt-3">
                            <div class="small opacity-50 mb-1 fw-bold">TOTAL BILLED VALUE</div>
                            <div class="h1 mb-0 fw-bold">₹ {{ number_format($gatePass->total_amount, 2) }}</div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Stats Grid -->
    <div class="premium-stats-grid mt-4">
        <div class="stat-premium-card">
            <div class="stat-icon-wrapper bg-blue-lt">
                <svg xmlns="http://www.w3.org/2000/svg" class="icon text-blue" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 3l8 4.5l0 9l-8 4.5l-8 -4.5l0 -9l8 -4.5" /><path d="M12 12l8 -4.5" /><path d="M12 12l0 9" /><path d="M12 12l-8 -4.5" /><path d="M16 5.25l-8 4.5" /></svg>
            </div>
            <div class="text-muted small fw-bold text-uppercase mb-1">Material Quantity</div>
            <div class="h2 mb-0 fw-bold">{{ number_format($gatePass->loading_quantity ?: $gatePass->net_weight, 2) }} <span class="small text-muted">CFT</span></div>
            <div class="text-muted mt-2 small fw-medium">{{ $gatePass->metalType->name ?? 'Mixed' }}</div>
        </div>

        <div class="stat-premium-card">
            <div class="stat-icon-wrapper bg-orange-lt">
                <svg xmlns="http://www.w3.org/2000/svg" class="icon text-orange" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 17v2a2 2 0 0 0 2 2h12a2 2 0 0 0 2 -2v-2" /><path d="M7 9l5 5l5 -5" /><path d="M12 4l0 10" /></svg>
            </div>
            <div class="text-muted small fw-bold text-uppercase mb-1">Loading Weight</div>
            <div class="h2 mb-0 fw-bold">{{ number_format($gatePass->net_weight, 2) }} <span class="small text-muted">Tons</span></div>
            <div class="text-muted mt-2 small fw-medium">Gross: {{ number_format($gatePass->gross_weight, 2) }}</div>
        </div>

        <div class="stat-premium-card">
            <div class="stat-icon-wrapper bg-teal-lt">
                <svg xmlns="http://www.w3.org/2000/svg" class="icon text-teal" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M17 8l4 4l-4 4" /><path d="M3 12l18 0" /></svg>
            </div>
            <div class="text-muted small fw-bold text-uppercase mb-1">Distance</div>
            <div class="h2 mb-0 fw-bold">{{ $gatePass->distance_km ?: 0 }} <span class="small text-muted">KM</span></div>
            <div class="text-muted mt-2 small fw-medium">{{ Str::limit($gatePass->delivery_location ?: 'Direct Sale', 20) }}</div>
        </div>

        <div class="stat-premium-card">
            <div class="stat-icon-wrapper bg-purple-lt">
                <svg xmlns="http://www.w3.org/2000/svg" class="icon text-purple" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><circle cx="12" cy="12" r="9" /><path d="M14.8 9a2 2 0 0 0 -1.8 -1h-2a2 2 0 0 0 0 4h2a2 2 0 0 1 0 4h-2a2 2 0 0 1 -1.8 -1" /><path d="M12 6v2m0 8v2" /></svg>
            </div>
            <div class="text-muted small fw-bold text-uppercase mb-1">Rate</div>
            <div class="h2 mb-0 fw-bold">₹{{ number_format($gatePass->rate_per_ton, 0) }}</div>
            <div class="text-muted mt-2 small fw-medium">Lead: ₹{{ number_format($gatePass->lead, 0) }}</div>
        </div>
    </div>

    <div class="row row-cards mt-2">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-transparent border-0 py-3">
                    <h3 class="card-title fw-bold">Detailed Information</h3>
                </div>
                <div class="table-responsive">
                    <table class="table table-vcenter table-premium card-table mt-0">
                        <tbody>
                            <tr>
                                <td style="width: 30%" class="text-muted fw-bold small text-uppercase">Logistics Summary</td>
                                <td>
                                    <div class="fw-bold fs-3 text-primary">{{ $destTitle }}</div>
                                    <div class="text-muted">{{ $destDesc }}</div>
                                    @if($gatePass->sourceUnit || $gatePass->destinationUnit)
                                        <div class="mt-2 p-2 bg-light rounded-3 d-flex align-items-center gap-2">
                                            <span class="fw-bold">{{ $gatePass->sourceUnit->name ?? 'QRY' }}</span>
                                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-arrow-narrow-right" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"></path><line x1="5" y1="12" x2="19" y2="12"></line><line x1="15" y1="16" x2="19" y2="12"></line><line x1="15" y1="8" x2="19" y2="12"></line></svg>
                                            <span class="fw-bold">{{ $gatePass->destinationUnit->name ?? 'CRS' }}</span>
                                        </div>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td class="text-muted fw-bold small text-uppercase">Customer/Client</td>
                                <td>
                                    @if($gatePass->client)
                                        <div class="fw-bold">{{ $gatePass->client->name }}</div>
                                        <div class="small text-muted">{{ $gatePass->client->phone }}</div>
                                        <a href="{{ route('clients.show', $gatePass->client) }}" class="btn btn-sm btn-ghost-primary mt-2">View Profile</a>
                                    @else
                                        <div class="fw-bold">{{ $gatePass->manual_customer_name ?: 'Internal/Transfer' }}</div>
                                        @if($gatePass->village_area)
                                            <div class="small text-muted">{{ $gatePass->village_area }}</div>
                                        @endif
                                    @endif
                                </td>
                            </tr>
                            @if($gatePass->remarks)
                                <tr>
                                    <td class="text-muted fw-bold small text-uppercase">Remarks</td>
                                    <td>
                                        <div class="fst-italic text-secondary">"{{ $gatePass->remarks }}"</div>
                                    </td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>

            @if($gatePass->transaction)
                <div class="card border-0 bg-blue-lt shadow-sm mb-4 overflow-hidden position-relative">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center">
                            <div class="stat-icon-wrapper bg-blue-lt text-blue me-4" style="width: 64px; height: 64px;">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-receipt" width="32" height="32" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none" /><path d="M5 21v-16a2 2 0 0 1 2 -2h10a2 2 0 0 1 2 2v16l-3 -2l-2 2l-2 -2l-2 2l-2 -2l-3 2" /><path d="M9 7h6" /><path d="M9 11h6" /><path d="M13 15h2" /></svg>
                            </div>
                            <div>
                                <h3 class="fw-bold h2 mb-1">Financial Link</h3>
                                <div class="text-muted fw-medium">
                                    Linked to transaction <span class="badge bg-blue text-white px-2">#{{ $gatePass->transaction->id }}</span>
                                </div>
                                <div class="mt-3">
                                    <span class="btn btn-primary shadow-sm btn-sm px-4">View Ledger Entry</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <div class="col-lg-4">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-transparent border-0 py-3">
                    <h3 class="card-title fw-bold">Financial Summary</h3>
                </div>
                <div class="card-body py-2">
                    <div class="d-flex justify-content-between mb-3">
                        <span class="text-muted fw-medium small text-uppercase">Rate per CFT</span>
                        <span class="fw-bold">₹{{ number_format($gatePass->rate_per_ton, 2) }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-3">
                        <span class="text-muted fw-medium small text-uppercase">Transport / Lead</span>
                        <span class="fw-bold text-azure">+ ₹{{ number_format($gatePass->lead, 2) }}</span>
                    </div>
                    <hr class="my-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="h4 mb-0 fw-bold">TOTAL AMOUNT</span>
                        <span class="h1 mb-0 fw-bold text-primary">₹ {{ number_format($gatePass->total_amount, 2) }}</span>
                    </div>
                </div>
                <div class="card-footer bg-light border-0 py-3">
                    @if($gatePass->paid_amount >= $gatePass->total_amount)
                        <div class="d-flex align-items-center text-green fw-bold">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-circle-check me-2" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none" /><path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" /><path d="M9 12l2 2l4 -4" /></svg>
                            FULLY PAID
                        </div>
                    @elseif($gatePass->paid_amount > 0)
                        <div class="progress mb-2" style="height: 6px; background: rgba(0,0,0,0.05);">
                            <div class="progress-bar bg-warning shadow-none" style="width: {{ ($gatePass->paid_amount / $gatePass->total_amount) * 100 }}%"></div>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span class="small text-muted fw-bold">PARTIALLY PAID</span>
                            <span class="small fw-bold text-orange">₹{{ number_format($gatePass->paid_amount, 2) }}</span>
                        </div>
                    @else
                        <div class="d-flex align-items-center text-red fw-bold">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-alert-circle me-2" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none" /><circle cx="12" cy="12" r="9" /><line x1="12" y1="8" x2="12" y2="12" /><line x1="12" y1="16" x2="12.01" y2="16" /></svg>
                            UNPAID
                        </div>
                    @endif
                </div>
            </div>

            <div class="card border-0 shadow-sm overflow-hidden">
                <div class="card-header bg-transparent border-0 py-3">
                    <h3 class="card-title fw-bold">Logistics Summary</h3>
                </div>
                <div class="card-body pt-0">
                    <div class="row g-3">
                        <div class="col-6">
                            <div class="text-muted small mb-1 text-uppercase fw-bold">Tare Weight</div>
                            <div class="fw-bold">{{ number_format($gatePass->tare_weight, 2) }} T</div>
                        </div>
                        <div class="col-6">
                            <div class="text-muted small mb-1 text-uppercase fw-bold">Gross Weight</div>
                            <div class="fw-bold">{{ number_format($gatePass->gross_weight, 2) }} T</div>
                        </div>
                        <div class="col-12">
                            <hr class="my-1 border-light">
                        </div>
                        <div class="col-12">
                            <div class="text-muted small mb-1 text-uppercase fw-bold">Driver Name</div>
                            <div class="fw-bold">{{ $gatePass->driver_name ?: 'Self/Direct' }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-tabler-layout>