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
        <div class="col-lg-10 mx-auto">
            <form action="{{ route('gate-passes.store') }}" method="POST" x-data="gatePassCreateForm({
                grossWeight: {{ (float) old('gross_weight', 0) }},
                tareWeight: {{ (float) old('tare_weight', 0) }},
                netWeight: {{ (float) old('net_weight', 0) }},
                activityType: '{{ old('activity_type', 'Sales') }}',
                sourceUnitId: {{ old('source_unit_id', 2) }},
                destinationUnitId: {{ old('destination_unit_id', 3) }},
                trips: {{ old('trips', 1) }},
                clientId: {{ Illuminate\Support\Js::from(old('client_id', '')) }},
                projectId: {{ Illuminate\Support\Js::from(old('project_id', '')) }},
                manualCustomerName: {{ Illuminate\Support\Js::from(old('manual_customer_name', '')) }},
                manualVehicleNumber: {{ Illuminate\Support\Js::from(old('manual_vehicle_number', '')) }},
                destinationType: {{ Illuminate\Support\Js::from(old('destination_type') ?: (old('manual_customer_name') ? 'regular' : (old('activity_type') == 'Material Transfer' ? 'transfer' : (old('project_id') && $projects->where('id', old('project_id'))->where('is_internal', true)->count() > 0 ? 'internal' : 'registered')))) }},
                dieselAmount: {{ (float) old('diesel_amount', 0) }},
                ratePerTon: {{ (float) old('rate_per_ton', 0) }},
                totalAmount: 0,
                isBillable: {{ (old('client_id') || old('manual_customer_name') || old('transport_is_billable')) ? 'true' : 'false' }},
                isManualVehicle: true,
                vehicles: {{ Illuminate\Support\Js::from($vehicles->map(fn($v) => ['number' => $v->registration_number, 'multiplier' => (float) $v->transport_multiplier, 'unit_id' => $v->operational_unit_id])) }}
            })">
                @csrf
                <x-card>
                    <div class="card-body p-4">
                        @if ($errors->any())
                            <div class="alert alert-danger alert-dismissible" role="alert">
                                <div class="d-flex">
                                    <div>
                                        <svg xmlns="http://www.w3.org/2000/svg"
                                            class="alert-icon icon icon-tabler icon-tabler-alert-circle me-2" width="24"
                                            height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
                                            fill="none" stroke-linecap="round" stroke-linejoin="round">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                            <path d="M3 12a9 9 0 1 0 18 0a9 9 0 1 0 -18 0"></path>
                                            <path d="M12 8v4"></path>
                                            <path d="M12 16h.01"></path>
                                        </svg>
                                    </div>
                                    <div>
                                        <h4 class="alert-title">Please correct the following errors:</h4>
                                        <ul class="mb-0">
                                            @foreach ($errors->all() as $error)
                                                <li>{{ $error }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>
                                <a href="#" class="btn-close" data-bs-dismiss="alert" aria-label="close"></a>
                            </div>
                        @endif

                        <!-- Section 1: Trip & Vehicle -->
                        <div class="mb-4">
                            <h3 class="card-title text-primary border-bottom pb-2 mb-3">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-id me-2"
                                    width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
                                    fill="none" stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                    <path
                                        d="M3 4m0 3a3 3 0 0 1 3 -3h12a3 3 0 0 1 3 3v10a3 3 0 0 1 -3 3h-12a3 3 0 0 1 -3 -3z">
                                    </path>
                                    <path
                                        d="M9 10m0 1a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v4a1 1 0 0 1 -1 1h-4a1 1 0 0 1 -1 -1z">
                                    </path>
                                    <path d="M3 8h18"></path>
                                </svg>
                                1. Basic Identification
                            </h3>
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label required">Gate Pass Number</label>
                                    <input type="text" class="form-control fw-bold bg-light" name="gate_pass_number"
                                        value="{{ old('gate_pass_number', $gpNumber) }}" readonly>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label required">Date & Time</label>
                                    <input type="datetime-local"
                                        class="form-control @error('date') is-invalid @enderror" name="date"
                                        value="{{ old('date', now()->format('Y-m-d\TH:i')) }}" required>
                                    <x-input-error :messages="$errors->get('date')" />
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label required">Vehicle Number</label>
                                    <div class="input-icon">
                                        <span class="input-icon-addon">
                                            <svg xmlns="http://www.w3.org/2000/svg"
                                                class="icon icon-tabler icon-tabler-truck" width="24" height="24"
                                                viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                                stroke-linecap="round" stroke-linejoin="round">
                                                <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                                <path d="M7 17m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0"></path>
                                                <path d="M17 17m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0"></path>
                                                <path
                                                    d="M5 17h-2v-11a1 1 0 0 1 1 -1h9v12m-4 0h6m4 0h2v-6h-8m0 -5h5l3 5">
                                                </path>
                                            </svg>
                                        </span>
                                        <input type="text"
                                            class="form-control text-uppercase fw-medium @if($errors->has('manual_vehicle_number') || $errors->has('vehicle_id')) is-invalid @endif"
                                            name="manual_vehicle_number" list="vehicleList"
                                            x-model="manualVehicleNumber" @input="checkVehicleMultiplier()"
                                            placeholder="ABC-1234" required autocomplete="off">
                                    </div>
                                    <datalist id="vehicleList">
                                        @foreach($vehicles as $vehicle)
                                            <option value="{{ $vehicle->registration_number }}">
                                                {{ $vehicle->registration_number }}
                                            </option>
                                        @endforeach
                                    </datalist>
                                    <x-input-error :messages="$errors->get('manual_vehicle_number')" />
                                    <x-input-error :messages="$errors->get('vehicle_id')" />
                                </div>
                            </div>
                        </div>

                        <!-- Section 2: Destination & Usage Selection -->
                        <div class="mb-4">
                            <h3 class="card-title text-primary border-bottom pb-2 mb-3">
                                <svg xmlns="http://www.w3.org/2000/svg"
                                    class="icon icon-tabler icon-tabler-truck-delivery me-2" width="24" height="24"
                                    viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                    stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                    <path d="M7 17m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0"></path>
                                    <path d="M17 17m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0"></path>
                                    <path d="M5 17h-2v-4m-1 -8h11v12m-4 0h6m4 0h2v-6h-8m0 -5h5l3 5"></path>
                                    <path d="M3 9l4 0"></path>
                                </svg>
                                2. Movement & Customer Details
                            </h3>
                            <div class="mb-3">
                                <label class="form-label fw-bold mb-3">Selling To / Movement Type</label>
                                <div
                                    class="form-selectgroup form-selectgroup-boxes d-flex flex-column flex-md-row gap-2">
                                    <label class="form-selectgroup-item flex-fill">
                                        <input type="radio" name="destination_type" value="registered"
                                            class="form-selectgroup-input" x-model="destinationType"
                                            @change="onUsageChange()">
                                        <span class="form-selectgroup-label d-flex align-items-center p-3">
                                            <span class="me-3"><span class="form-selectgroup-check"></span></span>
                                            <span class="form-selectgroup-label-content text-start">
                                                <span class="form-selectgroup-title fw-bold">Selling to Client</span>
                                                <span class="text-muted small">Registered account</span>
                                            </span>
                                        </span>
                                    </label>
                                    <label class="form-selectgroup-item flex-fill">
                                        <input type="radio" name="destination_type" value="regular"
                                            class="form-selectgroup-input" x-model="destinationType"
                                            @change="onUsageChange()">
                                        <span class="form-selectgroup-label d-flex align-items-center p-3">
                                            <span class="me-3"><span class="form-selectgroup-check"></span></span>
                                            <span class="form-selectgroup-label-content text-start">
                                                <span class="form-selectgroup-title fw-bold">Regular Sale</span>
                                                <span class="text-muted small">Enter name manually</span>
                                            </span>
                                        </span>
                                    </label>
                                    <label class="form-selectgroup-item flex-fill">
                                        <input type="radio" name="destination_type" value="internal"
                                            class="form-selectgroup-input" x-model="destinationType"
                                            @change="onUsageChange()">
                                        <span class="form-selectgroup-label d-flex align-items-center p-3">
                                            <span class="me-3"><span class="form-selectgroup-check"></span></span>
                                            <span class="form-selectgroup-label-content text-start">
                                                <span class="form-selectgroup-title fw-bold">Internal Project</span>
                                                <span class="text-muted small">Own project usage</span>
                                            </span>
                                        </span>
                                    </label>
                                    <label class="form-selectgroup-item flex-fill">
                                        <input type="radio" name="destination_type" value="transfer"
                                            class="form-selectgroup-input" x-model="destinationType"
                                            @change="onUsageChange()">
                                        <span class="form-selectgroup-label d-flex align-items-center p-3">
                                            <span class="me-3"><span class="form-selectgroup-check"></span></span>
                                            <span class="form-selectgroup-label-content text-start">
                                                <span class="form-selectgroup-title fw-bold">Transfer</span>
                                                <span class="text-muted small">Quarry to Crusher</span>
                                            </span>
                                        </span>
                                    </label>
                                </div>
                            </div>

                            <input type="hidden" name="activity_type" :value="activityType">
                            <input type="hidden" name="source_unit_id" x-model="sourceUnitId">
                            <input type="hidden" name="destination_unit_id" x-model="destinationUnitId">

                            <!-- Conditional Party Selectors (Wrapped in a subtle box) -->
                            <div class="bg-light-subtle rounded-3 p-3 mt-3 border border-dashed"
                                x-show="destinationType !== 'transfer'" x-transition>
                                <div class="row g-3">
                                    <!-- Registered Mode Fields -->
                                    <div class="col-md-6" x-show="destinationType === 'registered'">
                                        <label class="form-label required">Client / Customer</label>
                                        <select class="form-select @error('client_id') is-invalid @enderror"
                                            name="client_id" x-model="clientId"
                                            :required="destinationType === 'registered'">
                                            <option value="">Select Client</option>
                                            @foreach($clients as $client)
                                                <option value="{{ $client->id }}">{{ $client->name }}</option>
                                            @endforeach
                                        </select>
                                        <x-input-error :messages="$errors->get('client_id')" />
                                    </div>
                                    <div class="col-md-6" x-show="destinationType === 'registered'">
                                        <label class="form-label">Project (Optional)</label>
                                        <select class="form-select @error('project_id') is-invalid @enderror"
                                            name="project_id" x-model="projectId" @change="onProjectChange($event)">
                                            <option value="">Select Project</option>
                                            @foreach($projects->where('is_internal', false) as $project)
                                                <option value="{{ $project->id }}"
                                                    data-client-id="{{ $project->client_id }}">
                                                    {{ $project->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <x-input-error :messages="$errors->get('project_id')" />
                                    </div>

                                    <!-- Regular Sale Mode Fields -->
                                    <div class="col-12" x-show="destinationType === 'regular'">
                                        <label class="form-label required">Customer Name (Manual)</label>
                                        <input type="text"
                                            class="form-control @error('manual_customer_name') is-invalid @enderror"
                                            name="manual_customer_name" x-model="manualCustomerName"
                                            placeholder="Enter Customer Name" :required="destinationType === 'regular'">
                                        <x-input-error :messages="$errors->get('manual_customer_name')" />
                                    </div>

                                    <!-- Internal Mode Fields -->
                                    <div class="col-12" x-show="destinationType === 'internal'">
                                        <label class="form-label required">Internal Project</label>
                                        <select class="form-select @error('project_id') is-invalid @enderror"
                                            name="project_id" x-model="projectId" @change="onProjectChange($event)"
                                            :required="destinationType === 'internal'">
                                            <option value="">Select Internal Project</option>
                                            @foreach($projects->where('is_internal', true) as $project)
                                                <option value="{{ $project->id }}" data-is-internal="1">
                                                    {{ $project->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <x-input-error :messages="$errors->get('project_id')" />
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Section 3: Material & Quantity -->
                        <div class="mb-4">
                            <h3 class="card-title text-primary border-bottom pb-2 mb-3">
                                <svg xmlns="http://www.w3.org/2000/svg"
                                    class="icon icon-tabler icon-tabler-package me-2" width="24" height="24"
                                    viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                    stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                    <path d="M12 3l8 4.5l0 9l-8 4.5l-8 -4.5l0 -9l8 -4.5"></path>
                                    <path d="M12 12l8 -4.5"></path>
                                    <path d="M12 12l0 9"></path>
                                    <path d="M12 12l-8 -4.5"></path>
                                    <path d="M16 5.25l-8 4.5"></path>
                                </svg>
                                3. Loading Details
                            </h3>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label required">Material Type</label>
                                    <select class="form-select @error('metal_type_id') is-invalid @enderror"
                                        name="metal_type_id" required>
                                        <option value="">Select Material</option>
                                        @foreach($metalTypes as $type)
                                            <option value="{{ $type->id }}" {{ old('metal_type_id') == $type->id ? 'selected' : '' }}>{{ $type->name }}</option>
                                        @endforeach
                                    </select>
                                    <x-input-error :messages="$errors->get('metal_type_id')" />
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label" :class="destinationType !== 'transfer' && 'required'"
                                        x-text="destinationType === 'transfer' ? 'Quantity (Optional)' : 'Total Quantity (CFT)'"></label>
                                    <div class="input-group">
                                        <input type="number" step="0.01"
                                            class="form-control text-end fs-2 fw-bold @error('net_weight') is-invalid @enderror"
                                            name="net_weight" x-model.number="netWeight" @input="calculateTotal()"
                                            :required="destinationType !== 'transfer'">
                                        <span class="input-group-text">CFT</span>
                                    </div>
                                    <x-input-error :messages="$errors->get('net_weight')" />
                                </div>
                                <div class="col-md-3"
                                    x-show="destinationType !== 'transfer' && destinationType !== 'internal'"
                                    x-transition>
                                    <label class="form-label required">Rate per CFT</label>
                                    <div class="input-group">
                                        <span class="input-group-text">₹</span>
                                        <input type="number" step="0.01"
                                            class="form-control text-end fs-2 fw-bold @error('rate_per_ton') is-invalid @enderror"
                                            name="rate_per_ton" x-model.number="ratePerTon" @input="calculateTotal()"
                                            :required="destinationType !== 'transfer' && destinationType !== 'internal'">
                                    </div>
                                    <x-input-error :messages="$errors->get('rate_per_ton')" />
                                </div>
                                <input type="hidden" name="trips" value="1">
                                <input type="hidden" name="gross_weight" value="0">
                                <input type="hidden" name="tare_weight" value="0">
                            </div>
                            <div class="row g-3 mt-2"
                                x-show="destinationType !== 'transfer' && destinationType !== 'internal'" x-transition>
                                <div class="col-12 text-end">
                                    <h2 class="mb-0 text-muted">Total Amount: <span class="text-primary"
                                            x-text="'₹ ' + totalAmount"></span></h2>
                                    <small class="text-muted" x-show="isBillable">(Includes Transport Cost &
                                        Diesel)</small>
                                </div>
                            </div>
                        </div>

                        <!-- Section 4: Logistics & Charges -->
                        <div class="mb-2">
                            <h3 class="card-title text-primary border-bottom pb-2 mb-3">
                                <svg xmlns="http://www.w3.org/2000/svg"
                                    class="icon icon-tabler icon-tabler-receipt me-2" width="24" height="24"
                                    viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                    stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                    <path
                                        d="M5 21v-16a2 2 0 0 1 2 -2h10a2 2 0 0 1 2 2v16l-3 -2l-2 2l-2 -2l-2 2l-2 -2l-3 2">
                                    </path>
                                    <path d="M9 7h6"></path>
                                    <path d="M9 11h6"></path>
                                    <path d="M13 15h2"></path>
                                </svg>
                                4. Logistics & Billing
                            </h3>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">Transport Cost (₹)</label>
                                    <div class="input-group mb-2">
                                        <span class="input-group-text bg-light">₹</span>
                                        <input type="number" step="0.01"
                                            class="form-control text-end fs-3 @error('transport_cost') is-invalid @enderror"
                                            name="transport_cost" x-model.number="transportCost"
                                            @input="isManualTransportCost = true; calculateTotal()">
                                    </div>
                                    <x-input-error :messages="$errors->get('transport_cost')" />
                                </div>
                                <div class="col-md-6 d-flex align-items-center">
                                    <div class="mt-2">
                                        <label class="form-check form-switch form-check-inline"
                                            x-show="activityType === 'Sales'">
                                            <input class="form-check-input" type="checkbox" name="transport_is_billable"
                                                value="1" x-model="isBillable" @change="calculateTotal()">
                                            <span class="form-check-label fw-medium">Bill transport to client?</span>
                                        </label>
                                        <div class="text-muted small" x-show="isBillable && activityType === 'Sales'">
                                            The transport cost will be added to the customer's invoice.
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Diesel Amount (₹)</label>
                                    <div class="input-group mb-2">
                                        <span class="input-group-text bg-light">₹</span>
                                        <input type="number" step="0.01"
                                            class="form-control text-end fs-3 @error('diesel_amount') is-invalid @enderror"
                                            name="diesel_amount" x-model.number="dieselAmount" @input="calculateTotal()"
                                            placeholder="0.00">
                                    </div>
                                    <x-input-error :messages="$errors->get('diesel_amount')" />
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card-footer bg-light px-4 py-3 text-end mt-2">
                        <button type="submit" class="btn btn-primary btn-lg px-6 shadow-sm">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-circle-check"
                                width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
                                fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                <path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0"></path>
                                <path d="M9 12l2 2l4 -4"></path>
                            </svg>
                            Generate Gate Pass
                        </button>
                    </div>
                </x-card>
            </form>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('alpine:init', () => {
                Alpine.data('gatePassCreateForm', (config) => ({
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
                    vehicles: config.vehicles || [],

                    init() {
                        this.checkVehicleMultiplier();
                        this.calculateTotal();

                        this.$watch('clientId', (value) => {
                            if (value && this.activityType === 'Sales') this.isBillable = true;
                        });

                        this.$watch('manualCustomerName', (value) => {
                            if (value && this.activityType === 'Sales') this.isBillable = true;
                        });

                        this.$watch('destinationType', (value) => {
                            if (value === 'registered' && this.clientId) this.isBillable = true;
                            if (value === 'regular' && this.manualCustomerName) this.isBillable = true;
                        });
                    },

                    checkVehicleMultiplier() {
                        if (!this.manualVehicleNumber) return;
                        const normalizedInput = this.manualVehicleNumber.toLowerCase().trim();
                        const vehicle = this.vehicles.find(v => v.number && v.number.toLowerCase() === normalizedInput);
                    },

                    onUsageChange() {
                        if (this.destinationType === 'transfer') {
                            this.activityType = 'Material Transfer';
                            this.sourceUnitId = 1;
                            this.destinationUnitId = 2;
                            this.isBillable = false;
                        } else if (this.destinationType === 'internal') {
                            this.activityType = 'Internal Movement';
                            this.sourceUnitId = 2;
                            this.destinationUnitId = 3;
                            this.isBillable = false;
                        } else {
                            this.activityType = 'Sales';
                            this.sourceUnitId = 2;
                            this.destinationUnitId = 3;
                            this.isBillable = true;
                        }

                        if (this.destinationType !== 'registered' && this.destinationType !== 'internal') {
                            this.clientId = '';
                            this.projectId = '';
                        }
                        if (this.destinationType !== 'regular') {
                            this.manualCustomerName = '';
                        }
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

                        const baseAmount = quantity * rate;
                        this.totalAmount = (baseAmount + diesel + transport).toFixed(2);
                    }
                }));
            });
        </script>
    @endpush
</x-tabler-layout>