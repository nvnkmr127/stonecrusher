<x-tabler-layout>
    <x-slot name="header">
        <x-page-header title="Gate Passes" subtitle="Track vehicle movements and sales" :breadcrumbs="[
        ['label' => 'Dashboard', 'route' => 'dashboard'],
        ['label' => 'Gate Passes', 'active' => true],
    ]">
            <x-slot name="actions">
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
            </x-slot>
        </x-page-header>
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
                                    <x-status-badge :status="$gp->status->value ?? $gp->status" />
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
                                            <button type="button" class="btn btn-sm btn-outline-success" x-data
                                                x-on:click="$dispatch('open-payment-modal', { id: '{{ $gp->id }}', number: '{{ $gp->gate_pass_number }}', balance: '{{ $gp->total_amount - $gp->paid_amount }}' })">
                                                Pay
                                            </button>
                                        @endif
                                        <a href="{{ route('gate-passes.edit', $gp) }}"
                                            class="btn btn-sm btn-primary">Edit</a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10">
                                    <x-empty-state title="No Gate Passes Found"
                                        description="Try adjusting your search or create a new gate pass."
                                        action='<a href="{{ route("gate-passes.create") }}" class="btn btn-primary">Create Gate Pass</a>' />
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



    <x-modal name="payment-modal" :show="$errors->payment->isNotEmpty()" maxWidth="md" title="Record Payment">
        <div x-data="{ 
            gpId: '', 
            gpNumber: '', 
            amount: '',
            init() {
                window.addEventListener('open-payment-modal', event => {
                    this.gpId = event.detail.id;
                    this.gpNumber = event.detail.number;
                    this.amount = event.detail.balance;
                    $dispatch('open-modal', 'payment-modal');
                });
            }
        }">
            <form x-bind:action="`/gate-passes/${gpId}/payment`" method="POST">
                @csrf

                <p class="mb-4 text-sm text-secondary">
                    Recording payment for Gate Pass: <span class="font-bold text-primary" x-text="gpNumber"></span>
                </p>

                <div class="mb-3">
                    <label class="form-label required">Amount Received</label>
                    <input type="number" step="0.01" class="form-control" name="amount" x-model="amount" required>
                    @error('amount', 'payment') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label required">Date</label>
                    <input type="date" class="form-control" name="date" value="{{ date('Y-m-d') }}" required>
                    @error('date', 'payment') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label required">Payment Mode</label>
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

                <div class="mt-4 flex justify-end gap-2">
                    <button type="button" class="btn btn-ghost"
                        x-on:click="$dispatch('close-modal', 'payment-modal')">Cancel</button>
                    <button type="submit" class="btn btn-success">Record Payment</button>
                </div>
            </form>
        </div>
    </x-modal>
</x-tabler-layout>