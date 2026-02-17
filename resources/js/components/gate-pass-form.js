export default function gatePassForm(config = {}) {
    return {
        // Form Data
        gatePassId: config.gatePassId || null,
        status: config.status || 'pending',
        metalTypeId: config.metalTypeId || '',
        netWeight: config.netWeight || 0,
        transportCost: config.transportCost || 0,
        clientId: config.clientId || '',
        manualVehicleNumber: config.manualVehicleNumber || '',
        isBillable: config.isBillable || false,
        isManualTransportCost: false,

        // Static Data
        vehicles: config.vehicles || [],

        init() {
            // Initial calculations
            this.checkVehicleMultiplier();

            this.$watch('clientId', (value) => {
                if (value) {
                    this.isBillable = true;
                }
            });
        },

        checkVehicleMultiplier() {
            const normalizedInput = this.manualVehicleNumber.toLowerCase().trim();
            const vehicle = this.vehicles.find(v => v.number.toLowerCase() === normalizedInput);

            // This multiplier logic could still be used if there's a base rate somewhere,
            // but for now we'll just keep it minimal.
        },

        calculateTotal() {
            // Placeholder for future multi-field calculations
        }
    };
}
