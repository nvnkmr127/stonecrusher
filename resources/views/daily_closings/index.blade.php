<x-tabler-layout>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center">
            <h2 class="page-title">
                {{ __('Daily Closings') }}
            </h2>
            @if(auth()->user()->hasRole('admin') || auth()->user()->hasRole('manager'))
                <a href="{{ route('daily-closings.create') }}" class="btn btn-primary">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24"
                        stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                        <path d="M12 5l0 14" />
                        <path d="M5 12l14 0" />
                    </svg>
                    Perform Closing
                </a>
            @endif
        </div>
    </x-slot>

    <div class="row row-cards">
        <div class="col-12">
            <div class="card">
                <div class="card-body border-bottom py-3">

                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible" role="alert">
                            {{ session('success') }}
                            <a href="#" class="btn-close" data-bs-dismiss="alert" aria-label="close"></a>
                        </div>
                    @endif
                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible" role="alert">
                            {{ session('error') }}
                            <a href="#" class="btn-close" data-bs-dismiss="alert" aria-label="close"></a>
                        </div>
                    @endif

                    <div class="table-responsive">
                        <table class="table table-vcenter card-table">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th class="text-end">Total Sales</th>
                                    <th class="text-end">Total Collections</th>
                                    <th>Closed By</th>
                                    <th>Notes</th>
                                    <th class="text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($closings as $closing)
                                    <tr>
                                        <td>{{ $closing->date->format('d M Y') }}</td>
                                        <td class="text-end">{{ number_format($closing->total_sales, 2) }}</td>
                                        <td class="text-end">{{ number_format($closing->total_cash, 2) }}</td>
                                        <td>{{ $closing->closedBy->name ?? 'N/A' }}</td>
                                        <td>{{ Str::limit($closing->notes, 30) }}</td>
                                        <td class="text-center">
                                            @if(auth()->user()->hasRole('admin'))
                                                @if($closing->status === 'closed')
                                                    <button type="button" class="btn btn-outline-danger btn-sm"
                                                        data-bs-toggle="modal" data-bs-target="#reopenModal"
                                                        data-date="{{ $closing->date->format('Y-m-d') }}"
                                                        data-action="{{ route('daily-closings.reopen', $closing) }}">
                                                        Reopen
                                                    </button>
                                                @else
                                                    <span class="badge bg-secondary-lt">Reopened</span>
                                                @endif
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center text-muted">No closings found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $closings->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Reopen Modal (Bootstrap) -->
    <div class="modal modal-blur fade" id="reopenModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <form id="reopenForm" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">Reopen Day</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p class="text-secondary">Provide a reason to reopen <span id="modalDate"
                                class="fw-bold"></span>.</p>
                        <div class="mb-3">
                            <label class="form-label">Reason</label>
                            <textarea name="reason" class="form-control" rows="3" placeholder="Reason for reopening..."
                                required minlength="5"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-link link-secondary"
                            data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger ms-auto">Confirm Reopen</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var reopenModal = document.getElementById('reopenModal');
            reopenModal.addEventListener('show.bs.modal', function (event) {
                var button = event.relatedTarget;
                var date = button.getAttribute('data-date');
                var action = button.getAttribute('data-action');

                var modalDate = reopenModal.querySelector('#modalDate');
                var modalForm = reopenModal.querySelector('#reopenForm');

                modalDate.textContent = date;
                modalForm.action = action;
            });
        });
    </script>
</x-tabler-layout>