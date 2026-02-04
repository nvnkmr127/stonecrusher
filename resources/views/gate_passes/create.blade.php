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
            <form action="{{ route('gate-passes.store') }}" method="POST" x-data="gatePassForm({
                grossWeight: {{ old('gross_weight', 0) }},
                tareWeight: {{ old('tare_weight', 0) }},
                netWeight: {{ old('net_weight', 0) }},
                clientId: '{{ old('client_id') }}',
                distanceKm: {{ old('distance_km', 0) }},
                transportCost: {{ old('transport_cost', 0) }},
                transportRate: {{ $transportRate }},
                isRoundTrip: {{ $defaultRoundTrip ? 'true' : 'false' }},
                deliveryLocation: '{{ old('delivery_location') }}',
                endpoints: {
                    calculator: '{{ route('gate-passes.calculator') }}',
                    search: '{{ route('gate-passes.search-location') }}'
                },
                destinations: @json($destinations),
                allowedStates: @json(json_decode(\App\Models\Setting::get('allowed_states', '[]'))) || []
            })">
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
                            <div class="input-icon">
                                <span class="input-icon-addon">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24"
                                        viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                        stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                        <path d="M10 10m-7 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" />
                                        <path d="M21 21l-6 -6" />
                                    </svg>
                                </span>
                                <input type="text" class="form-control @error('delivery_location') is-invalid @enderror"
                                    name="delivery_location" x-model="deliveryLocation"
                                    @input.debounce.500ms="searchAddress()" @keydown.escape="showResults = false"
                                    @click.away="showResults = false" placeholder="Search specific location..."
                                    autocomplete="off">
                                <span class="input-icon-addon" x-show="isSearching" style="display: none;">
                                    <div class="spinner-border spinner-border-sm text-secondary" role="status"></div>
                                </span>
                            </div>

                            <!-- Search Results Dropdown -->
                            <div class="dropdown-menu show w-100" x-show="showResults && searchResults.length > 0"
                                style="display: none; max-height: 200px; overflow-y: auto;">
                                <template x-for="result in searchResults" :key="result.place_id">
                                    <a href="#" class="dropdown-item icon-link" @click.prevent="selectAddress(result)">
                                        <svg xmlns="http://www.w3.org/2000/svg"
                                            class="icon icon-tabler icon-tabler-map-pin" width="24" height="24"
                                            viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                            stroke-linecap="round" stroke-linejoin="round">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                            <path d="M9 11a3 3 0 1 0 6 0a3 3 0 0 0 -6 0" />
                                            <path
                                                d="M17.657 16.657l-4.243 4.243a2 2 0 0 1 -2.827 0l-4.244 -4.243a8 8 0 1 1 11.314 0z" />
                                        </svg>
                                        <span class="text-truncate" x-text="result.display_name"></span>
                                    </a>
                                </template>
                            </div>

                            <!-- User Saved Destinations Datalist (Fallback) -->
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
</x-tabler-layout>