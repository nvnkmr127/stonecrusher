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
                <a href="{{ route('gate-passes.index') }}" class="btn btn-secondary shadow-sm">
                    Cancel
                </a>
            </div>
        </div>
    </x-slot>

    <div class="row row-cards">
        <div class="col-lg-10 mx-auto">
            <form action="{{ route('gate-passes.update', $gatePass->id) }}" method="POST" x-data="gatePassEditForm({
                gatePassNumber: {{ Illuminate\Support\Js::from(old('gate_pass_number', $gatePass->gate_pass_number)) }},
                date: {{ Illuminate\Support\Js::from(old('date', $gatePass->date->format('Y-m-d\TH:i'))) }},
                status: '{{ old('status', $gatePass->status->value) }}',
                netWeight: {{ Illuminate\Support\Js::from(old('net_weight', $gatePass->net_weight ?: '')) }},
                leadAmount: {{ Illuminate\Support\Js::from(old('lead', $gatePass->lead ?: '')) }},
                ratePerTon: {{ Illuminate\Support\Js::from(old('rate_per_ton', $gatePass->rate_per_ton ?: '')) }},
                activityType: '{{ old('activity_type', $gatePass->activity_type->value) }}',
                sourceUnitId: {{ old('source_unit_id', $gatePass->source_unit_id ?? 2) }},
                destinationUnitId: {{ old('destination_unit_id', $gatePass->destination_unit_id ?? 3) }},
                trips: {{ old('trips', $gatePass->trips ?? 1) }},
                isBillable: {{ old('transport_is_billable', $gatePass->transport_is_billable) ? 'true' : 'false' }},
                clientId: {{ Illuminate\Support\Js::from(old('client_id', $gatePass->client_id)) }},
                projectId: {{ Illuminate\Support\Js::from(old('project_id', $gatePass->project_id)) }},
                manualCustomerName: {{ Illuminate\Support\Js::from(old('manual_customer_name', $gatePass->manual_customer_name)) }},
                villageArea: {{ Illuminate\Support\Js::from(old('village_area', $gatePass->village_area)) }},
                vehicleId: {{ Illuminate\Support\Js::from(old('vehicle_id', $gatePass->vehicle_id)) }},
                manualVehicleNumber: {{ Illuminate\Support\Js::from(old('manual_vehicle_number', $gatePass->vehicle ? $gatePass->vehicle->registration_number : $gatePass->manual_vehicle_number)) }},
                destinationType: {{ Illuminate\Support\Js::from(old('destination_type') ?: ($gatePass->manual_customer_name ? 'regular' : ($gatePass->activity_type->value == 'Material Transfer' ? 'transfer' : (($gatePass->activity_type->value == 'Internal Movement' || ($gatePass->project && $gatePass->project->is_internal)) ? 'internal' : 'registered')))) }}
            })">
                @csrf
                @method('PUT')
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
                                <div class="col-md-3">
                                    <label class="form-label required">Gate Pass Number</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control fw-bold bg-light" name="gate_pass_number"
                                            x-model="gatePassNumber" readonly>
                                        <button type="button" class="btn btn-outline-warning" x-show="isMismatch"
                                            @click="updateGpNumber()" title="Update number based on new date">
                                            Fix
                                        </button>
                                    </div>
                                    <div class="mt-1 text-danger small" x-show="isMismatch">
                                        <svg xmlns="http://www.w3.org/2000/svg"
                                            class="icon icon-tabler icon-tabler-alert-triangle me-1" width="24"
                                            height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
                                            fill="none" stroke-linecap="round" stroke-linejoin="round">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                            <path
                                                d="M12 9v2m0 4v.01m-6.937 7h13.874c1.77 0 2.87 -1.9 1.93 -3.45l-6.937 -12.05a2.23 2.23 0 0 0 -3.86 0l-6.937 12.05c-.94 1.55 .16 3.45 1.937 3.45z" />
                                        </svg>
                                        Mismatch with selected date
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label required">Date & Time</label>
                                    <input type="datetime-local"
                                        class="form-control @error('date') is-invalid @enderror" name="date"
                                        x-model="date" required>
                                    <x-input-error :messages="$errors->get('date')" />
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label required">Status</label>
                                    <select class="form-select @error('status') is-invalid @enderror" name="status"
                                        x-model="status" required>
                                        @foreach(\App\Enums\GatePassStatus::cases() as $s)
                                            <option value="{{ $s->value }}">{{ ucfirst($s->value) }}</option>
                                        @endforeach
                                    </select>
                                    <x-input-error :messages="$errors->get('status')" />
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label required">Vehicle Number</label>
                                    <div class="position-relative" @click.away="showSuggestions = false">
                                        <div class="input-icon">
                                            <span class="input-icon-addon">
                                                <svg xmlns="http://www.w3.org/2000/svg"
                                                    class="icon icon-tabler icon-tabler-truck" width="24" height="24"
                                                    viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
                                                    fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                                    <path d="M7 17m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0"></path>
                                                    <path d="M17 17m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0"></path>
                                                    <path
                                                        d="M5 17h-2v-11a1 1 0 0 1 1 -1h9v12m-4 0h6m4 0h2v-6h-8m0 -5h5l3 5">
                                                    </path>
                                                </svg>
                                            </span>
                                            <input type="text"
                                                class="form-control text-uppercase fw-medium @if($errors->has('manual_vehicle_number')) is-invalid @endif"
                                                x-model="searchTerm" @input.debounce.300ms="searchVehicles()"
                                                @focus="if(searchTerm.length >= 2) showSuggestions = true"
                                                placeholder="ABC-1234" required autocomplete="off">
                                            <input type="hidden" name="vehicle_id" x-model="vehicleId">
                                            <input type="hidden" name="manual_vehicle_number"
                                                x-model="manualVehicleNumber">
                                        </div>

                                        <!-- Suggestions Dropdown -->
                                        <div x-show="showSuggestions && (suggestions.length > 0 || searchTerm.length >= 2)"
                                            class="dropdown-menu show w-100 shadow-lg border-primary"
                                            style="max-height: 300px; overflow-y: auto; z-index: 1050;" x-transition>
                                            <div x-show="isLoading" class="p-3 text-center">
                                                <div class="spinner-border spinner-border-sm text-primary"
                                                    role="status"></div>
                                                <span class="ms-2">Searching...</span>
                                            </div>

                                            <template x-for="vehicle in suggestions" :key="vehicle.id">
                                                <button type="button" class="dropdown-item py-2 border-bottom"
                                                    @click="selectVehicle(vehicle)">
                                                    <div class="d-flex justify-content-between align-items-center">
                                                        <div>
                                                            <div class="fw-bold text-primary"
                                                                x-text="vehicle.registration_number"></div>
                                                            <div class="text-muted small"
                                                                x-text="vehicle.model || 'No model specified'"></div>
                                                        </div>
                                                        <div class="text-end">
                                                            <span class="badge bg-green-lt px-2">CFT: <span
                                                                    x-text="vehicle.cft"></span></span>
                                                        </div>
                                                    </div>
                                                </button>
                                            </template>

                                            <div x-show="!isLoading && searchTerm.length >= 2"
                                                class="p-2 border-top bg-light">
                                                <button type="button" class="btn btn-outline-primary btn-sm w-100"
                                                    @click="openCreateVehicleModal()">
                                                    <svg xmlns="http://www.w3.org/2000/svg"
                                                        class="icon icon-tabler icon-tabler-plus" width="24" height="24"
                                                        viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
                                                        fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                                        <path d="M12 5l0 14"></path>
                                                        <path d="M5 12l14 0"></path>
                                                    </svg>
                                                    Create "<span x-text="searchTerm" class="text-uppercase"></span>" as
                                                    New
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    <x-input-error :messages="$errors->get('manual_vehicle_number')" />
                                </div>
                            </div>
                        </div>

                        <!-- Modal: Create New Vehicle -->
                        <div class="modal modal-blur fade" id="modal-new-vehicle" tabindex="-1" role="dialog"
                            aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered" role="document">
                                <div class="modal-content shadow-lg border-primary">
                                    <div class="modal-header bg-primary text-white">
                                        <h5 class="modal-title">Quick Add New Vehicle</h5>
                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                                            aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="mb-3">
                                            <label class="form-label required">Registration Number</label>
                                            <input type="text" class="form-control text-uppercase fw-bold"
                                                x-model="newVehicle.registration_number" placeholder="ABC-1234">
                                        </div>
                                        <div class="row">
                                            <div class="col-md-7">
                                                <div class="mb-3">
                                                    <label class="form-label">Vehicle Model</label>
                                                    <input type="text" class="form-control" x-model="newVehicle.model"
                                                        placeholder="e.g. Tata Tiara, Leyland">
                                                </div>
                                            </div>
                                            <div class="col-md-5">
                                                <div class="mb-3">
                                                    <label class="form-label required">Vehicle CFT</label>
                                                    <div class="input-group">
                                                        <input type="number" step="0.01" class="form-control"
                                                            x-model.number="newVehicle.cft">
                                                        <span class="input-group-text">CFT</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-link link-secondary me-auto"
                                            data-bs-dismiss="modal">Cancel</button>
                                        <button type="button" class="btn btn-primary shadow" @click="saveNewVehicle()"
                                            :disabled="isSavingVehicle">
                                            <span x-show="isSavingVehicle" class="spinner-border spinner-border-sm me-2"
                                                role="status"></span>
                                            <svg x-show="!isSavingVehicle" xmlns="http://www.w3.org/2000/svg"
                                                class="icon icon-tabler icon-tabler-device-floppy me-1" width="24"
                                                height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
                                                fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                                <path
                                                    d="M6 4h10l4 4v10a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2v-12a2 2 0 0 1 2 -2">
                                                </path>
                                                <path d="M12 14m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0"></path>
                                                <path d="M14 4l0 4l-6 0l0 -4"></path>
                                            </svg>
                                            Save & Select Vehicle
                                        </button>
                                    </div>
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
                                    <div class="col-md-6" x-show="destinationType === 'regular'">
                                        <label class="form-label">Customer Name (Manual)</label>
                                        <input type="text"
                                            class="form-control @error('manual_customer_name') is-invalid @enderror"
                                            name="manual_customer_name" x-model="manualCustomerName"
                                            placeholder="Enter Customer Name">
                                        <x-input-error :messages="$errors->get('manual_customer_name')" />
                                    </div>
                                    <div class="col-md-6" x-show="destinationType === 'regular'">
                                        <label class="form-label">Village or Area</label>
                                        <input type="text"
                                            class="form-control @error('village_area') is-invalid @enderror"
                                            name="village_area" x-model="villageArea"
                                            placeholder="Enter Village or Area">
                                        <x-input-error :messages="$errors->get('village_area')" />
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
                                            <option value="{{ $type->id }}" {{ old('metal_type_id', $gatePass->metal_type_id) == $type->id ? 'selected' : '' }}>{{ $type->name }}
                                            </option>
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
                                <div class="col-md-4" x-show="destinationType !== 'internal'" x-transition>
                                    <label class="form-label">Lead (₹)</label>
                                    <div class="input-group mb-2">
                                        <span class="input-group-text bg-light">₹</span>
                                        <input type="number" step="0.01"
                                            class="form-control text-end fs-3 @error('lead') is-invalid @enderror"
                                            name="lead" x-model.number="leadAmount" @input="calculateTotal()">
                                    </div>
                                    <x-input-error :messages="$errors->get('lead')" />
                                </div>
                                <input type="hidden" name="trips" value="1">
                                <div class="col-md-4 d-flex align-items-center" x-show="destinationType !== 'internal'"
                                    x-transition>
                                    <div class="mt-2">
                                        <label class="form-check form-switch form-check-inline"
                                            x-show="activityType === 'Sales'">
                                            <input class="form-check-input" type="checkbox" name="transport_is_billable"
                                                value="1" x-model="isBillable" @change="calculateTotal()">
                                            <span class="form-check-label fw-medium">Bill lead to client?</span>
                                        </label>
                                        <div class="text-muted small" x-show="isBillable && activityType === 'Sales'">
                                            The lead cost will be added to the customer's invoice.
                                        </div>
                                    </div>
                                </div>

                                <input type="hidden" name="gross_weight" value="{{ $gatePass->gross_weight ?? 0 }}">
                                <input type="hidden" name="tare_weight" value="{{ $gatePass->tare_weight ?? 0 }}">
                            </div>

                            <div class="row g-3 mt-2"
                                x-show="destinationType !== 'transfer' && destinationType !== 'internal'" x-transition>
                                <div class="col-12 text-end">
                                    <h2 class="mb-0 text-muted">Total Amount: <span class="text-primary"
                                            x-text="'₹ ' + totalAmount"></span></h2>
                                    <small class="text-muted" x-show="isBillable">(Includes Lead)</small>
                                </div>
                            </div>
                        </div>

                    </div>
                    <div class="card-footer bg-light px-4 py-3 text-end mt-2">
                        <button type="submit" class="btn btn-success btn-lg px-6 shadow-sm">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-device-floppy"
                                width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
                                fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                <path d="M6 4h10l4 4v10a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2v-12a2 2 0 0 1 2 -2"></path>
                                <path d="M12 14m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0"></path>
                                <path d="M14 4l0 4l-6 0l0 -4"></path>
                            </svg>
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
                    // Form Data
                    gatePassNumber: initial.gatePassNumber || '',
                    date: initial.date || '',
                    status: initial.status || 'pending',
                    isMismatch: false,
                    netWeight: initial.netWeight ?? '',
                    ratePerTon: initial.ratePerTon ?? '',
                    leadAmount: initial.leadAmount ?? '',
                    totalAmount: 0,
                    activityType: initial.activityType || 'Sales',
                    sourceUnitId: initial.sourceUnitId || 2,
                    destinationUnitId: initial.destinationUnitId || 3,
                    trips: initial.trips || 1,
                    clientId: initial.clientId || '',
                    projectId: initial.projectId || '',
                    manualCustomerName: initial.manualCustomerName || '',
                    villageArea: initial.villageArea || '',
                    destinationType: initial.destinationType || 'registered',
                    vehicleId: initial.vehicleId || '',
                    manualVehicleNumber: initial.manualVehicleNumber || '',
                    isBillable: initial.isBillable || false,

                    // Vehicle Suggestion State
                    searchTerm: initial.manualVehicleNumber || '',
                    suggestions: [],
                    isLoading: false,
                    showSuggestions: false,
                    newVehicle: {
                        registration_number: '',
                        model: '',
                        cft: 0
                    },
                    isSavingVehicle: false,

                    init() {
                        this.calculateTotal();
                        this.checkMismatch();

                        this.$watch('date', () => {
                            this.checkMismatch();
                        });

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

                        this.$watch('searchTerm', (value) => {
                            this.manualVehicleNumber = value;
                            // Reset vehicleId if typing something new
                            if (this.vehicleId && value !== this.manualVehicleNumber) {
                                // Only reset if it's not the current one being typed
                            }
                        });
                    },

                    checkMismatch() {
                        if (!this.date || !this.gatePassNumber) return;
                        const datePrefix = 'GP-' + this.date.split('T')[0].replace(/-/g, '');
                        this.isMismatch = !this.gatePassNumber.startsWith(datePrefix);
                    },

                    async updateGpNumber() {
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

                    async searchVehicles() {
                        if (this.searchTerm.length < 2) {
                            this.suggestions = [];
                            return;
                        }
                        this.isLoading = true;
                        try {
                            const response = await fetch(`/vehicles/search?q=${this.searchTerm}`);
                            this.suggestions = await response.json();
                            this.showSuggestions = true;
                        } catch (error) {
                            console.error('Search failed', error);
                        } finally {
                            this.isLoading = false;
                        }
                    },

                    selectVehicle(vehicle) {
                        this.vehicleId = vehicle.id;
                        this.manualVehicleNumber = vehicle.registration_number;
                        this.searchTerm = vehicle.registration_number;
                        this.netWeight = vehicle.cft;
                        this.suggestions = [];
                        this.showSuggestions = false;
                        this.calculateTotal();
                    },

                    openCreateVehicleModal() {
                        this.newVehicle.registration_number = this.searchTerm.toUpperCase();
                        this.showSuggestions = false;
                        const modal = new bootstrap.Modal(document.getElementById('modal-new-vehicle'));
                        modal.show();
                    },

                    async saveNewVehicle() {
                        if (!this.newVehicle.registration_number) return;
                        this.isSavingVehicle = true;
                        try {
                            const response = await fetch('/vehicles/quick-store', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                                    'Accept': 'application/json'
                                },
                                body: JSON.stringify(this.newVehicle)
                            });
                            const data = await response.json();
                            if (data.success) {
                                this.selectVehicle(data.vehicle);
                                const modalElement = document.getElementById('modal-new-vehicle');
                                const modalInstance = bootstrap.Modal.getInstance(modalElement);
                                if (modalInstance) modalInstance.hide();
                            } else {
                                alert(data.message || 'Validation error');
                            }
                        } catch (error) {
                            console.error('Save failed', error);
                            alert('An error occurred while saving the vehicle.');
                        } finally {
                            this.isSavingVehicle = false;
                        }
                    },

                    onUsageChange() {
                        if (this.destinationType === 'transfer') {
                            this.activityType = 'Material Transfer';
                            this.sourceUnitId = 1;
                            this.destinationUnitId = 2;
                            this.isBillable = false;
                            this.leadAmount = 0;
                        } else if (this.destinationType === 'internal') {
                            this.activityType = 'Internal Movement';
                            this.sourceUnitId = 2;
                            this.destinationUnitId = 3;
                            this.isBillable = false;
                            this.leadAmount = 0;
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
                        this.calculateTotal();
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

                        const baseAmount = quantity * rate;
                        this.totalAmount = (baseAmount + lead).toFixed(2);
                    }
                }));
            });
        </script>
    @endpush
</x-tabler-layout>