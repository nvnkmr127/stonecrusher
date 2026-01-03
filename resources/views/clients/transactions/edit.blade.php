<x-tabler-layout>
    <x-slot name="header">
        <h2 class="page-title">
            Edit Transaction for {{ $client->name }}
        </h2>
    </x-slot>

    <div class="row row-cards">
        <div class="col-md-6 offset-md-3">
             <x-card>
                <form action="{{ route('clients.transactions.update', [$client, $transaction]) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label class="form-label">Transaction Type</label>
                        <input type="text" class="form-control" value="{{ ucfirst($transaction->transaction_type) }}" disabled>
                        <small class="form-hint">Type cannot be changed.</small>
                    </div>

                    <x-form.input
                        name="amount"
                        label="Amount"
                        type="number"
                        step="0.01"
                        :value="old('amount', $transaction->amount)"
                        required
                    />

                    @if($transaction->transaction_type === 'credit')
                    <x-form.select
                        name="payment_mode"
                        label="Payment Mode"
                        :options="['Cash' => 'Cash', 'Bank Transfer' => 'Bank Transfer', 'UPI' => 'UPI', 'Check' => 'Check', 'Other' => 'Other']"
                        :selected="old('payment_mode', $transaction->payment_mode)"
                    />
                    @endif

                    <x-form.input
                        name="transaction_date"
                        label="Date"
                        type="date"
                        :value="old('transaction_date', $transaction->transaction_date->format('Y-m-d'))"
                        required
                    />

                    <x-form.input
                        name="reference_number"
                        label="Reference Number"
                        :value="old('reference_number', $transaction->reference_number)"
                    />

                    <x-form.textarea
                        name="description"
                        label="Description / Remarks"
                        rows="3"
                        :value="old('description', $transaction->description)"
                    />

                    <x-form.textarea
                        name="edit_reason"
                        label="Reason for Edit (Mandatory)"
                        rows="2"
                        placeholder="Explain why you are modifying this record..."
                        required
                    />

                    <div class="form-footer text-end">
                        <x-button type="submit" variant="primary">
                            Update Transaction
                        </x-button>
                        <a href="{{ route('clients.show', $client) }}" class="btn btn-link link-secondary">Cancel</a>
                    </div>
                </form>
             </x-card>
        </div>
    </div>
</x-tabler-layout>
