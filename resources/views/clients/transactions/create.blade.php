<x-tabler-layout>
    <x-slot name="header">
        <h2 class="page-title">
            Record Transaction for {{ $client->name }}
        </h2>
        <div class="page-subtitle">
            Current Balance: 
            <span class="{{ $client->balance >= 0 ? 'text-success' : 'text-danger' }}">
                {{ number_format(abs($client->balance), 2) }} {{ $client->balance >= 0 ? 'Cr' : 'Dr' }}
            </span>
        </div>
    </x-slot>

    <div class="row row-cards">
        <div class="col-md-6 offset-md-3">
             <x-card>
                <form action="{{ route('clients.transactions.store', $client) }}" method="POST">
                    @csrf

                    <x-form.select
                        name="transaction_type"
                        label="Transaction Type"
                        :options="['credit' => 'Credit (Advance / Payment Received)', 'debit' => 'Debit (Sale / Charge)']"
                        :selected="old('transaction_type', 'credit')"
                        required
                    />

                    <x-form.input
                        name="amount"
                        label="Amount"
                        type="number"
                        step="0.01"
                        placeholder="0.00"
                        :value="old('amount')"
                        required
                    />

                    <x-form.select
                        name="payment_mode"
                        label="Payment Mode (For Advances)"
                        :options="['Cash' => 'Cash', 'Bank Transfer' => 'Bank Transfer', 'UPI' => 'UPI', 'Check' => 'Check', 'Other' => 'Other']"
                        :selected="old('payment_mode')"
                    />

                    <x-form.input
                        name="transaction_date"
                        label="Date"
                        type="date"
                        :value="old('transaction_date', date('Y-m-d'))"
                        required
                    />

                    <x-form.input
                        name="reference_number"
                        label="Reference Number (Optional)"
                        placeholder="e.g. UPI Ref, Check #"
                        :value="old('reference_number')"
                    />

                    <x-form.textarea
                        name="description"
                        label="Description / Remarks"
                        rows="3"
                        :value="old('description')"
                    />

                    <div class="form-footer text-end">
                        <x-button type="submit" variant="primary">
                            Save Transaction
                        </x-button>
                        <a href="{{ route('clients.show', $client) }}" class="btn btn-link link-secondary">Cancel</a>
                    </div>
                </form>
             </x-card>
        </div>
    </div>
</x-tabler-layout>
