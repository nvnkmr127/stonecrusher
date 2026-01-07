<x-tabler-layout>
    <x-slot name="header">
        <div class="row align-items-center">
            <div class="col">
                <h2 class="page-title">Distance Calculator</h2>
                <div class="page-subtitle">Estimate transport costs</div>
            </div>
            <div class="col-auto">
                {{-- Optional Action Buttons --}}
            </div>
        </div>
    </x-slot>

    <div class="row row-cards" x-data="distanceCalculator()">
        <div class="col-md-6 offset-md-3">
            <x-card>
                <div class="card-header">
                    <h3 class="card-title">Check Transport Cost</h3>
                </div>
                <div class="card-body">
                    <div class="mb-3 position-relative">
                        <label class="form-label">Search Address/Location</label>
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
                            <input type="text" class="form-control" placeholder="Type a location (e.g. Visakhapatnam)"
                                x-model="searchQuery" @input.debounce.500ms="searchAddress()"
                                @keydown.escape="showResults = false" @click.away="showResults = false">
                        </div>

                        <div class="dropdown-menu show w-100" x-show="showResults && searchResults.length > 0"
                            style="max-height: 200px; overflow-y: auto;">
                            <template x-for="result in searchResults" :key="result.place_id">
                                <a href="#" class="dropdown-item icon-link" @click.prevent="selectAddress(result)">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-map-pin"
                                        width="24" height="24" viewBox="0 0 24 24" stroke-width="2"
                                        stroke="currentColor" fill="none" stroke-linecap="round"
                                        stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                        <path d="M9 11a3 3 0 1 0 6 0a3 3 0 0 0 -6 0" />
                                        <path
                                            d="M17.657 16.657l-4.243 4.243a2 2 0 0 1 -2.827 0l-4.244 -4.243a8 8 0 1 1 11.314 0z" />
                                    </svg>
                                    <span class="text-truncate" x-text="result.display_name"></span>
                                </a>
                            </template>
                        </div>
                        <div class="text-secondary mt-1 small" x-show="isSearching">
                            <div class="spinner-border spinner-border-sm me-1" role="status"></div> Searching...
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Delivery Coordinates (Lat, Lon)</label>
                        <div class="row g-2">
                            <div class="col">
                                <input type="number" step="any" class="form-control" placeholder="Latitude"
                                    x-model.number="lat">
                            </div>
                            <div class="col">
                                <input type="number" step="any" class="form-control" placeholder="Longitude"
                                    x-model.number="lon">
                            </div>
                            <div class="col-auto">
                                <button class="btn btn-primary btn-icon" @click="fetchDistance()"
                                    title="Calculate Distance" :disabled="!lat || !lon">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24"
                                        viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                        stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                        <path d="M3 7l6 -3l6 3l6 -3" />
                                        <path d="M3 10l6 -3l6 3l6 -3" />
                                        <path d="M3 13l6 -3l6 3l6 -3" />
                                        <path d="M3 16l6 -3l6 3l6 -3" />
                                        <path d="M9 4l0 13" />
                                        <path d="M15 7l0 13" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                        <small class="form-hint">
                            Crusher Location: {{ $crusherLat }}, {{ $crusherLon }}
                            <a href="#" @click.prevent="getUserLocation()">Get My Location</a>
                        </small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Distance (KM)</label>
                        <input type="number" step="0.01" class="form-control" x-model.number="distance"
                            @input="calculate()">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Vehicle Type (Optional)</label>
                        <select class="form-select" x-model="vehicleMultiplier" @change="calculate()">
                            <option value="1">Standard (1.0x)</option>
                            @foreach($vehicles as $vehicle)
                                <option value="{{ $vehicle->transport_multiplier }}">{{ $vehicle->registration_number }} -
                                    {{ $vehicle->model }} ({{ $vehicle->transport_multiplier }}x)
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-check">
                            <input class="form-check-input" type="checkbox" x-model="isRoundTrip" @change="calculate()">
                            <span class="form-check-label">Round Trip (2x Distance)</span>
                        </label>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Transport Rate (₹/KM)</label>
                        <div class="row g-2">
                            <div class="col">
                                <input type="number" step="0.01" class="form-control" x-model.number="rate"
                                    @input="calculate()">
                                <small class="form-hint">Default is ₹{{ $defaultRate }}</small>
                            </div>
                            <div class="col-auto">
                                <button class="btn btn-icon" @click="resetRate()" title="Reset to Default">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24"
                                        viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                        stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                        <path d="M20 11a8.1 8.1 0 0 0 -15.5 -2m-.5 -4v4h4" />
                                        <path d="M4 13a8.1 8.1 0 0 0 15.5 2m.5 4v-4h-4" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="hr-text">Result</div>

                    <div class="mb-3">
                        <label class="form-label">Estimated Transport Cost</label>
                        <div class="form-control-plaintext text-center fs-1 fw-bold">
                            ₹<span x-text="cost"></span>
                        </div>
                    </div>
                </div>
            </x-card>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('alpine:init', () => {
                Alpine.data('distanceCalculator', () => ({
                    lat: '',
                    lon: '',
                    distance: 0,
                    rate: {{ $defaultRate }},
                    defaultRate: {{ $defaultRate }},
                    cost: 0,
                    vehicleMultiplier: 1,
                    isRoundTrip: false,

                    searchQuery: '',
                    searchResults: [],
                    showResults: false,
                    isSearching: false,

                    init() {
                        this.calculate();
                    },

                    calculate() {
                        let d = parseFloat(this.distance) || 0;
                        let r = parseFloat(this.rate) || 0;
                        let vm = parseFloat(this.vehicleMultiplier) || 1;
                        let rt = this.isRoundTrip ? 2 : 1;

                        this.cost = (d * r * vm * rt).toFixed(2);
                    },

                    resetRate() {
                        this.rate = this.defaultRate;
                        this.calculate();
                    },

                    async fetchDistance() {
                        if (!this.lat || !this.lon) return;

                        try {
                            let response = await fetch(`{{ route('gate-passes.calculator') }}?lat=${this.lat}&lon=${this.lon}&json=1`, {
                                headers: {
                                    'Accept': 'application/json',
                                    'X-Requested-With': 'XMLHttpRequest'
                                }
                            });
                            let data = await response.json();

                            this.distance = data.distance;
                            this.cost = data.cost;
                            // Optional: update rate if server returns it, but we respect user override or current input
                            this.calculate();
                        } catch (error) {
                            console.error('Error fetching distance:', error);
                            alert('Failed to calculate distance.');
                        }
                    },

                    getUserLocation() {
                        if (navigator.geolocation) {
                            navigator.geolocation.getCurrentPosition((position) => {
                                this.lat = position.coords.latitude;
                                this.lon = position.coords.longitude;
                                this.fetchDistance();
                                this.searchQuery = "My Location";
                            });
                        } else {
                            alert("Geolocation is not supported by this browser.");
                        }
                    },

                    async searchAddress() {
                        if (this.searchQuery.length < 3) {
                            this.searchResults = [];
                            this.showResults = false;
                            return;
                        }

                        this.isSearching = true;

                        try {
                            // Using Nominatim API (OpenStreetMap)
                            let response = await fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(this.searchQuery)}&limit=5`);
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
                        this.lat = result.lat;
                        this.lon = result.lon;
                        this.searchQuery = result.display_name;
                        this.showResults = false;
                        this.fetchDistance();
                    }
                }));
            });
        </script>
    @endpush
</x-tabler-layout>