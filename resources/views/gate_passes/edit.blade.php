<x-tabler-layout>
    <x-slot name="header">
        <div class="row align-items-center">
            <div class="col">
                <h2 class="page-title">Edit Gate Pass</h2>
                <div class="page-subtitle">{{ $gatePass->gate_pass_number }}</div>
            </div>
            <div class="col-auto">
                <div class="btn-list">
                    <a href="{{ route('gate-passes.index') }}" class="btn btn-secondary">
                        Cancel
                    </a>
                    @if(auth()->user()->hasRole('admin'))
                    <form action="{{ route('gate-passes.destroy', $gatePass) }}" method="POST" class="d-inline"
                        onsubmit="return confirm('Are you sure you want to delete this Gate Pass? This action cannot be undone.');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">Delete</button>
                    </form>
                    @endif
                </div>
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
                                        name="vehicle_id" required @change="updateVehicleMultiplier()">
                                        <option value="">Select Vehicle</option>
                                        @foreach($vehicles as $vehicle)
                                            <option value="{{ $vehicle->id }}" data-multiplier="{{ $vehicle->transport_multiplier }}" {{ old('vehicle_id', $gatePass->vehicle_id) == $vehicle->id ? 'selected' : '' }}>
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


                                    <label class="form-label">Remarks</label>
                                    <textarea class="form-control" name="remarks"
                                        rows="2">{{ old('remarks', $gatePass->remarks) }}</textarea>
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
                            <div class="input-icon">
                                <span class="input-icon-addon">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M10 10m-7 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" /><path d="M21 21l-6 -6" /></svg>
                                </span>
                                <input type="text" class="form-control @error('delivery_location') is-invalid @enderror"
                                    name="delivery_location" 
                                    x-model="deliveryLocation" 
                                    @input.debounce.500ms="searchAddress()"
                                    @keydown.escape="showResults = false"
                                    @click.away="showResults = false"
                                    placeholder="Search specific location..."
                                    autocomplete="off">
                                <span class="input-icon-addon" x-show="isSearching" style="display: none;">
                                    <div class="spinner-border spinner-border-sm text-secondary" role="status"></div>
                                </span>
                            </div>
                            
                            <!-- Search Results Dropdown -->
                            <div class="dropdown-menu show w-100" x-show="showResults && searchResults.length > 0" style="display: none; max-height: 200px; overflow-y: auto;">
                                <template x-for="result in searchResults" :key="result.place_id">
                                    <a href="#" class="dropdown-item icon-link" @click.prevent="selectAddress(result)">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-map-pin" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M9 11a3 3 0 1 0 6 0a3 3 0 0 0 -6 0" /><path d="M17.657 16.657l-4.243 4.243a2 2 0 0 1 -2.827 0l-4.244 -4.243a8 8 0 1 1 11.314 0z" /></svg>
                                        <span class="text-truncate" x-text="result.display_name"></span>
                                    </a>
                                </template>
                            </div>

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
                                    <input type="number" step="0.01" class="form-control @error('distance_km') is-invalid @enderror"
                                        name="distance_km" x-model.number="distanceKm" @input="calculateTransportCost()" readonly>
                                    <x-input-error :messages="$errors->get('distance_km')" />
                                    
                                    <div class="mt-2">
                                        <label class="form-check">
                                            <input class="form-check-input" type="checkbox" x-model="isRoundTrip" @change="calculateTransportCost()">
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
                                                <input type="number" step="any" class="form-control form-control-sm" placeholder="Lat" x-model="destLat">
                                            </div>
                                            <div class="col">
                                                <input type="number" step="any" class="form-control form-control-sm" placeholder="Lon" x-model="destLon">
                                            </div>
                                        </div>
                                        <div class="d-flex justify-content-between align-items-center">
                                            <a href="#" class="text-muted small" @click.prevent="validGeo ? getUserLocation() : null" :class="{'disabled': !validGeo}">My Location</a>
                                            <button type="button" class="btn btn-sm btn-outline-primary" @click="fetchDistance()" :disabled="!destLat || !destLon">Calculate</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Transport Cost (₹)</label>
                                    <input type="number" step="0.01" class="form-control @error('transport_cost') is-invalid @enderror"
                                        name="transport_cost" x-model.number="transportCost" readonly>
                                    <div class="form-check mt-2">
                                        <input class="form-check-input" type="checkbox" name="transport_is_billable" value="1" id="billTransport" x-model="isBillable" @change="calculateTotal()">
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
                    distanceKm: {{ old('distance_km', $gatePass->distance_km ?? 0) }},
                    transportCost: {{ old('transport_cost', $gatePass->transport_cost ?? 0) }},
                    transportRate: {{ $transportRate }},
                    vehicleMultiplier: 1,
                    isRoundTrip: false,
                    showCoords: false,
                    destLat: '',
                    destLon: '',
                    destLat: '',
                    destLon: '',
                    validGeo: !!navigator.geolocation,
                    destinations: @json($destinations),
                    destinations: @json($destinations),
                    deliveryLocation: '{{ old('delivery_location', $gatePass->delivery_location) }}',
                    isKnownLocation: false,
                    isBillable: {{ old('transport_is_billable', $gatePass->transport_is_billable ?? 0) ? 'true' : 'false' }},
                    isSearching: false,
                    searchResults: [],
                    showResults: false,

                    init() {
                       if (!this.ratePerTon && this.metalTypeId) {
                           this.updateRate();
                       }
                       if (this.clientId) {
                           this.$nextTick(() => { this.updateBalance(); });
                       }
                       this.updateVehicleMultiplier();
                    },

                    updateVehicleMultiplier() {
                        const select = document.querySelector('select[name="vehicle_id"]');
                        if (select && select.selectedIndex >= 0) {
                            const option = select.options[select.selectedIndex];
                            this.vehicleMultiplier = parseFloat(option.getAttribute('data-multiplier')) || 1;
                            this.calculateTransportCost();
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
                        let transport = 0;
                        
                        if (this.isBillable) {
                            transport = parseFloat(this.transportCost) || 0;
                        }

                        // Total = (Qty * Rate) + Diesel + Transport
                        this.totalAmount = ((qty * rate) + diesel + transport).toFixed(2);
                    },

                    calculateTransportCost() {
                        const distance = parseFloat(this.distanceKm) || 0;
                        const multiplier = parseFloat(this.vehicleMultiplier) || 1;
                        const rt = this.isRoundTrip ? 2 : 1;

                        if (distance > 0) {
                            this.transportCost = (distance * this.transportRate * multiplier * rt).toFixed(2);
                        }
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
                        }
                    },

                    checkLocation() {
                        const match = this.destinations.find(d => d.name.toLowerCase() === this.deliveryLocation.toLowerCase());
                        if (match) {
                            this.isKnownLocation = true;
                            if (this.distanceKm == 0) {
                                this.distanceKm = match.distance_km;
                                this.destLat = match.latitude;
                                this.destLon = match.longitude;
                                this.calculateTransportCost();
                            }
                        } else {
                            this.isKnownLocation = false;
                        }
                    },

                    async searchAddress() {
                        if (this.deliveryLocation.length < 3) {
                            this.searchResults = [];
                            this.showResults = false;
                            this.checkLocation();
                            return;
                        }

                        this.isSearching = true;

                        try {
                            let response = await fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(this.deliveryLocation)}&limit=5`);
                            let data = await response.json();
                            this.searchResults = data;
                            this.showResults = true;
                        } catch (error) {
                            console.error('Error searching address:', error);
                            this.searchResults = [];
                        } finally {
                            this.isSearching = false;
                        }
                    },

                    selectAddress(result) {
                        this.deliveryLocation = result.display_name;
                        this.destLat = result.lat;
                        this.destLon = result.lon;
                        this.showResults = false;
                        this.fetchDistance();
                    }
                }));
            });
        </script>
    @endpush
</x-tabler-layout>