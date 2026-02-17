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
                isBillable: {{ old('client_id') ? 'true' : (old('transport_is_billable') ? 'true' : 'false') }},
                isManualVehicle: true,
                vehicles: @json($vehicles->map(fn($v) => ['number' => $v->vehicle_number, 'multiplier' => $v->transport_multiplier]))
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

                                <!-- Section 2: Order Details -->
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Client / Customer</label>
                                        <select class="form-select @error('client_id') is-invalid @enderror"
                                            name="client_id" x-model="clientId"
                                            @change="if(clientId) isBillable = true">
                                            <option value="">Select Client (Optional)</option>
                                            @foreach($clients as $client)
                                                <option value="{{ $client->id }}" {{ old('client_id') == $client->id ? 'selected' : '' }}>{{ $client->name }}</option>
                                            @endforeach
                                        </select>
                                        <x-input-error :messages="$errors->get('client_id')" />
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