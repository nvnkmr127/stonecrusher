<x-tabler-layout>
    <x-slot name="header">
        <x-page-header title="Edit Diesel Stock"
            subtitle="Update stock record for {{ $dieselStock->date->format('d M, Y') }}" :breadcrumbs="[
        ['label' => 'Dashboard', 'route' => 'dashboard'],
        ['label' => 'Diesel Stock', 'route' => 'diesel-stocks.index'],
        ['label' => 'Edit Record', 'active' => true],
    ]" />
    </x-slot>

    <div class="row row-cards">
        <div class="col-md-6">
            <form action="{{ route('diesel-stocks.update', $dieselStock) }}" method="POST">
                @csrf
                @method('PUT')
                <x-card>
                    <div class="card-body" x-data="{ 
                        opening: {{ old('opening_liters', $dieselStock->opening_liters) }}, 
                        purchased: {{ old('purchased_liters', $dieselStock->purchased_liters) }},
                        closing: {{ old('closing_liters', $dieselStock->closing_liters) }},
                        get total() { return parseFloat(this.opening || 0) + parseFloat(this.purchased || 0) },
                        get consumed() { return this.total - parseFloat(this.closing || 0) }
                    }">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label required">Date</label>
                                    <input type="date" name="date"
                                        value="{{ old('date', $dieselStock->date->toDateString()) }}"
                                        class="form-control @error('date') is-invalid @enderror" required>
                                    <x-input-error :messages="$errors->get('date')" />
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Location (Tank)</label>
                                    <select name="operational_unit_id"
                                        class="form-select @error('operational_unit_id') is-invalid @enderror">
                                        <option value="">Main Tank</option>
                                        @foreach($locations as $loc)
                                            <option value="{{ $loc->id }}" {{ old('operational_unit_id', $dieselStock->operational_unit_id) == $loc->id ? 'selected' : '' }}>
                                                {{ $loc->name }}</option>
                                        @endforeach
                                    </select>
                                    <x-input-error :messages="$errors->get('operational_unit_id')" />
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label required">Opening Stock (Liters)</label>
                                    <input type="number" step="0.01" name="opening_liters" x-model.number="opening"
                                        class="form-control @error('opening_liters') is-invalid @enderror" required>
                                    <x-input-error :messages="$errors->get('opening_liters')" />
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label required">Purchased/Refilled (Liters)</label>
                                    <input type="number" step="0.01" name="purchased_liters" x-model.number="purchased"
                                        class="form-control @error('purchased_liters') is-invalid @enderror" required>
                                    <x-input-error :messages="$errors->get('purchased_liters')" />
                                </div>
                            </div>

                            <div class="col-12 py-2 bg-light-subtle rounded border border-dashed text-center">
                                <div class="h4 mb-1">Total Available</div>
                                <div class="h2 text-primary mb-0" x-text="total.toFixed(2) + ' L'">0.00 L</div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label required">Closing Balance (Liters)</label>
                                    <input type="number" step="0.01" name="closing_liters" x-model.number="closing"
                                        class="form-control @error('closing_liters') is-invalid @enderror" required>
                                    <x-input-error :messages="$errors->get('closing_liters')" />
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Calculated Consumption</label>
                                    <div class="h3 py-2 text-danger" x-text="consumed.toFixed(2) + ' L'">0.00 L</div>
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="mb-3">
                                    <label class="form-label">Remarks</label>
                                    <textarea name="remarks" class="form-control"
                                        rows="2">{{ old('remarks', $dieselStock->remarks) }}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer text-end">
                        <button type="submit" class="btn btn-primary">Update Stock Record</button>
                    </div>
                </x-card>
            </form>
        </div>
    </div>
</x-tabler-layout>