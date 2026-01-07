<x-tabler-layout>
    <x-slot name="header">
        <div class="row align-items-center">
            <div class="col">
                <div class="mb-1">
                    <x-breadcrumb>
                        <x-breadcrumb-item href="{{ route('dashboard') }}">Dashboard</x-breadcrumb-item>
                        <x-breadcrumb-item href="{{ route('gate-passes.index') }}">Gate Passes</x-breadcrumb-item>
                        <x-breadcrumb-item active>Create</x-breadcrumb-item>
                    </x-breadcrumb>
                </div>
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
                                        name="vehicle_id" required @change="updateVehicleMultiplier()">
                                        <option value="">Select Vehicle</option>
                                        @foreach($vehicles as $vehicle)
                                            <option value="{{ $vehicle->id }}"
                                                data-multiplier="{{ $vehicle->transport_multiplier }}" {{ old('vehicle_id') == $vehicle->id ? 'selected' : '' }}>
                                                {{ $vehicle->vehicle_number }}
                                            </option>
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

                            <label class="form-label">Remarks</label>
                            <textarea class="form-control" name="remarks" rows="2">{{ old('remarks') }}</textarea>
                        </div>
                    </div>

                    <div class="col-12">
                        <hr class="my-4">
                    </div>

                    <!-- Transport Details -->
                    <div class="col-12">
                        <h3 class="card-title">Delivery & Transport</h3>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label">Delivery Location</label>
                            <input type="text" class="form-control @error('delivery_location') is-invalid @enderror"
                                name="delivery_location" list="destinationsList" x-model="deliveryLocation"
                                @input="checkLocation()">
                            <datalist id="destinationsList">
                                <template x-for="dest in destinations" :key="dest.id">
                                    <option :value="dest.name"></option>
                                </template>
                            </datalist>
                            <x-input-error :messages="$errors->get('delivery_location')" />

                            <div class="mt-2" x-show="deliveryLocation && !isKnownLocation">
                                <label class="form-check">
                                    <input class="form-check-input" type="checkbox" name="save_location" value="1">
                                    <span class="form-check-label">Save this location for future use</span>
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label">Distance (KM)</label>
                            <input type="number" step="0.01"
                                class="form-control @error('distance_km') is-invalid @enderror" name="distance_km"
                                x-model.number="distanceKm" @input="calculateTransportCost()" readonly>
                            <x-input-error :messages="$errors->get('distance_km')" />

                            <div class="mt-2">
                                <label class="form-check">
                                    <input class="form-check-input" type="checkbox" x-model="isRoundTrip"
                                        @change="calculateTransportCost()">
                                    <span class="form-check-label">Round Trip? (2x)</span>
                                </label>
                            </div>

                            <div class="mt-2 text-end">
                                <a href="#" class="small" @click.prevent="showCoords = !showCoords">Use Coordinates</a>
                            </div>
                            <div x-show="showCoords" style="display: none;" class="mt-2 p-2 border rounded bg-light">
                                <small class="d-block mb-1 fw-bold">From: {{ $crusherLat }}, {{ $crusherLon }}</small>
                                <div class="row g-2 mb-2">
                                    <div class="col">
                                        <input type="number" step="any" class="form-control form-control-sm"
                                            placeholder="Lat" x-model="destLat">
                                    </div>
                                    <div class="col">
                                        <input type="number" step="any" class="form-control form-control-sm"
                                            placeholder="Lon" x-model="destLon">
                                    </div>
                                </div>
                                <div class="d-flex justify-content-between align-items-center">
                                    <a href="#" class="text-muted small"
                                        @click.prevent="validGeo ? getUserLocation() : null"
                                        :class="{'disabled': !validGeo}">My Location</a>
                                    <button type="button" class="btn btn-sm btn-outline-primary"
                                        @click="fetchDistance()" :disabled="!destLat || !destLon || isLoading">
                                        <span x-show="isLoading" class="spinner-border spinner-border-sm" role="status"
                                            aria-hidden="true"></span>
                                        <span x-show="!isLoading">Calculate</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label">Transport Cost (₹)</label>
                            <input type="number" step="0.01"
                                class="form-control @error('transport_cost') is-invalid @enderror" name="transport_cost"
                                x-model.number="transportCost" readonly>
                            <div class="form-check mt-2">
                                <input class="form-check-input" type="checkbox" name="transport_is_billable" value="1"
                                    id="billTransport" x-model="isBillable" @change="calculateTotal()">
                                <label class="form-check-label" for="billTransport">
                                    Bill Transport to Client?
                                </label>
                            </div>
                            <small class="form-hint">Auto-calculated: Distance * ₹{{ $transportRate }}/km</small>
                            <x-input-error :messages="$errors->get('transport_cost')" />
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
                    distanceKm: {{ old('distance_km', 0) }},
                    transportCost: {{ old('transport_cost', 0) }},
                    transportRate: {{ $transportRate }},
                    vehicleMultiplier: 1,
                    isRoundTrip: {{ $defaultRoundTrip ? 'true' : 'false' }},
                    showCoords: false,
                    destLat: '',
                    destLon: '',
                    validGeo: !!navigator.geolocation,
                    destinations: @json($destinations),
                    deliveryLocation: '{{ old('delivery_location') }}',
                    isKnownLocation: false,
                    isBillable: false,
                    isLoading: false,

                    init() {
                        if (this.clientId) {
                            this.updateBalance();
                        }
                        this.updateVehicleMultiplier();
                    },

                    updateVehicleMultiplier() {
                        const select = document.querySelector('select[name="vehicle_id"]');
                        if (select.selectedIndex >= 0) {
                            const option = select.options[select.selectedIndex];
                            this.vehicleMultiplier = parseFloat(option.getAttribute('data-multiplier')) || 1;
                            this.calculateTransportCost();
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
                    },

                    calculateTransportCost() {
                        const distance = parseFloat(this.distanceKm) || 0;
                        const multiplier = parseFloat(this.vehicleMultiplier) || 1;
                        const rt = this.isRoundTrip ? 2 : 1;

                        if (distance > 0) {
                            this.transportCost = (distance * this.transportRate * multiplier * rt).toFixed(2);
                            this.calculateTotal();
                        }
                    },

                    calculateTotal() {
                        const qty = parseFloat(this.loadingQty) > 0 ? parseFloat(this.loadingQty) : (parseFloat(this.netWeight) || 0);
                        const rate = parseFloat(this.ratePerTon) || 0;
                        const diesel = parseFloat(this.dieselAmount) || 0;
                        let transport = 0;

                        if (this.isBillable) {
                            transport = parseFloat(this.transportCost) || 0;
                        }

                        // Total = (Qty * Rate) + Diesel + Transport
                        this.totalAmount = ((qty * rate) + diesel + transport).toFixed(2);
                    },

                    getUserLocation() {
                        if (navigator.geolocation) {
                            navigator.geolocation.getCurrentPosition((position) => {
                                this.destLat = position.coords.latitude;
                                this.destLon = position.coords.longitude;
                            });
                        }
                    },

                    async fetchDistance() {
                        if (!this.destLat || !this.destLon) return;

                        try {
                            this.isLoading = true;
                            let response = await fetch(`{{ route('gate-passes.calculator') }}?lat=${this.destLat}&lon=${this.destLon}&json=1`, {
                                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
                            });
                            let data = await response.json();

                            this.distanceKm = data.distance;
                            this.calculateTransportCost();
                            this.showCoords = false;
                        } catch (error) {
                            console.error('Error:', error);
                            alert('Calculation failed');
                        } finally {
                            this.isLoading = false;
                        }
                    },

                    checkLocation() {
                        const match = this.destinations.find(d => d.name.toLowerCase() === this.deliveryLocation.toLowerCase());
                        if (match) {
                            this.isKnownLocation = true;
                            // Only auto-fill if distance is 0 or empty to avoid overwriting user manual input
                            if (this.distanceKm == 0) {
                                this.distanceKm = match.distance_km;
                                this.destLat = match.latitude;
                                this.destLon = match.longitude;
                                this.calculateTransportCost();
                            }
                        } else {
                            this.isKnownLocation = false;
                        }
                    }
                }));
            });
        </script>
    @endpush
</x-tabler-layout>