export default function gatePassForm(config = {}) {
    return {
        // Form Data
        gatePassId: config.gatePassId || null,
        status: config.status || 'pending',
        metalTypeId: config.metalTypeId || '',
        netWeight: config.netWeight || 0,
        transportCost: config.transportCost || 0,
        dieselAmount: config.dieselAmount || 0,
        ratePerTon: config.ratePerTon || 0,
        totalAmount: 0,
        activityType: config.activityType || 'Sales',
        sourceUnitId: config.sourceUnitId || 2,
        destinationUnitId: config.destinationUnitId || 3,
        trips: config.trips || 1,
        clientId: config.clientId || '',
        projectId: config.projectId || '',
        manualCustomerName: config.manualCustomerName || '',
        destinationType: config.destinationType || 'registered', // registered, regular
        manualVehicleNumber: config.manualVehicleNumber || '',
        isBillable: config.isBillable || false,
        isManualTransportCost: false,

        // Static Data
        vehicles: config.vehicles || [],

        init() {
            // Initial calculations
            this.checkVehicleMultiplier();
            this.calculateTotal();

            this.$watch('clientId', (value) => {
                if (value && this.activityType === 'Sales') {
                    this.isBillable = true;
                }
            });

            this.$watch('manualCustomerName', (value) => {
                if (value && this.activityType === 'Sales') {
                    this.isBillable = true;
                }
            });

            this.$watch('destinationType', (value) => {
                // If switching to registered, check if clientId is set
                if (value === 'registered' && this.clientId) {
                    this.isBillable = true;
                }
                // If switching to regular, check if name is set
                if (value === 'regular' && this.manualCustomerName) {
                    this.isBillable = true;
                }
            });
        },

        checkVehicleMultiplier() {
            if (!this.manualVehicleNumber) return;
            const normalizedInput = this.manualVehicleNumber.toLowerCase().trim();
            const vehicle = this.vehicles.find(v => v.number && v.number.toLowerCase() === normalizedInput);

            // If vehicle has a preferred unit, we could auto-set source unit
            if (vehicle && vehicle.unit_id && this.activityType === 'Sales') {
                // this.sourceUnitId = vehicle.unit_id;
            }
        },

        onUsageChange() {
            // Mapping Destination Type to Activity & Units
            if (this.destinationType === 'transfer') {
                this.activityType = 'Material Transfer';
                this.sourceUnitId = 1;
                this.destinationUnitId = 2;
                this.isBillable = false;
            } else if (this.destinationType === 'internal') {
                this.activityType = 'Internal Movement';
                this.sourceUnitId = 2; // Usually Crusher
                this.destinationUnitId = 3; // External/Internal site
                this.isBillable = false;
            } else {
                // registered or regular (Sales)
                this.activityType = 'Sales';
                this.sourceUnitId = 2; // Crusher
                this.destinationUnitId = 3; // External
                this.isBillable = true;
            }

            // Reset selection fields when switching types
            if (this.destinationType !== 'registered' && this.destinationType !== 'internal') {
                this.clientId = '';
                this.projectId = '';
            }
            if (this.destinationType !== 'regular') {
                this.manualCustomerName = '';
            }
        },

        onDestinationTypeChange() {
            this.onUsageChange();
        },

        onProjectChange(e) {
            if (!this.projectId) return;

            const select = e ? e.target : null;
            if (!select) return;

            const option = select.options[select.selectedIndex];

            if (option) {
                const clientId = option.getAttribute('data-client-id');
                if (this.destinationType === 'registered' && clientId) {
                    this.clientId = clientId;
                }
            }
        },

        calculateTotal() {
            if (this.destinationType === 'transfer' || this.destinationType === 'internal') {
                this.totalAmount = 0;
                return;
            }

            const quantity = parseFloat(this.netWeight) || 0;
            const rate = parseFloat(this.ratePerTon) || 0;
            const diesel = parseFloat(this.dieselAmount) || 0;
            const transport = this.isBillable ? (parseFloat(this.transportCost) || 0) : 0;

            // Base amount from Quantity * Rate
            const baseAmount = quantity * rate;
            
            // Total = Base Amount + Diesel + Transport
            // Note: Diesel is added to the total amount as per user request
            this.totalAmount = (baseAmount + diesel + transport).toFixed(2);
        }
    };
}
