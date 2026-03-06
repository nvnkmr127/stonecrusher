<x-tabler-layout>
    <x-slot name="header">
        <div class="row align-items-center">
            <div class="col">
                <div class="mb-1">
                    <x-breadcrumb>
                        <x-breadcrumb-item href="{{ route('dashboard') }}">Dashboard</x-breadcrumb-item>
                        <x-breadcrumb-item href="{{ route('gate-passes.index') }}">Gate Passes</x-breadcrumb-item>
                        <x-breadcrumb-item active>Edit #{{ $gatePass->gate_pass_number }}</x-breadcrumb-item>
                    </x-breadcrumb>
                </div>
                <h2 class="page-title">Edit Gate Pass</h2>
                <div class="page-subtitle">Update entry for vehicle movement</div>
            </div>
            <div class="col-auto">
                <a href="{{ route('gate-passes.index') }}" class="btn btn-secondary">
                    Back to List
                </a>
            </div>
        </div>
    </x-slot>

    <div class="row row-cards">
        <div class="col-12">
            <form action="{{ route('gate-passes.update', $gatePass->id) }}" method="POST" 
                x-data="gatePassEditForm({
                    gatePassNumber: {{ Illuminate\Support\Js::from(old('gate_pass_number', $gatePass->gate_pass_number)) }},
                    date: {{ Illuminate\Support\Js::from(old('date', $gatePass->date->format('Y-m-d\TH:i'))) }},
                    netWeight: {{ (float) old('net_weight', $gatePass->net_weight) }},
                    leadAmount: {{ (float) old('lead', $gatePass->lead ?? 0) }},
                    ratePerTon: {{ (float) old('rate_per_ton', $gatePass->rate_per_ton ?? 0) }},
                    activityType: '{{ old('activity_type', $gatePass->activity_type->value) }}',
                    sourceUnitId: {{ old('source_unit_id', $gatePass->source_unit_id ?? 2) }},
                    destinationUnitId: {{ old('destination_unit_id', $gatePass->destination_unit_id ?? 3) }},
                    trips: {{ old('trips', $gatePass->trips ?? 1) }},
                    isBillable: {{ old('transport_is_billable', $gatePass->transport_is_billable) ? 'true' : 'false' }},
                    clientId: {{ Illuminate\Support\Js::from(old('client_id', $gatePass->client_id)) }},
                    projectId: {{ Illuminate\Support\Js::from(old('project_id', $gatePass->project_id)) }},
                    manualCustomerName: {{ Illuminate\Support\Js::from(old('manual_customer_name', $gatePass->manual_customer_name)) }},
                    villageArea: {{ Illuminate\Support\Js::from(old('village_area', $gatePass->village_area)) }},
                    manualVehicleNumber: {{ Illuminate\Support\Js::from(old('manual_vehicle_number', $gatePass->vehicle ? $gatePass->vehicle->registration_number : $gatePass->manual_vehicle_number)) }},
                    destinationType: {{ Illuminate\Support\Js::from(old('destination_type') ?: (old('manual_customer_name', $gatePass->manual_customer_name) ? 'regular' : ($gatePass->activity_type->value == 'Material Transfer' ? 'transfer' : ($gatePass->project && $gatePass->project->is_internal ? 'internal' : 'registered')))) }}
                })">
                @csrf
                @method('PUT')
                <x-card>
                    <div class="card-body">
                        @if ($errors->any())
                            <div class="alert alert-danger alert-dismissible" role="alert">
                                <div class="d-flex">
                                    <div>
                                        <svg xmlns="http://www.w3.org/2000/svg" class="alert-icon icon icon-tabler icon-tabler-alert-circle me-2" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
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
                        <div class="row g-3">
                            <!-- Section 1: Identification -->
                             <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label required">Gate Pass Number</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control fw-bold bg-light" name="gate_pass_number"
                                            x-model="gatePassNumber" readonly>
                                        <button type="button" class="btn btn-outline-warning" 
                                            x-show="isMismatch" @click="fixGpNumber()" 
                                            title="Update number based on new date">
                                            Fix
                                        </button>
                                    </div>
                                    <div class="mt-1 text-danger small" x-show="isMismatch">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-alert-triangle me-1" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 9v2m0 4v.01m-6.937 7h13.874c1.77 0 2.87 -1.9 1.93 -3.45l-6.937 -12.05a2.23 2.23 0 0 0 -3.86 0l-6.937 12.05c-.94 1.55 .16 3.45 1.937 3.45z" /></svg>
                                        Mismatch with selected date
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label required">Date & Time</label>
                                    <input type="datetime-local"
                                        class="form-control @error('date') is-invalid @enderror" name="date"
                                        x-model="date" required>
                                    <x-input-error :messages="$errors->get('date')" />
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label required">Vehicle Number</label>
                                    <input type="text"
                                        class="form-control @if($errors->has('manual_vehicle_number') || $errors->has('vehicle_id')) is-invalid @endif"
                                        name="manual_vehicle_number" list="vehicleList"
                                        x-model="manualVehicleNumber"
                                        placeholder="Enter Vehicle Number" required autocomplete="off">
                                    <datalist id="vehicleList">
                                        @foreach($vehicles as $vehicle)
                                            <option value="{{ $vehicle->registration_number }}"></option>
                                        @endforeach
                                    </datalist>
                                    <x-input-error :messages="$errors->get('manual_vehicle_number')" />
                                    <x-input-error :messages="$errors->get('vehicle_id')" />
                                </div>
                            </div>

                            <div class="col-12 mt-0">
                                <hr class="my-2 opacity-50">
                            </div>

                            <!-- Section 2: Destination & Usage Selection -->
                            <div class="col-12 mb-3">
                                <label class="form-label fw-bold mb-3">Selling To / Movement Type</label>
                                <div class="form-selectgroup form-selectgroup-boxes d-flex flex-column flex-md-row gap-2">
                                    <label class="form-selectgroup-item flex-fill">
                                        <input type="radio" name="destination_type" value="registered" class="form-selectgroup-input" x-model="destinationType" @change="onUsageChange()">
                                        <span class="form-selectgroup-label d-flex align-items-center p-3">
                                            <span class="me-3"><span class="form-selectgroup-check"></span></span>
                                            <span class="form-selectgroup-label-content text-start">
                                                <span class="form-selectgroup-title fw-bold">Selling to Client</span>
                                                <span class="text-muted small">Registered account</span>
                                            </span>
                                        </span>
                                    </label>
                                    <label class="form-selectgroup-item flex-fill">
                                        <input type="radio" name="destination_type" value="regular" class="form-selectgroup-input" x-model="destinationType" @change="onUsageChange()">
                                        <span class="form-selectgroup-label d-flex align-items-center p-3">
                                            <span class="me-3"><span class="form-selectgroup-check"></span></span>
                                            <span class="form-selectgroup-label-content text-start">
                                                <span class="form-selectgroup-title fw-bold">Regular Sale</span>
                                                <span class="text-muted small">Enter name manually</span>
                                            </span>
                                        </span>
                                    </label>
                                    <label class="form-selectgroup-item flex-fill">
                                        <input type="radio" name="destination_type" value="internal" class="form-selectgroup-input" x-model="destinationType" @change="onUsageChange()">
                                        <span class="form-selectgroup-label d-flex align-items-center p-3">
                                            <span class="me-3"><span class="form-selectgroup-check"></span></span>
                                            <span class="form-selectgroup-label-content text-start">
                                                <span class="form-selectgroup-title fw-bold">Internal Project</span>
                                                <span class="text-muted small">Own project usage</span>
                                            </span>
                                        </span>
                                    </label>
                                    <label class="form-selectgroup-item flex-fill">
                                        <input type="radio" name="destination_type" value="transfer" class="form-selectgroup-input" x-model="destinationType" @change="onUsageChange()">
                                        <span class="form-selectgroup-label d-flex align-items-center p-3">
                                            <span class="me-3"><span class="form-selectgroup-check"></span></span>
                                            <span class="form-selectgroup-label-content text-start">
                                                <span class="form-selectgroup-title fw-bold">Material Transfer</span>
                                                <span class="text-muted small">Quarry to Crusher</span>
                                            </span>
                                        </span>
                                    </label>
                                </div>
                            </div>

                            <input type="hidden" name="activity_type" :value="activityType">
                            <input type="hidden" name="source_unit_id" x-model="sourceUnitId">
                            <input type="hidden" name="destination_unit_id" x-model="destinationUnitId">

                            <!-- Conditional Inputs based on Selection -->
                            <div class="col-12" x-show="destinationType !== 'transfer'" x-transition>

                            <!-- Registered Mode Fields -->
                            <div class="col-md-6" x-show="destinationType === 'registered'" x-transition>
                                <div class="mb-3">
                                    <label class="form-label required">Client / Customer</label>
                                    <select class="form-select @error('client_id') is-invalid @enderror"
                                        name="client_id" x-model="clientId" :required="destinationType === 'registered'">
                                        <option value="">Select Client</option>
                                        @foreach($clients as $client)
                                            <option value="{{ $client->id }}" {{ old('client_id', $gatePass->client_id) == $client->id ? 'selected' : '' }}>{{ $client->name }}</option>
                                        @endforeach
                                    </select>
                                    <x-input-error :messages="$errors->get('client_id')" />
                                </div>
                            </div>

                            <div class="col-md-6" x-show="destinationType === 'registered'" x-transition>
                                <div class="mb-3">
                                    <label class="form-label">Project (Optional)</label>
                                    <select class="form-select @error('project_id') is-invalid @enderror"
                                        name="project_id" x-model="projectId" @change="onProjectChange($event)">
                                        <option value="">Select Project</option>
                                        @foreach($projects->where('is_internal', false) as $project)
                                            <option value="{{ $project->id }}" 
                                                data-client-id="{{ $project->client_id }}"
                                                {{ old('project_id', $gatePass->project_id) == $project->id ? 'selected' : '' }}>
                                                {{ $project->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <x-input-error :messages="$errors->get('project_id')" />
                                </div>
                            </div>

                            <!-- Regular Sale Mode Fields -->
                            <div class="col-md-6" x-show="destinationType === 'regular'" x-transition>
                                <div class="mb-3">
                                    <label class="form-label required">Customer Name</label>
                                    <input type="text" class="form-control @error('manual_customer_name') is-invalid @enderror"
                                        name="manual_customer_name" x-model="manualCustomerName"
                                        placeholder="Enter Customer Name" :required="destinationType === 'regular'">
                                    <x-input-error :messages="$errors->get('manual_customer_name')" />
                                </div>
                            </div>
                            <div class="col-md-6" x-show="destinationType === 'regular'" x-transition>
                                <div class="mb-3">
                                    <label class="form-label required">Village or Area</label>
                                    <input type="text" class="form-control @error('village_area') is-invalid @enderror"
                                        name="village_area" x-model="villageArea"
                                        placeholder="Enter Village or Area" :required="destinationType === 'regular'">
                                    <x-input-error :messages="$errors->get('village_area')" />
                                </div>
                            </div>

                            <!-- Internal Mode Fields -->
                            <div class="col-md-12" x-show="destinationType === 'internal'" x-transition>
                                <div class="mb-3">
                                    <label class="form-label required">Internal Project</label>
                                    <select class="form-select @error('project_id') is-invalid @enderror"
                                        name="project_id" x-model="projectId" @change="onProjectChange($event)" :required="destinationType === 'internal'">
                                        <option value="">Select Internal Project</option>
                                        @foreach($projects->where('is_internal', true) as $project)
                                            <option value="{{ $project->id }}" 
                                                data-is-internal="1"
                                                {{ old('project_id', $gatePass->project_id) == $project->id ? 'selected' : '' }}>
                                                {{ $project->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <x-input-error :messages="$errors->get('project_id')" />
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <label class="form-label required">Material Type</label>
                                        <select class="form-select @error('metal_type_id') is-invalid @enderror"
                                            name="metal_type_id" required>
                                            <option value="">Select Material</option>
                                            @foreach($metalTypes as $type)
                                                <option value="{{ $type->id }}" {{ old('metal_type_id', $gatePass->metal_type_id) == $type->id ? 'selected' : '' }}>{{ $type->name }}</option>
                                            @endforeach
                                        </select>
                                        <x-input-error :messages="$errors->get('metal_type_id')" />
                                    </div>
                                </div>
                            </div>
                            <input type="hidden" name="trips" value="1">

                            <div class="col-12 mt-0">
                                <hr class="my-2 opacity-50">
                            </div>

                            <!-- Section 3: Quantity & Cost -->
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label class="form-label" :class="destinationType !== 'transfer' && 'required'"
                                        x-text="destinationType === 'transfer' ? 'Quantity (Optional)' : 'Weight / Quantity (CFT)'"></label>
                                    <div class="input-group">
                                        <input type="number" step="0.01"
                                            class="form-control text-end fs-2 fw-bold @error('net_weight') is-invalid @enderror" name="net_weight"
                                            x-model.number="netWeight" @input="calculateTotal()" :required="destinationType !== 'transfer'">
                                        <span class="input-group-text">CFT</span>
                                    </div>
                                    <x-input-error :messages="$errors->get('net_weight')" />
                                </div>
                                <input type="hidden" name="gross_weight" value="{{ $gatePass->gross_weight ?? 0 }}">
                                <input type="hidden" name="tare_weight" value="{{ $gatePass->tare_weight ?? 0 }}">
                            </div>

                            <div class="col-md-3" x-show="destinationType !== 'transfer' && destinationType !== 'internal'" x-transition>
                                <label class="form-label required">Rate per CFT</label>
                                <div class="input-group">
                                    <span class="input-group-text">₹</span>
                                    <input type="number" step="0.01"
                                        class="form-control text-end @error('rate_per_ton') is-invalid @enderror"
                                        name="rate_per_ton" x-model.number="ratePerTon" @input="calculateTotal()"
                                        :required="destinationType !== 'transfer' && destinationType !== 'internal'">
                                </div>
                                <x-input-error :messages="$errors->get('rate_per_ton')" />
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Lead (₹)</label>
                                    <div class="input-group mb-2">
                                        <span class="input-group-text">₹</span>
                                        <input type="number" step="0.01"
                                            class="form-control @error('lead') is-invalid @enderror"
                                            name="lead" x-model.number="leadAmount" @input="calculateTotal()">
                                    </div>
                                    <label class="form-check form-check-inline" x-show="activityType === 'Sales'">
                                        <input class="form-check-input" type="checkbox" name="transport_is_billable"
                                            value="1" id="billTransport" x-model="isBillable" @change="calculateTotal()"
                                            {{ $gatePass->transport_is_billable ? 'checked' : '' }}>
                                        <span class="form-check-label">Bill lead to client?</span>
                                    </label>
                                    <x-input-error :messages="$errors->get('lead')" />
                                </div>
                            </div>

                            <div class="col-12 mt-2" x-show="destinationType !== 'transfer' && destinationType !== 'internal'" x-transition>
                                <div class="col-12 text-end">
                                    <h2 class="mb-0 text-muted">Total Amount: <span class="text-primary" x-text="'₹ ' + totalAmount"></span></h2>
                                    <small class="text-muted" x-show="isBillable">(Includes Lead)</small>
                                </div>
                            </div>


                        </div>
                    </div>
                    <div class="card-footer bg-light-subtle text-end">
                        <button type="submit" class="btn btn-success btn-lg px-5">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-device-floppy" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M6 4h10l4 4v10a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2v-12a2 2 0 0 1 2 -2" /><path d="M12 14m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0" /><path d="M14 4l0 4l-6 0l0 -4" /></svg>
                            Update Gate Pass
                        </button>
                    </div>
                </x-card>
            </form>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('alpine:init', () => {
                Alpine.data('gatePassEditForm', (initial) => ({
                    gatePassNumber: initial.gatePassNumber,
                    date: initial.date,
                    isMismatch: false,
                    netWeight: initial.netWeight,
                    leadAmount: initial.leadAmount,
                    activityType: initial.activityType,
                    sourceUnitId: initial.sourceUnitId,
                    destinationUnitId: initial.destinationUnitId,
                    trips: initial.trips,
                    clientId: initial.clientId,
                    projectId: initial.projectId,
                    manualCustomerName: initial.manualCustomerName,
                    villageArea: initial.villageArea,
                    manualVehicleNumber: initial.manualVehicleNumber,
                    destinationType: initial.destinationType,
                    isBillable: initial.isBillable,
                    ratePerTon: initial.ratePerTon,
                    totalAmount: 0,

                     init() {
                        this.calculateTotal();
                        this.checkMismatch();

                        this.$watch('date', () => {
                            this.checkMismatch();
                        });

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
                            if (value === 'registered' && this.clientId) {
                                this.isBillable = true;
                            }
                            if (value === 'regular' && this.manualCustomerName) {
                                this.isBillable = true;
                            }
                        });
                    },

                    checkMismatch() {
                        if (!this.date || !this.gatePassNumber) return;
                        const datePrefix = 'GP-' + this.date.split('T')[0].replace(/-/g, '');
                        this.isMismatch = !this.gatePassNumber.startsWith(datePrefix);
                    },

                    async fixGpNumber() {
                        if (!this.date) return;
                        const dateOnly = this.date.split('T')[0];
                        try {
                            const response = await fetch(`/gate-passes/next-number?date=${dateOnly}`);
                            const data = await response.json();
                            if (data.next_number) {
                                this.gatePassNumber = data.next_number;
                                this.checkMismatch();
                            }
                        } catch (error) {
                            console.error('Failed to fetch next GP number', error);
                        }
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
                            this.villageArea = '';
                        }
                    },

                    onDestinationTypeChange() {
                        this.onUsageChange();
                    },

                    onProjectChange(e) {
                        if (!this.projectId) return;

                        const select = e ? e.target : document.querySelector('select[name="project_id"]');
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
                        const lead = this.isBillable ? (parseFloat(this.leadAmount) || 0) : 0;

                        // Base amount from Quantity * Rate
                        const baseAmount = quantity * rate;
                        
                        // Total = Base Amount + Lead
                        this.totalAmount = (baseAmount + lead).toFixed(2);
                    }
                }));
            });
        </script>
    @endpush
</x-tabler-layout>