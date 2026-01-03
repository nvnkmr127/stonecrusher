<x-tabler-layout>
    <x-slot name="header">
        <div class="row align-items-center">
            <div class="col">
                <h2 class="page-title">Edit Gate Pass</h2>
                <div class="page-subtitle">{{ $gatePass->gate_pass_number }}</div>
            </div>
            <div class="col-auto">
                <a href="{{ route('gate-passes.index') }}" class="btn btn-secondary">
                    Cancel
                </a>
            </div>
        </div>
    </x-slot>

    <div class="row row-cards">
        <div class="col-12">
            <form action="{{ route('gate-passes.update', $gatePass) }}" method="POST" x-data="gatePassEditForm()">
                @csrf
                @method('PUT')
                <x-card>
                    <div class="card-body">
                        <div class="row row-cards">
                            <!-- Basic Info -->
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label required">Gate Pass Number</label>
                                    <input type="text" class="form-control" name="gate_pass_number"
                                        value="{{ old('gate_pass_number', $gatePass->gate_pass_number) }}" readonly>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label required">Date & Time</label>
                                    <input type="datetime-local"
                                        class="form-control @error('date') is-invalid @enderror" name="date"
                                        value="{{ old('date', $gatePass->date->format('Y-m-d\TH:i')) }}" required>
                                    <x-input-error :messages="$errors->get('date')" />
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label required">Status</label>
                                    <select class="form-select @error('status') is-invalid @enderror" name="status">
                                        <option value="pending" {{ old('status', $gatePass->status) == 'pending' ? 'selected' : '' }}>Pending</option>
                                        <option value="completed" {{ old('status', $gatePass->status) == 'completed' ? 'selected' : '' }}>Completed</option>
                                        <option value="cancelled" {{ old('status', $gatePass->status) == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                    </select>
                                    <x-input-error :messages="$errors->get('status')" />
                                </div>
                            </div>

                            <div class="col-12">
                                <hr class="my-4">
                            </div>

                            <!-- Vehicle & Driver -->
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label required">Vehicle</label>
                                    <select class="form-select @error('vehicle_id') is-invalid @enderror"
                                        name="vehicle_id" required>
                                        <option value="">Select Vehicle</option>
                                        @foreach($vehicles as $vehicle)
                                            <option value="{{ $vehicle->id }}" {{ old('vehicle_id', $gatePass->vehicle_id) == $vehicle->id ? 'selected' : '' }}>
                                                {{ $vehicle->vehicle_number }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <x-input-error :messages="$errors->get('vehicle_id')" />
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label required">Driver Name</label>
                                    <input type="text" class="form-control @error('driver_name') is-invalid @enderror"
                                        name="driver_name" value="{{ old('driver_name', $gatePass->driver_name) }}"
                                        required>
                                    <x-input-error :messages="$errors->get('driver_name')" />
                                </div>
                            </div>

                            <!-- Client & Material -->
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Client</label>
                                    <select class="form-select @error('client_id') is-invalid @enderror"
                                        name="client_id" x-model="clientId" @change="updateBalance()">
                                        <option value="" data-balance="0">Select Client (Optional)</option>
                                        @foreach($clients as $client)
                                            <option value="{{ $client->id }}" 
                                                data-balance="{{ $client->balance }}"
                                                {{ old('client_id', $gatePass->client_id) == $client->id ? 'selected' : '' }}>
                                                {{ $client->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <div class="mt-1" x-show="clientId">
                                        <small class="text-muted">Current Balance: 
                                            <span :class="parseFloat(clientBalance) >= 0 ? 'text-success' : 'text-danger'" 
                                                  x-text="parseFloat(clientBalance).toFixed(2)"></span>
                                        </small>
                                    </div>
                                    <x-input-error :messages="$errors->get('client_id')" />
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label required">Material / Metal Type</label>
                                    <select class="form-select @error('metal_type_id') is-invalid @enderror"
                                        name="metal_type_id" x-model="metalTypeId" @change="updateRate()" required>
                                        <option value="" data-price="0">Select Material</option>
                                        @foreach($metalTypes as $type)
                                            <option value="{{ $type->id }}" data-price="{{ $type->unit_price }}" {{ old('metal_type_id', $gatePass->metal_type_id) == $type->id ? 'selected' : '' }}>{{ $type->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <x-input-error :messages="$errors->get('metal_type_id')" />
                                </div>
                            </div>

                            <div class="col-12">
                                <hr class="my-4">
                            </div>

                            <!-- Weights & Financials -->
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label class="form-label">Loading Qty (CFT)</label>
                                    <input type="number" step="0.01"
                                        class="form-control @error('loading_quantity') is-invalid @enderror"
                                        name="loading_quantity" x-model.number="loadingQty" @input="calculateTotal()"
                                        placeholder="Optional if weighing">
                                    <small class="form-hint">Enter for Volume Sales</small>
                                    <x-input-error :messages="$errors->get('loading_quantity')" />
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label class="form-label">Gross Weight</label>
                                    <input type="number" step="0.01"
                                        class="form-control @error('gross_weight') is-invalid @enderror"
                                        name="gross_weight" x-model.number="grossWeight" @input="calculateNet()"
                                        placeholder="Tons">
                                    <x-input-error :messages="$errors->get('gross_weight')" />
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label class="form-label">Tare Weight</label>
                                    <input type="number" step="0.01"
                                        class="form-control @error('tare_weight') is-invalid @enderror"
                                        name="tare_weight" x-model.number="tareWeight" @input="calculateNet()"
                                        placeholder="Tons">
                                    <x-input-error :messages="$errors->get('tare_weight')" />
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label class="form-label">Net Weight</label>
                                    <input type="number" step="0.01" class="form-control bg-light" name="net_weight"
                                        x-model="netWeight" readonly>
                                </div>
                            </div>

                            <div class="col-12"></div>

                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Rate (Per Unit)</label>
                                    <input type="number" step="0.01"
                                        class="form-control @error('rate_per_ton') is-invalid @enderror"
                                        name="rate_per_ton" x-model.number="ratePerTon" @input="calculateTotal()">
                                    <small class="form-hint">Per Ton or Per CFT</small>
                                    <x-input-error :messages="$errors->get('rate_per_ton')" />
                                </div>
                            </div>

                            <!-- Deductions & Total -->
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Diesel Amount (+)</label>
                                    <input type="number" step="0.01"
                                        class="form-control @error('diesel_amount') is-invalid @enderror"
                                        name="diesel_amount" x-model.number="dieselAmount" @input="calculateTotal()">
                                    <small class="form-hint">Added to Total</small>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Total Amount</label>
                                    <input type="number" step="0.01" class="form-control form-control-lg bg-light"
                                        name="total_amount" x-model="totalAmount" readonly>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Driver Advance (-)</label>
                                    <input type="number" step="0.01"
                                        class="form-control @error('advance_amount') is-invalid @enderror"
                                        name="advance_amount" x-model.number="advanceAmount">
                                    <small class="form-hint">Deducted from Payment (if applicable)</small>
                                </div>
                            </div>


                            <div class="col-12">
                                <div class="mb-3">
                                    <label class="form-label">Remarks</label>
                                    <textarea class="form-control" name="remarks"
                                        rows="2">{{ old('remarks', $gatePass->remarks) }}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer text-end">
                        <button type="submit" class="btn btn-primary">Update Gate Pass</button>
                    </div>
                </x-card>
            </form>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('alpine:init', () => {
                Alpine.data('gatePassEditForm', () => ({
                    metalTypeId: '{{ old('metal_type_id', $gatePass->metal_type_id) }}',
                    grossWeight: {{ old('gross_weight', $gatePass->gross_weight) }},
                    tareWeight: {{ old('tare_weight', $gatePass->tare_weight) }},
                    netWeight: {{ old('net_weight', $gatePass->net_weight) }},
                    loadingQty: {{ old('loading_quantity', $gatePass->loading_quantity ?? 0) }},
                    ratePerTon: {{ old('rate_per_ton', $gatePass->rate_per_ton) }},
                    dieselAmount: {{ old('diesel_amount', $gatePass->diesel_amount) }},
                    advanceAmount: {{ old('advance_amount', $gatePass->advance_amount) }},
                    totalAmount: {{ old('total_amount', $gatePass->total_amount) }},
                    clientId: '{{ old('client_id', $gatePass->client_id) }}',
                    clientBalance: 0,

                    init() {
                       if (!this.ratePerTon && this.metalTypeId) {
                           this.updateRate();
                       }
                       if (this.clientId) {
                           this.$nextTick(() => { this.updateBalance(); });
                       }
                    },

                    updateBalance() {
                        const select = document.querySelector('select[name="client_id"]');
                        if (select && select.selectedIndex >= 0) {
                            const option = select.options[select.selectedIndex];
                            const balance = option.getAttribute('data-balance');
                            this.clientBalance = balance ? parseFloat(balance) : 0;
                        }
                    },

                    updateRate() {
                        const select = document.querySelector('select[name="metal_type_id"]');
                        const option = select.options[select.selectedIndex];
                        const price = parseFloat(option.getAttribute('data-price')) || 0;
                        this.ratePerTon = price;
                        this.calculateTotal();
                    },

                    calculateNet() {
                        const gross = parseFloat(this.grossWeight) || 0;
                        const tare = parseFloat(this.tareWeight) || 0;
                        if (gross > 0) {
                            this.netWeight = (gross - tare).toFixed(2);
                        } else {
                            this.netWeight = 0;
                        }
                        this.calculateTotal();
                    },

                    calculateTotal() {
                        const qty = parseFloat(this.loadingQty) > 0 ? parseFloat(this.loadingQty) : (parseFloat(this.netWeight) || 0);
                        const rate = parseFloat(this.ratePerTon) || 0;
                        const diesel = parseFloat(this.dieselAmount) || 0;

                        // Total = (Qty * Rate) + Diesel
                        this.totalAmount = ((qty * rate) + diesel).toFixed(2);
                    }
                }));
            });
        </script>
    @endpush
</x-tabler-layout>