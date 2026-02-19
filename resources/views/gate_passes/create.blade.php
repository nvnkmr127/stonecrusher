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
                grossWeight: {{ (float) old('gross_weight', 0) }},
                tareWeight: {{ (float) old('tare_weight', 0) }},
                netWeight: {{ (float) old('net_weight', 0) }},
                clientId: {{ Illuminate\Support\Js::from(old('client_id', '')) }},
                projectId: {{ Illuminate\Support\Js::from(old('project_id', '')) }},
                manualCustomerName: {{ Illuminate\Support\Js::from(old('manual_customer_name', '')) }},
                manualVehicleNumber: {{ Illuminate\Support\Js::from(old('manual_vehicle_number', '')) }},
                destinationType: {{ Illuminate\Support\Js::from(old('manual_customer_name') ? 'regular' : (old('project_id') && count($projects->where('id', old('project_id'))->where('is_internal', true)) > 0 ? 'internal' : 'registered')) }},
                isBillable: {{ (old('client_id') || old('manual_customer_name') || old('transport_is_billable')) ? 'true' : 'false' }},
                isManualVehicle: true,
                vehicles: {{ Illuminate\Support\Js::from($vehicles->map(fn($v) => ['number' => $v->vehicle_number, 'multiplier' => (float)$v->transport_multiplier])) }}
            })">
                @csrf
                <x-card>
                    <div class="card-body">
                        <div class="row row-cards">
                            <div class="row g-3">
                                <!-- Section 1: Identification -->
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label class="form-label required">Gate Pass Number</label>
                                        <input type="text" class="form-control fw-bold" name="gate_pass_number"
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
                                        <label class="form-label required">Vehicle Number</label>
                                        <input type="text"
                                            class="form-control @error('manual_vehicle_number') is-invalid @enderror"
                                            name="manual_vehicle_number" list="vehicleList"
                                            x-model="manualVehicleNumber" @input="checkVehicleMultiplier()"
                                            placeholder="Enter Vehicle Number" required autocomplete="off">
                                        <datalist id="vehicleList">
                                            @foreach($vehicles as $vehicle)
                                                <option value="{{ $vehicle->vehicle_number }}"></option>
                                            @endforeach
                                        </datalist>
                                        <x-input-error :messages="$errors->get('manual_vehicle_number')" />
                                    </div>
                                </div>

                                <div class="col-12 mt-0">
                                    <hr class="my-2 opacity-50">
                                </div>

                                <!-- Section 2: Destination Selection -->
                                <div class="col-12 mb-2">
                                    <label class="form-label font-bold mb-2">Selling To / Destination</label>
                                    <div class="form-selectgroup form-selectgroup-boxes d-flex flex-column flex-md-row gap-2">
                                        <label class="form-selectgroup-item flex-fill">
                                            <input type="radio" name="destination_type" value="registered" class="form-selectgroup-input" x-model="destinationType" @change="onDestinationTypeChange()">
                                            <span class="form-selectgroup-label d-flex align-items-center p-3">
                                                <span class="me-3">
                                                    <span class="form-selectgroup-check"></span>
                                                </span>
                                                <span class="form-selectgroup-label-content">
                                                    <span class="form-selectgroup-title fw-bold">Selling to Client</span>
                                                    <span class="text-muted">Registered account</span>
                                                </span>
                                            </span>
                                        </label>
                                        <label class="form-selectgroup-item flex-fill">
                                            <input type="radio" name="destination_type" value="regular" class="form-selectgroup-input" x-model="destinationType" @change="onDestinationTypeChange()">
                                            <span class="form-selectgroup-label d-flex align-items-center p-3">
                                                <span class="me-3">
                                                    <span class="form-selectgroup-check"></span>
                                                </span>
                                                <span class="form-selectgroup-label-content">
                                                    <span class="form-selectgroup-title fw-bold">Regular Sale</span>
                                                    <span class="text-muted">Enter name manually</span>
                                                </span>
                                            </span>
                                        </label>
                                        <label class="form-selectgroup-item flex-fill">
                                            <input type="radio" name="destination_type" value="internal" class="form-selectgroup-input" x-model="destinationType" @change="onDestinationTypeChange()">
                                            <span class="form-selectgroup-label d-flex align-items-center p-3">
                                                <span class="me-3">
                                                    <span class="form-selectgroup-check"></span>
                                                </span>
                                                <span class="form-selectgroup-label-content">
                                                    <span class="form-selectgroup-title fw-bold">Internal Project</span>
                                                    <span class="text-muted">Own usage</span>
                                                </span>
                                            </span>
                                        </label>
                                    </div>
                                </div>

                                <!-- Registered Mode Fields -->
                                <div class="col-md-6" x-show="destinationType === 'registered'" x-transition>
                                    <div class="mb-3">
                                        <label class="form-label required">Client / Customer</label>
                                        <select class="form-select @error('client_id') is-invalid @enderror"
                                            name="client_id" x-model="clientId" :required="destinationType === 'registered'">
                                            <option value="">Select Client</option>
                                            @foreach($clients as $client)
                                                <option value="{{ $client->id }}" {{ old('client_id') == $client->id ? 'selected' : '' }}>{{ $client->name }}</option>
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
                                                    {{ old('project_id') == $project->id ? 'selected' : '' }}>
                                                    {{ $project->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <x-input-error :messages="$errors->get('project_id')" />
                                    </div>
                                </div>

                                <!-- Regular Sale Mode Fields -->
                                <div class="col-md-12" x-show="destinationType === 'regular'" x-transition>
                                    <div class="mb-3">
                                        <label class="form-label required">Customer Name</label>
                                        <input type="text" class="form-control @error('manual_customer_name') is-invalid @enderror"
                                            name="manual_customer_name" x-model="manualCustomerName"
                                            placeholder="Enter Customer Name" :required="destinationType === 'regular'">
                                        <x-input-error :messages="$errors->get('manual_customer_name')" />
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
                                                    {{ old('project_id') == $project->id ? 'selected' : '' }}>
                                                    {{ $project->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <x-input-error :messages="$errors->get('project_id')" />
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Material Type</label>
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

                                <div class="col-12 mt-0">
                                    <hr class="my-2 opacity-50">
                                </div>

                                <!-- Section 3: Quantity & Cost -->
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Weight / Quantity (CFT)</label>
                                        <div class="input-group">
                                            <input type="number" step="0.01"
                                                class="form-control @error('net_weight') is-invalid @enderror"
                                                name="net_weight" x-model.number="netWeight" @input="calculateTotal()"
                                                required>
                                            <span class="input-group-text">CFT</span>
                                        </div>
                                        <x-input-error :messages="$errors->get('net_weight')" />
                                    </div>
                                    <input type="hidden" name="gross_weight" value="0">
                                    <input type="hidden" name="tare_weight" value="0">
                                </div>

                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Transport Cost (₹)</label>
                                        <div class="input-group mb-2">
                                            <span class="input-group-text">₹</span>
                                            <input type="number" step="0.01"
                                                class="form-control @error('transport_cost') is-invalid @enderror"
                                                name="transport_cost" x-model.number="transportCost"
                                                @input="isManualTransportCost = true; calculateTotal()">
                                        </div>
                                        <label class="form-check form-check-inline">
                                            <input class="form-check-input" type="checkbox" name="transport_is_billable"
                                                value="1" id="billTransport" x-model="isBillable"
                                                @change="calculateTotal()">
                                            <span class="form-check-label">Bill transport to client?</span>
                                        </label>
                                        <x-input-error :messages="$errors->get('transport_cost')" />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer bg-light-subtle text-end">
                            <button type="submit" class="btn btn-primary btn-lg px-5">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-check"
                                    width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
                                    fill="none" stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                    <path d="M5 12l5 5l10 -10" />
                                </svg>
                                Create Gate Pass
                            </button>
                        </div>
                </x-card>
            </form>
        </div>
    </div>
</x-tabler-layout>