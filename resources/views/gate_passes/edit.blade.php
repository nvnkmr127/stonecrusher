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
                    netWeight: {{ old('net_weight', $gatePass->net_weight) }},
                    transportCost: {{ old('transport_cost', $gatePass->transport_cost ?? 0) }},
                    isBillable: {{ old('transport_is_billable', $gatePass->transport_is_billable) ? 'true' : 'false' }},
                    clientId: '{{ old('client_id', $gatePass->client_id) }}'
                })">
                @csrf
                @method('PUT')
                <x-card>
                    <div class="card-body">
                        <div class="row g-3">
                            <!-- Section 1: Identification -->
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label class="form-label required">Gate Pass Number</label>
                                    <input type="text" class="form-control fw-bold bg-light" name="gate_pass_number"
                                        value="{{ old('gate_pass_number', $gatePass->gate_pass_number) }}" readonly>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label class="form-label required">Date & Time</label>
                                    <input type="datetime-local"
                                        class="form-control @error('date') is-invalid @enderror" name="date"
                                        value="{{ old('date', $gatePass->date->format('Y-m-d\TH:i')) }}" required>
                                    <x-input-error :messages="$errors->get('date')" />
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label class="form-label required">Status</label>
                                    <select class="form-select @error('status') is-invalid @enderror" name="status">
                                        <option value="pending" {{ old('status', $gatePass->status) == 'pending' ? 'selected' : '' }}>Pending</option>
                                        <option value="completed" {{ old('status', $gatePass->status) == 'completed' ? 'selected' : '' }}>Completed</option>
                                        <option value="cancelled" {{ old('status', $gatePass->status) == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                    </select>
                                    <x-input-error :messages="$errors->get('status')" />
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label class="form-label required">Vehicle Number</label>
                                    <input type="text" class="form-control bg-light" value="{{ $gatePass->vehicle->vehicle_number ?? $gatePass->manual_vehicle_number }}" readonly>
                                    <input type="hidden" name="manual_vehicle_number" value="{{ $gatePass->manual_vehicle_number }}">
                                    <input type="hidden" name="vehicle_id" value="{{ $gatePass->vehicle_id }}">
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
                                        name="client_id" x-model="clientId" @change="if(clientId) isBillable = true">
                                        <option value="">Select Client (Optional)</option>
                                        @foreach($clients as $client)
                                            <option value="{{ $client->id }}" {{ old('client_id', $gatePass->client_id) == $client->id ? 'selected' : '' }}>{{ $client->name }}</option>
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
                                            <option value="{{ $type->id }}" {{ old('metal_type_id', $gatePass->metal_type_id) == $type->id ? 'selected' : '' }}>{{ $type->name }}</option>
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
                                            class="form-control @error('net_weight') is-invalid @enderror" name="net_weight"
                                            x-model.number="netWeight" required>
                                        <span class="input-group-text">CFT</span>
                                    </div>
                                    <x-input-error :messages="$errors->get('net_weight')" />
                                </div>
                                <input type="hidden" name="gross_weight" value="{{ $gatePass->gross_weight ?? 0 }}">
                                <input type="hidden" name="tare_weight" value="{{ $gatePass->tare_weight ?? 0 }}">
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Transport Cost (₹)</label>
                                    <div class="input-group mb-2">
                                        <span class="input-group-text">₹</span>
                                        <input type="number" step="0.01"
                                            class="form-control @error('transport_cost') is-invalid @enderror"
                                            name="transport_cost" x-model.number="transportCost">
                                    </div>
                                    <label class="form-check form-check-inline">
                                        <input class="form-check-input" type="checkbox" name="transport_is_billable"
                                            value="1" id="billTransport" x-model="isBillable"
                                            {{ $gatePass->transport_is_billable ? 'checked' : '' }}>
                                        <span class="form-check-label">Bill transport to client?</span>
                                    </label>
                                    <x-input-error :messages="$errors->get('transport_cost')" />
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
                    netWeight: initial.netWeight,
                    transportCost: initial.transportCost,
                    clientId: initial.clientId,
                    isBillable: initial.isBillable,

                    init() {
                        this.$watch('clientId', (value) => {
                            if (value) {
                                this.isBillable = true;
                            }
                        });
                    }
                }));
            });
        </script>
    @endpush
</x-tabler-layout>