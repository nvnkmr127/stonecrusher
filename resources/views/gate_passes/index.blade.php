<x-tabler-layout>
    <x-slot name="header">
        <div class="row align-items-center">
            <div class="col">
                <div class="mb-1">
                    <x-breadcrumb>
                        <x-breadcrumb-item href="{{ route('dashboard') }}">Dashboard</x-breadcrumb-item>
                        <x-breadcrumb-item active>Gate Passes</x-breadcrumb-item>
                    </x-breadcrumb>
                </div>
                <h2 class="page-title">Gate Passes</h2>
                <div class="page-subtitle">Track vehicle movements and sales</div>
            </div>
            <div class="col-auto">
                <a href="{{ route('gate-passes.create') }}" class="btn btn-primary">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24"
                        stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round"
                        stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                        <path d="M12 5l0 14" />
                        <path d="M5 12l14 0" />
                    </svg>
                    New Gate Pass
                </a>
                <a href="{{ route('gate-passes.daily-report') }}" class="btn btn-secondary ms-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24"
                        stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round"
                        stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                        <path d="M14 3v4a1 1 0 0 0 1 1h4" />
                        <path d="M17 21h-10a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2z" />
                        <path d="M9 9l1 0" />
                        <path d="M9 13l6 0" />
                        <path d="M9 17l6 0" />
                    </svg>
                    Daily Report
                </a>
            </div>
        </div>
    </x-slot>

    <div class="row row-deck row-cards">
        <div class="col-12">
            <x-card>
                <x-slot name="header">
                    Gate Passes ({{ $gatePasses->total() }})
                </x-slot>

                <div class="card-body border-bottom py-3">
                    <div class="d-flex flex-column flex-md-row align-items-center gap-2">
                        <div class="ms-md-auto text-muted">
                            Search:
                            <div class="d-inline-block">
                                <form method="GET" action="{{ route('gate-passes.index') }}">
                                    <input type="text" name="search" value="{{ request('search') }}"
                                        class="form-control form-control-sm" aria-label="Search gate pass"
                                        placeholder="GP No, Vehicle, or Client...">
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <x-table>
                    <thead>
                        <tr>
                            <th>GP Number</th>
                            <th>Date</th>
                            <th>Vehicle</th>
                            <th>Client</th>
                            <th>Material</th>
                            <th>Weight/Qty</th>
                            <th>Diesel/Adv</th>
                            <th>Status</th>
                            <th>Sale Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($gatePasses as $gp)
                            <tr>
                                <td>
                                    <a href="{{ route('gate-passes.edit', $gp) }}"
                                        class="text-reset fw-bold">{{ $gp->gate_pass_number }}</a>
                                </td>
                                <td>{{ $gp->date->format('d M, Y h:i A') }}</td>
                                <td>{{ $gp->vehicle->vehicle_number }}</td>
                                <td>{{ $gp->client->name ?? '-' }}</td>
                                <td>{{ $gp->metalType->name ?? '-' }}</td>
                                <td>
                                    @if($gp->loading_quantity > 0)
                                        {{ $gp->loading_quantity }} CFT
                                    @elseif($gp->net_weight > 0)
                                        {{ $gp->net_weight }} Tons
                                    @else
                                        -
                                    @endif
                                </td>
                                <td>
                                    @if($gp->diesel_amount > 0)
                                        <div class="text-danger" title="Diesel">D: {{ $gp->diesel_amount }}</div>
                                    @endif
                                    @if($gp->advance_amount > 0)
                                        <div class="text-info" title="Advance">A: {{ $gp->advance_amount }}</div>
                                    @endif
                                    @if($gp->diesel_amount <= 0 && $gp->advance_amount <= 0)
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    @if($gp->status == 'completed')
                                        <span class="badge bg-success">Completed</span>
                                    @elseif($gp->status == 'cancelled')
                                        <span class="badge bg-danger">Cancelled</span>
                                    @else
                                        <span class="badge bg-warning">Pending</span>
                                    @endif
                                </td>
                                <td>
                                    @if($gp->transaction)
                                        <span class="badge bg-teal" title="Transaction Ref: {{ $gp->transaction->id }}">
                                            Billed: ₹{{ number_format($gp->transaction->amount, 0) }}
                                        </span>
                                    @elseif($gp->status == 'completed' && $gp->client_id)
                                        <span class="badge bg-warning">Unbilled</span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-list">
                                        @if($gp->status == 'completed' && $gp->paid_amount < $gp->total_amount)
                                            <button type="button" class="btn btn-sm btn-outline-success"
                                                onclick="openPaymentModal('{{ $gp->id }}', '{{ $gp->gate_pass_number }}', '{{ $gp->total_amount - $gp->paid_amount }}')">
                                                Pay
                                            </button>
                                        @endif
                                        <a href="{{ route('gate-passes.edit', $gp) }}"
                                            class="btn btn-sm btn-primary">Edit</a>
                                        <!-- Add print button later -->
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10">
                                    <x-empty-state
                                        title="No Gate Passes Found"
                                        description="Try adjusting your search or create a new gate pass."
                                        action='<a href="{{ route("gate-passes.create") }}" class="btn btn-primary">Create Gate Pass</a>'
                                    />
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </x-table>

                <div class="mt-3">
                    {{ $gatePasses->links() }}
                </div>
            </x-card>
        </div>
    </div>

    <!-- Payment Modal -->
    <div class="modal modal-blur fade" id="paymentModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <form id="paymentForm" action="" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">Record Payment for <span id="modalGpNumber"></span></h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Amount Received</label>
                            <input type="number" step="0.01" class="form-control" name="amount" id="modalAmount"
                                required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Date</label>
                            <input type="date" class="form-control" name="date" value="{{ date('Y-m-d') }}" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Payment Mode</label>
                            <select class="form-select" name="payment_mode" required>
                                <option value="cash">Cash</option>
                                <option value="bank">Bank Transfer</option>
                                <option value="upi">UPI</option>
                                <option value="cheque">Cheque</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Remarks</label>
                            <textarea class="form-control" name="remarks" rows="2"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn me-auto" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-success">Record Payment</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            function openPaymentModal(id, number, balance) {
                const form = document.getElementById('paymentForm');
                form.action = `/gate-passes/${id}/payment`;

                document.getElementById('modalGpNumber').textContent = number;
                document.getElementById('modalAmount').value = balance; // Auto-fill balance

                const modal = new bootstrap.Modal(document.getElementById('paymentModal'));
                modal.show();
            }
        </script>
    @endpush
</x-tabler-layout>