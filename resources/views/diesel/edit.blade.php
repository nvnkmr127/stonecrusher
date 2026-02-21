<x-tabler-layout>
    <x-slot name="header">
        <x-page-header title="Edit Diesel Issue" subtitle="Update fuel issue record" :breadcrumbs="[
        ['label' => 'Dashboard', 'route' => 'dashboard'],
        ['label' => 'Diesel Issues', 'route' => 'diesel.index'],
        ['label' => 'Edit Issue', 'active' => true],
    ]" />
    </x-slot>
 
    <div class="row row-cards">
        <div class="col-md-8 mx-auto">
            <x-card>
                <form action="{{ route('diesel.update', $diesel) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label required">Date</label>
                                <input type="date" name="date" value="{{ old('date', $diesel->date->format('Y-m-d')) }}"
                                    class="form-control @error('date') is-invalid @enderror" required>
                                @error('date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
 
                            <div class="col-md-6 mb-3">
                                <label class="form-label required">Operational Unit (Quarry / Crusher)</label>
                                <select name="operational_unit_id" id="operational_unit_id"
                                    class="form-select @error('operational_unit_id') is-invalid @enderror" required>
                                    <option value="">Select Unit</option>
                                    @foreach($locations as $location)
                                        <option value="{{ $location->id }}" {{ old('operational_unit_id', $diesel->operational_unit_id) == $location->id ? 'selected' : '' }}>
                                            {{ $location->name }} ({{ $location->code }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('operational_unit_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
 
                            <div class="col-md-6 mb-3">
                                <label class="form-label required">Vehicle or Machine ID</label>
                                <select name="vehicle_id" id="vehicle_id" class="form-select @error('vehicle_id') is-invalid @enderror"
                                    required>
                                    <option value="">Select Vehicle / Machine</option>
                                    @foreach($vehicles as $vehicle)
                                        <option value="{{ $vehicle->id }}" 
                                            data-location="{{ $vehicle->operational_unit_id }}"
                                            {{ old('vehicle_id', $diesel->vehicle_id) == $vehicle->id ? 'selected' : '' }}>
                                            {{ $vehicle->registration_number }} ({{ $vehicle->type }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('vehicle_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
 
                            <div class="col-md-6 mb-3">
                                <label class="form-label required">Liters Issued</label>
                                <div class="input-group">
                                    <input type="number" step="0.01" name="liters"
                                        value="{{ old('liters', $diesel->liters) }}"
                                        class="form-control @error('liters') is-invalid @enderror" placeholder="0.00"
                                        required>
                                    <span class="input-group-text">L</span>
                                </div>
                                @error('liters') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
 
                            <div class="col-md-6 mb-3">
                                <label class="form-label required">Driver</label>
                                <input type="text" name="driver_name"
                                    value="{{ old('driver_name', $diesel->driver_name) }}"
                                    class="form-control @error('driver_name') is-invalid @enderror"
                                    placeholder="Enter driver name" required>
                                @error('driver_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
 
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Linked Trip ID (Optional)</label>
                                <select name="gate_pass_id" class="form-select @error('gate_pass_id') is-invalid @enderror">
                                    <option value="">None</option>
                                    @php
                                        $recentGPs = \App\Models\GatePass::where('vehicle_id', $diesel->vehicle_id)->latest('date')->take(10)->get();
                                        if ($diesel->gate_pass_id && !$recentGPs->contains('id', $diesel->gate_pass_id)) {
                                            $recentGPs->prepend($diesel->gatePass);
                                        }
                                    @endphp
                                    @foreach($recentGPs as $gp)
                                        <option value="{{ $gp->id }}" {{ old('gate_pass_id', $diesel->gate_pass_id) == $gp->id ? 'selected' : '' }}>
                                            GP #{{ $gp->gate_pass_number }} ({{ $gp->date->format('d/m') }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('gate_pass_id') <div class="invalid-feedback text-danger small">{{ $message }}</div> @enderror
                            </div>
 
                            <div class="col-12 mb-3">
                                <label class="form-label required">Work Type</label>
                                <div class="form-selectgroup">
                                    @php $workTypes = ['Plant', 'JCB', 'Loading', 'Transport', 'Excavator', 'Other']; @endphp
                                    @foreach($workTypes as $type)
                                        <label class="form-selectgroup-item">
                                            <input type="radio" name="work_type" value="{{ $type }}"
                                                class="form-selectgroup-input" {{ old('work_type', $diesel->work_type) == $type ? 'checked' : '' }}>
                                            <span class="form-selectgroup-label">{{ $type }}</span>
                                        </label>
                                    @endforeach
                                </div>
                                @error('work_type') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>
                        </div>
                    </div>
                    <div class="card-footer text-end">
                        <div class="d-flex">
                            <a href="{{ route('diesel.index') }}" class="btn btn-link">Cancel</a>
                            <button type="submit" class="btn btn-primary ms-auto">Update Issue</button>
                        </div>
                    </div>
                </form>
            </x-card>
        </div>
    </div>

    @push('scripts')
    <script>
        document.getElementById('vehicle_id').addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            const locationId = selectedOption.getAttribute('data-location');
            
            if (locationId && locationId !== "") {
                const select = document.getElementById('operational_unit_id');
                if (select) select.value = locationId;
            }
        });
    </script>
    @endpush
</x-tabler-layout>