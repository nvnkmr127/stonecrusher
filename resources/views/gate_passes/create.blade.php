<x-tabler-layout>
    <x-slot name="header">
        <div class="row align-items-center">
            <div class="col">
                <h2 class="page-title">New Gate Pass</h2>
                <div class="page-subtitle">Create a new entry for vehicle movement</div>
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
            <form action="{{ route('gate-passes.store') }}" method="POST" x-data="gatePassForm()">
                @csrf
                <x-card>
                    <div class="card-body">
                        <div class="row row-cards">
                            <!-- Basic Info -->
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label required">Gate Pass Number</label>
                                    <input type="text" class="form-control" name="gate_pass_number"
                                        value="{{ old('gate_pass_number', $gpNumber) }}">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label required">Date & Time</label>
                                    <input type="datetime-local"
                                        class="form-control @error('date') is-invalid @enderror" name="date"
                                        value="{{ old('date', now()->format('Y-m-d\TH:i')) }}" required>
                                    <x-input-error :messages="$errors->get('date')" />
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label required">Status</label>
                                    <select class="form-select @error('status') is-invalid @enderror" name="status">
                                        <option value="pending" {{ old('status') == 'pending' ? 'selected' : '' }}>Pending
                                            (In Process)</option>
                                        <option value="completed" {{ old('status') == 'completed' ? 'selected' : '' }}>
                                            Completed</option>
                                        <option value="cancelled" {{ old('status') == 'cancelled' ? 'selected' : '' }}>
                                            Cancelled</option>
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
                                            <option value="{{ $vehicle->id }}" {{ old('vehicle_id') == $vehicle->id ? 'selected' : '' }}>{{ $vehicle->vehicle_number }}</option>
                                        @endforeach
                                    </select>
                                    <x-input-error :messages="$errors->get('vehicle_id')" />
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Driver Name</label>
                                    <input type="text" class="form-control @error('driver_name') is-invalid @enderror"
                                        name="driver_name" value="{{ old('driver_name') }}">
                                    <x-input-error :messages="$errors->get('driver_name')" />
                                </div>
                            </div>

                            <!-- Client & Material -->
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label required">Client</label>
                                    <select class="form-select @error('client_id') is-invalid @enderror"
                                        name="client_id" required>
                                        <option value="">Select Client</option>
                                        @foreach($clients as $client)
                                            <option value="{{ $client->id }}" {{ old('client_id') == $client->id ? 'selected' : '' }}>{{ $client->name }}</option>
                                        @endforeach
                                    </select>
                                    <x-input-error :messages="$errors->get('client_id')" />
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Material / Metal Type</label>
                                    <select class="form-select @error('metal_type_id') is-invalid @enderror"
                                        name="metal_type_id">
                                        <option value="">Select Material</option>
                                        @foreach($metalTypes as $type)
                                            <option value="{{ $type->id }}" {{ old('metal_type_id') == $type->id ? 'selected' : '' }}>{{ $type->name }}</option>
                                        @endforeach
                                    </select>
                                    <x-input-error :messages="$errors->get('metal_type_id')" />
                                </div>
                            </div>

                            <div class="col-12">
                                <hr class="my-4">
                            </div>

                            <!-- Initial Weighing -->
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Gross Weight (Tons)</label>
                                    <input type="number" step="0.01"
                                        class="form-control @error('gross_weight') is-invalid @enderror"
                                        name="gross_weight" x-model.number="grossWeight" @input="calculateNet()">
                                    <x-input-error :messages="$errors->get('gross_weight')" />
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Tare Weight (Tons)</label>
                                    <input type="number" step="0.01"
                                        class="form-control @error('tare_weight') is-invalid @enderror"
                                        name="tare_weight" x-model.number="tareWeight" @input="calculateNet()">
                                    <x-input-error :messages="$errors->get('tare_weight')" />
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Net Weight (Tons)</label>
                                    <input type="number" step="0.01" class="form-control bg-light" name="net_weight"
                                        x-model="netWeight" readonly>
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="mb-3">
                                    <label class="form-label">Remarks</label>
                                    <textarea class="form-control" name="remarks"
                                        rows="2">{{ old('remarks') }}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer text-end">
                        <button type="submit" class="btn btn-primary">Create Gate Pass</button>
                    </div>
                </x-card>
            </form>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('alpine:init', () => {
                Alpine.data('gatePassForm', () => ({
                    grossWeight: {{ old('gross_weight', 0) }},
                    tareWeight: {{ old('tare_weight', 0) }},
                    netWeight: {{ old('net_weight', 0) }},
                    clientId: '{{ old('client_id') }}',
                    clientBalance: 0,

                    init() {
                        if (this.clientId) {
                            this.updateBalance();
                        }
                    },

                    updateBalance() {
                        const select = document.querySelector('select[name="client_id"]');
                        if (select.selectedIndex >= 0) {
                            const option = select.options[select.selectedIndex];
                            this.clientBalance = option.getAttribute('data-balance') || 0;
                        }
                    },

                    calculateNet() {
                        const gross = parseFloat(this.grossWeight) || 0;
                        const tare = parseFloat(this.tareWeight) || 0;

                        if (gross > 0 && tare > 0) {
                            this.netWeight = (gross - tare).toFixed(2);
                        } else {
                            this.netWeight = 0;
                        }
                    }
                }));
            });
        </script>
    @endpush
</x-tabler-layout>