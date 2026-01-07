<x-tabler-layout>
    <x-slot name="header">
        <h2 class="page-title">
            {{ __('Perform Daily Closing') }}
        </h2>
    </x-slot>

    <div class="row row-cards">
        <div class="col-md-8 mx-auto">
            <div class="card">
                <div class="card-body">

                    <form action="{{ route('daily-closings.create') }}" method="GET" class="mb-4">
                        <div class="row g-2 align-items-end">
                            <div class="col">
                                <label class="form-label">{{ __('Select Date') }}</label>
                                <input type="date" name="date" value="{{ $date }}" class="form-control" required>
                            </div>
                            <div class="col-auto">
                                <button type="submit" class="btn btn-primary">{{ __('Fetch Computations') }}</button>
                            </div>
                        </div>
                    </form>

                    <div class="card bg-layout-secondary mb-4">
                        <div class="card-body">
                            <h3 class="card-title">Summary for {{ \Carbon\Carbon::parse($date)->format('d M Y') }}</h3>
                            <div class="row g-3">
                                <div class="col-6">
                                    <div class="text-secondary small">Total Sales</div>
                                    <div class="h2 mb-0 text-primary">{{ number_format($totals['total_sales'], 2) }}
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="text-secondary small">Total Collections</div>
                                    <div class="h2 mb-0 text-success">{{ number_format($totals['total_cash'], 2) }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <form action="{{ route('daily-closings.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="date" value="{{ $date }}">

                        <div class="mb-3">
                            <label class="form-label">{{ __('Closing Notes') }}</label>
                            <textarea name="notes" class="form-control" rows="3"></textarea>
                        </div>

                        <div class="mb-4">
                            <label class="form-check">
                                <input type="checkbox" name="confirm_closing" class="form-check-input" required>
                                <span class="form-check-label">
                                    I verify these totals are correct and I want to close the day.
                                    <span class="d-block text-danger fw-bold small">This action will LOCK data for this
                                        date.</span>
                                </span>
                            </label>
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('daily-closings.index') }}" class="btn btn-link link-secondary">Cancel</a>
                            <button type="submit" class="btn btn-danger">{{ __('Close Day') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-tabler-layout>