<x-tabler-layout title="Edit Salary Advance">
    <div class="page-header d-print-none mb-4">
        <div class="container-xl">
            <div class="row g-2 align-items-center">
                <div class="col">
                    <h2 class="page-title fw-bold text-primary">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-edit me-2" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                            <path d="M9 7h-3a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-3" />
                            <path d="M9 15h3l8.5 -8.5a1.5 1.5 0 0 0 -3 -3l-8.5 8.5v3" />
                            <path d="M16 5l3 3" />
                        </svg>
                        Edit Salary Advance
                    </h2>
                    <div class="text-muted">Modify previously recorded salary advance.</div>
                </div>
                <div class="col-auto ms-auto d-print-none">
                    <div class="btn-list">
                        <a href="{{ route('salary-advances.index') }}" class="btn btn-secondary d-none d-sm-inline-block shadow-sm">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-arrow-left" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                <path d="M5 12l14 0"></path>
                                <path d="M5 12l6 6"></path>
                                <path d="M5 12l6 -6"></path>
                            </svg>
                            Back to List
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="page-body">
        <div class="container-xl">
            <div class="row row-cards justify-content-center">
                <div class="col-md-8">
                    <form action="{{ route('salary-advances.update', $salaryAdvance->id) }}" method="POST" class="card shadow-sm border-0">
                        @csrf
                        @method('PUT')
                        <div class="card-header bg-white py-3">
                            <h3 class="card-title fw-bold">Advance Details</h3>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <label class="form-label required fw-bold">Employee</label>
                                        <div class="input-icon">
                                            <span class="input-icon-addon">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-user" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                                    <path d="M8 7a4 4 0 1 0 8 0a4 4 0 0 0 -8 0" />
                                                    <path d="M6 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2" />
                                                </svg>
                                            </span>
                                            <select name="user_id" class="form-select @error('user_id') is-invalid @enderror" required>
                                                <option value="">Select an employee...</option>
                                                @foreach($users as $user)
                                                    <option value="{{ $user->id }}" {{ (old('user_id', $salaryAdvance->user_id) == $user->id) ? 'selected' : '' }}>
                                                        {{ $user->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        @error('user_id') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label class="form-label required fw-bold">Amount</label>
                                        <div class="input-icon">
                                            <span class="input-icon-addon">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-currency-rupee" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                                    <path d="M18 5h-11h3a4 4 0 0 1 0 8h-3l6 6"></path>
                                                    <path d="M7 9l11 0"></path>
                                                </svg>
                                            </span>
                                            <input type="number" step="0.01" name="amount"
                                                class="form-control @error('amount') is-invalid @enderror" placeholder="0.00" value="{{ old('amount', $salaryAdvance->amount) }}"
                                                required>
                                        </div>
                                        @error('amount') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                                        <div class="form-hint">Amount to be disbursed.</div>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label class="form-label required fw-bold">Date</label>
                                        <div class="input-icon">
                                            <span class="input-icon-addon">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-calendar-event" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                                    <path d="M4 5m0 2a2 2 0 0 1 2 -2h12a2 2 0 0 1 2 2v12a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2z" />
                                                    <path d="M16 3l0 4" />
                                                    <path d="M8 3l0 4" />
                                                    <path d="M4 11l16 0" />
                                                    <path d="M8 15h2v2h-2z" />
                                                </svg>
                                            </span>
                                            <input type="date" name="date" class="form-control @error('date') is-invalid @enderror"
                                                value="{{ old('date', $salaryAdvance->date->format('Y-m-d')) }}" required>
                                        </div>
                                        @error('date') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Payment Mode</label>
                                        <div class="input-icon">
                                            <span class="input-icon-addon">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-wallet" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                                    <path d="M17 8v-3a1 1 0 0 0 -1 -1h-10a2 2 0 0 0 0 4h12a1 1 0 0 1 1 1v3m0 4v3a1 1 0 0 1 -1 1h-12a2 2 0 0 1 -2 -2v-12" />
                                                    <path d="M20 12v4h-4a2 2 0 0 1 0 -4h4" />
                                                </svg>
                                            </span>
                                            <select name="payment_mode" class="form-select @error('payment_mode') is-invalid @enderror">
                                                <option value="">Select Mode</option>
                                                <option value="Cash" {{ old('payment_mode', $salaryAdvance->payment_mode) == 'Cash' ? 'selected' : '' }}>Cash</option>
                                                <option value="Bank Transfer" {{ old('payment_mode', $salaryAdvance->payment_mode) == 'Bank Transfer' ? 'selected' : '' }}>Bank Transfer</option>
                                                <option value="UPI" {{ old('payment_mode', $salaryAdvance->payment_mode) == 'UPI' ? 'selected' : '' }}>UPI</option>
                                                <option value="Cheque" {{ old('payment_mode', $salaryAdvance->payment_mode) == 'Cheque' ? 'selected' : '' }}>Cheque</option>
                                            </select>
                                        </div>
                                        @error('payment_mode') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                                    </div>
                                </div>

                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Remarks</label>
                                        <textarea name="remarks" class="form-control @error('remarks') is-invalid @enderror"
                                            rows="4" placeholder="Optional: Reason for the advance...">{{ old('remarks', $salaryAdvance->remarks) }}</textarea>
                                        @error('remarks') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer bg-light text-end py-3">
                            <div class="d-flex">
                                <a href="{{ route('salary-advances.index') }}" class="btn btn-link link-secondary">Cancel</a>
                                <button type="submit" class="btn btn-primary ms-auto shadow-sm">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-device-floppy me-2" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                        <path d="M6 4h10l4 4v10a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2v-12a2 2 0 0 1 2 -2" />
                                        <path d="M12 14m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0" />
                                        <path d="M14 4l0 4l-6 0l0 -4" />
                                    </svg>
                                    Update Advance
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-tabler-layout>
