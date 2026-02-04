export default function gatePassForm(config = {}) {
    return {
        // Form Data
        gatePassId: config.gatePassId || null,
        status: config.status || 'pending',
        metalTypeId: config.metalTypeId || '',
        grossWeight: config.grossWeight || 0,
        tareWeight: config.tareWeight || 0,
        netWeight: config.netWeight || 0,
        loadingQty: config.loadingQty || 0,
        ratePerTon: config.ratePerTon || 0,
        dieselAmount: config.dieselAmount || 0,
        advanceAmount: config.advanceAmount || 0,
        totalAmount: config.totalAmount || 0,
        clientId: config.clientId || '',
        driverName: config.driverName || '',
        vehicleId: config.vehicleId || '',

        // Settings & Meta
        clientBalance: 0,
        distanceKm: config.distanceKm || 0,
        transportCost: config.transportCost || 0,
        transportRate: config.transportRate || 0,
        vehicleMultiplier: 1,
        isRoundTrip: config.isRoundTrip || false,
        isBillable: config.isBillable || false,
        deliveryLocation: config.deliveryLocation || '',

        // UI State
        showCoords: false,
        destLat: config.destLat || '',
        destLon: config.destLon || '',
        isLoading: false,
        isSearching: false,
        searchResults: [],
        showResults: false,
        isKnownLocation: false,
        validGeo: !!navigator.geolocation,

        // Static Data
        destinations: config.destinations || [],
        allowedStates: config.allowedStates || [],
        endpoints: {
            calculator: config.endpoints?.calculator || '/gate-passes/calculator',
            search: config.endpoints?.search || '/gate-passes/search-location',
        },

        init() {
            // Restore state if editing
            if (!this.ratePerTon && this.metalTypeId) {
                this.updateRate();
            }

            if (this.clientId) {
                this.$nextTick(() => { this.updateBalance(); });
            }

            // Initial calculations
            this.updateVehicleMultiplier();

            // Watchers for complex dependencies could go here or remain as @change handlers
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
            if (select && select.selectedIndex >= 0) {
                const option = select.options[select.selectedIndex];
                const price = parseFloat(option.getAttribute('data-price')) || 0;
                this.ratePerTon = price;
                this.calculateTotal();
            }
        },

        calculateNet() {
            const gross = parseFloat(this.grossWeight) || 0;
            const tare = parseFloat(this.tareWeight) || 0;

            if (gross > 0) {
                // Ensure tare doesn't exceed gross if logical, though standard allows negative net in some weird edge cases, usually block it.
                // For now, simple calc.
                this.netWeight = Math.max(0, gross - tare).toFixed(2);
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
                this.calculateTotal(); // Re-calc total as transport cost changed
            } else {
                this.transportCost = 0;
                this.calculateTotal();
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
                this.isLoading = true;
                const url = `${this.endpoints.calculator}?lat=${this.destLat}&lon=${this.destLon}&json=1`;

                let response = await fetch(url, {
                    headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
                });

                if (!response.ok) throw new Error('Network response was not ok');

                let data = await response.json();

                this.distanceKm = data.distance;
                this.calculateTransportCost();
                this.showCoords = false;
            } catch (error) {
                console.error('Error:', error);
                alert('Distance calculation failed. Please try again.');
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
                const url = `${this.endpoints.search}?q=${encodeURIComponent(this.deliveryLocation)}`;

                let response = await fetch(url, {
                    headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
                });

                let data = await response.json();

                // Filter by allowed states if configured
                if (this.allowedStates.length > 0) {
                    data = data.filter(result => {
                        return this.allowedStates.includes(result.address.state);
                    });
                }

                this.searchResults = data.slice(0, 5);
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
    };
}
