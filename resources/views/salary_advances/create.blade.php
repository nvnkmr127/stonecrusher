<x-tabler-layout>
    <x-slot name="header">
        <h2 class="page-title">Record Salary Advance</h2>
    </x-slot>

    <div class="row row-cards">
        <div class="col-md-6 mx-auto">
            <x-card>
                <form action="{{ route('salary-advances.store') }}" method="POST">
                    @csrf

                    <div class="mb-3">
                        <label class="form-label required">Employee</label>
                        <select name="user_id" class="form-select @error('user_id') is-invalid @enderror" required>
                            <option value="">Select Employee</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}" {{ (old('user_id') ?? $selectedUserId) == $user->id ? 'selected' : '' }}>
                                    {{ $user->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('user_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label required">Amount</label>
                        <input type="number" step="0.01" name="amount"
                            class="form-control @error('amount') is-invalid @enderror" value="{{ old('amount') }}"
                            required>
                        @error('amount') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label required">Date</label>
                        <input type="date" name="date" class="form-control @error('date') is-invalid @enderror"
                            value="{{ old('date', date('Y-m-d')) }}" required>
                        @error('date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Remarks</label>
                        <textarea name="remarks" class="form-control @error('remarks') is-invalid @enderror"
                            rows="3">{{ old('remarks') }}</textarea>
                        @error('remarks') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="d-flex">
                        <x-button variant="primary" type="submit">Save Advance</x-button>
                        <a href="{{ route('salary-advances.index') }}" class="btn btn-secondary ms-2">Cancel</a>
                    </div>
                </form>
            </x-card>
        </div>
    </div>
</x-tabler-layout>