<x-tabler-layout title="Add Employee">
    <x-slot name="header">
        <div class="row align-items-center">
            <div class="col">
                <h2 class="page-title">Add Employee</h2>
                <div class="page-subtitle">Create a new employee profile with salary details and role definition</div>
            </div>
            <div class="col-auto">
                <a href="{{ route('employees.index') }}" class="btn btn-secondary">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24"
                        stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round"
                        stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                        <path d="M9 11l-4 4l4 4m-4 -4h11a4 4 0 0 0 0 -8h-1" />
                    </svg>
                    Back to List
                </a>
            </div>
        </div>
    </x-slot>

    <div class="row row-cards justify-content-center">
        <div class="col-md-8">
            <x-card>
                <form method="POST" action="{{ route('employees.store') }}">
                    @csrf

                    <div class="row">
                        <div class="col-md-6">
                            <x-form.input name="name" label="Employee Name" :value="old('name')" required />
                        </div>
                        <div class="col-md-6">
                            <x-form.select name="role" label="Role" :options="$roles" :selected="old('role')" required />
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-md-6">
                            <x-form.input name="base_salary" label="Base Monthly Salary (₹)" type="number" step="0.01" :value="old('base_salary', '0.00')" />
                        </div>
                        <div class="col-md-6">
                            <x-form.input name="daily_rate" label="Daily Rate (₹)" type="number" step="0.01" :value="old('daily_rate', '0.00')" />
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Operational Unit (Quarry/Crusher)</label>
                                <select name="operational_unit_id" class="form-select @error('operational_unit_id') is-invalid @enderror">
                                    <option value="">None (Office/External)</option>
                                    @foreach($operationalUnits as $unit)
                                        <option value="{{ $unit->id }}" {{ old('operational_unit_id') == $unit->id ? 'selected' : '' }}>
                                            {{ $unit->name }} ({{ ucfirst($unit->type) }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('operational_unit_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Link User Account (Optional)</label>
                                <select name="user_id" class="form-select @error('user_id') is-invalid @enderror">
                                    <option value="">None (No login access)</option>
                                    @foreach($users as $user)
                                        <option value="{{ $user->id }}" {{ old('user_id') == $user->id ? 'selected' : '' }}>
                                            {{ $user->name }} ({{ $user->email }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('user_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>
                    </div>

                    <div class="mb-3 mt-3">
                        <label class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                            <span class="form-check-label">Active Status</span>
                        </label>
                    </div>

                    <div class="card-footer text-end mt-4">
                        <x-button type="submit" variant="primary">
                            {{ __('Save Employee') }}
                        </x-button>
                        <a href="{{ route('employees.index') }}" class="btn btn-link link-secondary">
                            {{ __('Cancel') }}
                        </a>
                    </div>
                </form>
            </x-card>
        </div>
    </div>
</x-tabler-layout>
