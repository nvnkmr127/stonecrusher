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

    <div class="card border-0 shadow-sm overflow-hidden">
        <div class="card-header border-0 bg-transparent py-3">
            <div class="row align-items-center">
                <div class="col">
                    <h3 class="card-title fw-bold">Recent Movements</h3>
                </div>
                <div class="col-auto">
                    <form method="GET" action="{{ route('gate-passes.index') }}" class="input-group input-group-sm input-group-flat" style="width: 300px;">
                        <input type="text" name="search" value="{{ request('search') }}"
                            class="form-control ps-2" aria-label="Search gate pass"
                            placeholder="GP No, Vehicle, or Client...">
                        <span class="input-group-text">
                            <button type="submit" class="btn btn-sm btn-ghost-primary p-0 border-0">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-search" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"></path><circle cx="10" cy="10" r="7"></circle><line x1="21" y1="21" x2="15" y2="15"></line></svg>
                            </button>
                        </span>
                    </form>
                </div>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-vcenter table-premium card-table mt-0">
                <thead>
                    <tr>
                        <th style="width: 15%">GP / Date</th>
                        <th>Vehicle & Usage</th>
                        <th>Client / Project</th>
                        <th>Material Info</th>
                        <th>Status</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($gatePasses as $gp)
                        <tr>
                            <td>
                                <div>
                                    <a href="{{ route('gate-passes.show', $gp) }}" class="fw-bold text-reset">#{{ $gp->gate_pass_number }}</a>
                                </div>
                                <div class="small text-muted">{{ $gp->date->format('d M, Y') }}</div>
                                <div class="small text-muted" style="font-size: 0.65rem;">{{ $gp->date->format('h:i A') }}</div>
                            </td>
                            <td>
                                <div class="d-flex align-items-center mb-1">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-truck me-1 text-muted" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"></path><circle cx="7" cy="17" r="2"></circle><circle cx="17" cy="17" r="2"></circle><path d="M5 17h-2v-11a1 1 0 0 1 1 -1h9v12m-4 0h6m4 0h2v-6h-8m0 -5h5l3 5"></path></svg>
                                    <span class="fw-bold">{{ $gp->vehicle->registration_number ?? $gp->manual_vehicle_number ?? '-' }}</span>
                                </div>
                                <div class="small">
                                    <span class="badge bg-blue-lt text-blue px-1">{{ $gp->activity_type->value }}</span>
                                    @if($gp->activity_type->value == 'Material Transfer')
                                        <span class="text-muted" style="font-size: 0.7rem;">{{ $gp->sourceUnit->code ?? 'QRY' }} &rarr; {{ $gp->destinationUnit->code ?? 'CRS' }}</span>
                                    @endif
                                </div>
                            </td>
                            <td>
                                <div class="fw-medium">{{ $gp->client->name ?? $gp->manual_customer_name ?? '-' }}</div>
                                @if($gp->village_area)
                                    <div class="small text-muted italic" style="font-size: 0.7rem;">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-map-pin" width="12" height="12" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"></path><circle cx="12" cy="11" r="3"></circle><path d="M17.657 16.657l-4.243 4.243a2 2 0 0 1 -2.827 0l-4.244 -4.243a8 8 0 1 1 11.314 0z"></path></svg>
                                        {{ $gp->village_area }}
                                    </div>
                                @endif
                                @if($gp->project)
                                    <div class="small text-primary fw-bold" style="font-size: 0.7rem;">PRJ: {{ $gp->project->name }}</div>
                                @endif
                            </td>
                            <td>
                                <div class="fw-bold text-blue">{{ $gp->metalType->name ?? '-' }}</div>
                                <div class="small fw-medium">
                                    @if($gp->loading_quantity > 0)
                                        {{ $gp->loading_quantity }} CFT
                                    @elseif($gp->net_weight > 0)
                                        {{ $gp->net_weight }} CFT
                                    @else
                                        -
                                    @endif
                                </div>
                            </td>
                            <td>
                                @if($gp->project && $gp->project->is_internal)
                                    <span class="badge bg-info-lt text-info text-uppercase fw-bold" style="font-size: 0.6rem;">Internal</span>
                                @elseif($gp->transaction)
                                    <div class="badge bg-green-lt text-green text-uppercase fw-bold" style="font-size: 0.6rem;">Billed</div>
                                    <div class="small fw-bold text-green mt-1">₹{{ number_format($gp->transaction->amount, 0) }}</div>
                                @elseif($gp->client_id)
                                    <span class="badge bg-orange-lt text-orange text-uppercase fw-bold" style="font-size: 0.6rem;">Unbilled Loan</span>
                                @else
                                    <span class="badge bg-gray-lt text-muted text-uppercase fw-bold" style="font-size: 0.6rem;">Draft</span>
                                @endif
                            </td>
                            <td class="text-end">
                                <div class="d-flex justify-content-end gap-1">
                                    <a href="{{ route('gate-passes.show', $gp) }}" class="btn btn-premium-action btn-ghost-primary" title="View Details">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-eye" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"></path><circle cx="12" cy="12" r="2"></circle><path d="M22 12c-2.427 4.173 -5.76 7 -10 7s-7.573 -2.827 -10 -7c2.427 -4.173 5.76 -7 10 -7s7.573 2.827 10 7"></path></svg>
                                    </a>
                                    @if($gp->status == 'completed' && $gp->paid_amount < $gp->total_amount)
                                        <button type="button" class="btn btn-premium-action btn-ghost-success" title="Record Payment"
                                            x-data x-on:click="$dispatch('open-payment-modal', { id: '{{ $gp->id }}', number: '{{ $gp->gate_pass_number }}', balance: '{{ $gp->total_amount - $gp->paid_amount }}' })">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-cash" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"></path><rect x="7" y="9" width="14" height="10" rx="2"></rect><circle cx="14" cy="14" r="2"></circle><path d="M17 9v-2a2 2 0 0 0 -2 -2h-10a2 2 0 0 0 -2 2v6a2 2 0 0 0 2 2h2"></path></svg>
                                        </button>
                                    @endif
                                    <a href="{{ route('gate-passes.edit', $gp) }}" class="btn btn-premium-action btn-ghost-primary" title="Edit">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-pencil" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"></path><path d="M4 20h4l10.5 -10.5a1.5 1.5 0 0 0 -4 -4l-10.5 10.5v4"></path><line x1="13.5" y1="6.5" x2="17.5" y2="10.5"></line></svg>
                                    </a>
                                    <form action="{{ route('gate-passes.destroy', $gp) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this Gate Pass? This will also remove any linked financial transaction.')" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-premium-action btn-ghost-danger" title="Delete">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-trash" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"></path><line x1="4" y1="7" x2="20" y2="7"></line><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line><path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12"></path><path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3"></path></svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-5">
                                <div class="text-muted mb-2">No gate passes recorded yet.</div>
                                <a href="{{ route('gate-passes.create') }}" class="btn btn-sm btn-primary px-4">Create First Pass</a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer bg-transparent border-0">
            {{ $gatePasses->links() }}
        </div>
    </div>

    <!-- ... rest of the file (modals) ... -->
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