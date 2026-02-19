export default function gatePassForm(config = {}) {
    return {
        // Form Data
        gatePassId: config.gatePassId || null,
        status: config.status || 'pending',
        metalTypeId: config.metalTypeId || '',
        netWeight: config.netWeight || 0,
        transportCost: config.transportCost || 0,
        clientId: config.clientId || '',
        projectId: config.projectId || '',
        manualCustomerName: config.manualCustomerName || '',
        destinationType: config.destinationType || 'registered', // registered, regular, internal
        manualVehicleNumber: config.manualVehicleNumber || '',
        isBillable: config.isBillable || false,
        isManualTransportCost: false,

        // Static Data
        vehicles: config.vehicles || [],

        init() {
            // Initial calculations
            this.checkVehicleMultiplier();

            this.$watch('clientId', (value) => {
                if (value && this.destinationType === 'registered') {
                    this.isBillable = true;
                }
            });

            this.$watch('manualCustomerName', (value) => {
                if (value && this.destinationType === 'regular') {
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
                // Internal is always false
                if (value === 'internal') {
                    this.isBillable = false;
                }
            });
        },

        checkVehicleMultiplier() {
            if (!this.manualVehicleNumber) return;
            const normalizedInput = this.manualVehicleNumber.toLowerCase().trim();
            const vehicle = this.vehicles.find(v => v.number && v.number.toLowerCase() === normalizedInput);
            // Multiplier logic can go here if needed
        },

        onDestinationTypeChange() {
            // Reset fields when switching
            this.clientId = '';
            this.projectId = '';
            this.manualCustomerName = '';

            if (this.destinationType === 'internal') {
                this.isBillable = false;
            } else {
                this.isBillable = false;
            }
        },

        onProjectChange(e) {
            if (!this.projectId) return;

            // Try to find the select that triggered this
            const select = e ? e.target : null;
            if (!select) return;

            const option = select.options[select.selectedIndex];

            if (option) {
                const clientId = option.getAttribute('data-client-id');
                const isInternal = option.getAttribute('data-is-internal') === '1';

                if (this.destinationType === 'registered' && clientId) {
                    this.clientId = clientId;
                }

                if (isInternal || this.destinationType === 'internal') {
                    this.isBillable = false;
                }
            }
        },

        calculateTotal() {
            // UI feedback
        }
    };
}
