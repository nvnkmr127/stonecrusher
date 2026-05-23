<x-tabler-layout>
    <x-slot name="header">
        <div class="row align-items-center mb-3">
            <div class="col">
                <div class="page-pretitle text-uppercase fw-bold text-muted" style="letter-spacing: 0.05em;">Operations</div>
                <h2 class="page-title h1 fw-bold">Quarry Operations</h2>
            </div>
            <div class="col-auto ms-auto d-print-none">
                <div class="d-flex gap-2">
                    <button class="btn btn-primary shadow-sm" data-bs-toggle="modal" data-bs-target="#addRecordModal">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 5l0 14" /><path d="M5 12l14 0" /></svg>
                        Add Record
                    </button>
                </div>
            </div>
        </div>
    </x-slot>

    <!-- Error/Success notifications -->
    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible" role="alert">
            <h4 class="alert-title fw-bold">Please correct the following errors:</h4>
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <a href="#" class="btn-close" data-bs-dismiss="alert" aria-label="close"></a>
        </div>
    @endif

    <!-- Month Filter Card -->
    <div class="card mb-4 border-0 shadow-sm">
        <div class="card-body">
            <form action="{{ route('quarry.index') }}" method="GET" class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label class="form-label small text-muted text-uppercase fw-bold" style="letter-spacing: 0.05em;">Month</label>
                    <select name="month" class="form-select">
                        @for ($m = 1; $m <= 12; $m++)
                            <option value="{{ $m }}" {{ $month == $m ? 'selected' : '' }}>
                                {{ Carbon\Carbon::create()->month($m)->format('F') }}
                            </option>
                        @endfor
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label small text-muted text-uppercase fw-bold" style="letter-spacing: 0.05em;">Year</label>
                    <select name="year" class="form-select">
                        @for ($y = Carbon\Carbon::now()->year - 5; $y <= Carbon\Carbon::now()->year + 2; $y++)
                            <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>
                                {{ $y }}
                            </option>
                        @endfor
                    </select>
                </div>
                <div class="col-md-4">
                    <button type="submit" class="btn btn-primary w-100">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><circle cx="10" cy="10" r="7" /><line x1="21" y1="21" x2="15" y2="15" /></svg>
                        Filter Operations
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- P&L Stats Widgets -->
    <div class="row row-cards mb-4">
        <div class="col-sm-6 col-lg-4">
            <div class="card card-sm border-0 shadow-sm">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-auto">
                            <span class="bg-green-lt text-green avatar">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><circle cx="12" cy="12" r="9" /><path d="M14.8 9a2 2 0 0 0 -1.8 -1h-2a2 2 0 0 0 0 4h2a2 2 0 0 1 0 4h-2a2 2 0 0 1 -1.8 -1" /><path d="M12 6v2m0 8v2" /></svg>
                            </span>
                        </div>
                        <div class="col">
                            <div class="font-weight-medium text-muted">Total Revenue</div>
                            <div class="h2 mb-0 font-weight-bold text-green">₹{{ number_format($totalRevenue, 2) }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-4">
            <div class="card card-sm border-0 shadow-sm">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-auto">
                            <span class="bg-red-lt text-red avatar">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><circle cx="12" cy="12" r="9" /><path d="M14.8 9a2 2 0 0 0 -1.8 -1h-2a2 2 0 0 0 0 4h2a2 2 0 0 1 0 4h-2a2 2 0 0 1 -1.8 -1" /><path d="M12 6v2m0 8v2" /></svg>
                            </span>
                        </div>
                        <div class="col">
                            <div class="font-weight-medium text-muted">Total Expense</div>
                            <div class="h2 mb-0 font-weight-bold text-danger">₹{{ number_format($totalExpense, 2) }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-4">
            <div class="card card-sm border-0 shadow-sm">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-auto">
                            <span class="avatar {{ $netProfitLoss >= 0 ? 'bg-teal-lt text-teal' : 'bg-orange-lt text-orange' }}">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M17 8v-3a1 1 0 0 0 -1 -1h-10a2 2 0 0 0 0 4h12a1 1 0 0 1 1 1v3m0 4v3a1 1 0 0 1 -1 1h-12a2 2 0 0 1 -2 -2v-12" /><path d="M20 12v4h-4a2 2 0 0 1 0 -4h4" /></svg>
                            </span>
                        </div>
                        <div class="col">
                            <div class="font-weight-medium text-muted">Net Profit/Loss</div>
                            <div class="h2 mb-0 font-weight-bold {{ $netProfitLoss >= 0 ? 'text-teal' : 'text-orange' }}">
                                ₹{{ number_format($netProfitLoss, 2) }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content & Sidebar -->
    <div class="row row-cards">
        <!-- Records List -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header d-flex justify-content-between align-items-center py-3 bg-transparent">
                    <h3 class="card-title fw-bold">Quarry Records</h3>
                    <span class="badge bg-blue-lt">Page {{ $records->currentPage() }} of {{ $records->lastPage() }}</span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-vcenter card-table table-striped table-hover table-premium">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Tag/Category</th>
                                    <th class="text-end">Qty</th>
                                    <th class="text-end">Rate (₹)</th>
                                    <th class="text-end">Amount (₹)</th>
                                    <th>Remarks</th>
                                    <th class="text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($records as $record)
                                    <tr>
                                        <td class="text-nowrap">{{ \Carbon\Carbon::parse($record->date)->format('d M Y') }}</td>
                                        <td>
                                            <span class="badge {{ $record->tag->type === 'revenue' ? 'bg-green-lt text-green' : 'bg-red-lt text-red' }} text-uppercase" style="font-size: 0.7rem;">
                                                {{ $record->tag->name }}
                                            </span>
                                        </td>
                                        <td class="text-end text-muted">{{ $record->quantity ? number_format($record->quantity, 2) : '-' }}</td>
                                        <td class="text-end text-muted">{{ $record->rate ? number_format($record->rate, 2) : '-' }}</td>
                                        <td class="text-end fw-bold">₹{{ number_format($record->amount, 2) }}</td>
                                        <td class="text-muted small">{{ Str::limit($record->remarks, 30) }}</td>
                                        <td class="text-center">
                                            <div class="d-flex justify-content-center gap-1">
                                                <button class="btn btn-icon btn-outline-primary btn-premium-action" 
                                                    data-bs-toggle="modal" data-bs-target="#editRecordModal"
                                                    data-id="{{ $record->id }}"
                                                    data-date="{{ $record->date->format('Y-m-d') }}"
                                                    data-tag-id="{{ $record->operational_tag_id }}"
                                                    data-quantity="{{ $record->quantity }}"
                                                    data-rate="{{ $record->rate }}"
                                                    data-amount="{{ $record->amount }}"
                                                    data-remarks="{{ $record->remarks }}">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 20h4l10.5 -10.5a1.5 1.5 0 0 0 -4 -4l-10.5 10.5v4" /><line x1="13.5" y1="6.5" x2="17.5" y2="10.5" /></svg>
                                                </button>
                                                <button class="btn btn-icon btn-outline-danger btn-premium-action"
                                                    data-bs-toggle="modal" data-bs-target="#deleteRecordModal"
                                                    data-id="{{ $record->id }}"
                                                    data-date="{{ $record->date->format('d M Y') }}"
                                                    data-tag="{{ $record->tag->name }}"
                                                    data-amount="{{ number_format($record->amount, 2) }}">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><line x1="4" y1="7" x2="20" y2="7" /><line x1="10" y1="11" x2="10" y2="17" /><line x1="14" y1="11" x2="14" y2="17" /><path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12" /><path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3" /></svg>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center text-muted py-5 bg-light-lt">
                                            <div class="py-3">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="icon text-muted mb-2 icon-lg" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><rect x="4" y="4" width="16" height="16" rx="2" /><path d="M9 9h6v6h-6z" /></svg>
                                                <p class="mb-0">No records found for {{ Carbon\Carbon::create()->month($month)->format('F') }} {{ $year }}.</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    @if($records->hasPages())
                        <div class="card-footer d-flex align-items-center justify-content-between border-0 bg-transparent py-3">
                            {{ $records->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Tags Manager Sidebar -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header py-3 bg-transparent">
                    <h3 class="card-title fw-bold">Operational Tags</h3>
                </div>
                <div class="card-body">
                    <p class="text-muted small mb-3">Define operational categories to track expenses or revenues. Deleting custom tags is only permitted if they are not active in any records.</p>
                    
                    <div class="list-group list-group-flush mb-4 border rounded-3 overflow-hidden" style="max-height: 280px; overflow-y: auto;">
                        @foreach($tags as $tag)
                            <div class="list-group-item d-flex justify-content-between align-items-center py-2 px-3 hover-light">
                                <div>
                                    <span class="fw-bold text-dark">{{ $tag->name }}</span>
                                    <br>
                                    <span class="small text-uppercase tracking-wider fw-bold {{ $tag->type === 'revenue' ? 'text-green' : 'text-danger' }}" style="font-size: 0.65rem;">
                                        {{ $tag->type }}
                                    </span>
                                </div>
                                <div>
                                    <form action="{{ route('operations.tags.destroy', $tag) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this tag?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-icon btn-link text-danger p-0" title="Delete Tag">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="20" height="20" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><line x1="18" y1="6" x2="6" y2="18" /><line x1="6" y1="6" x2="18" y2="18" /></svg>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="hr-text text-muted my-3">Create New Tag</div>

                    <form action="{{ route('operations.tags.store', $unit) }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label small text-muted text-uppercase fw-bold">Tag Name</label>
                            <input type="text" name="name" class="form-control" placeholder="e.g. Borewells Subcontract" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small text-muted text-uppercase fw-bold">Tag Type</label>
                            <div class="form-selectgroup form-selectgroup-boxes d-flex">
                                <label class="form-selectgroup-item flex-fill">
                                    <input type="radio" name="type" value="expense" class="form-selectgroup-input" checked>
                                    <span class="form-selectgroup-label d-flex align-items-center justify-content-center p-2">
                                        <span class="form-selectgroup-label-content">Expense</span>
                                    </span>
                                </label>
                                <label class="form-selectgroup-item flex-fill">
                                    <input type="radio" name="type" value="revenue" class="form-selectgroup-input">
                                    <span class="form-selectgroup-label d-flex align-items-center justify-content-center p-2">
                                        <span class="form-selectgroup-label-content">Revenue</span>
                                    </span>
                                </label>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary w-100 shadow-sm">
                            Add Tag
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Record Modal -->
    <div class="modal modal-blur fade" id="addRecordModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content border-0 shadow-lg">
                <form action="{{ route('operations.records.store', $unit) }}" method="POST">
                    @csrf
                    <div class="modal-header bg-dark text-white py-3 border-0">
                        <h5 class="modal-title fw-bold">Add Operational Record - Quarry</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body py-4">
                        <div class="mb-3">
                            <label class="form-label required">Date</label>
                            <input type="date" name="date" class="form-control" value="{{ Carbon\Carbon::now()->format('Y-m-d') }}" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label required">Tag / Category</label>
                            <select name="operational_tag_id" class="form-select" required>
                                <option value="" disabled selected>Select category...</option>
                                @foreach($tags as $tag)
                                    <option value="{{ $tag->id }}">{{ $tag->name }} ({{ ucfirst($tag->type) }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Quantity</label>
                                <input type="number" step="0.01" name="quantity" id="add_quantity" class="form-control" placeholder="Optional">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Rate (₹)</label>
                                <input type="number" step="0.01" name="rate" id="add_rate" class="form-control" placeholder="Optional">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label required">Amount (₹)</label>
                            <input type="number" step="0.01" name="amount" id="add_amount" class="form-control" placeholder="Enter total amount" required>
                            <span class="form-hint small text-muted">Amount auto-calculates if Qty & Rate are entered.</span>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Remarks</label>
                            <textarea name="remarks" class="form-control" rows="3" placeholder="Enter remarks..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer border-top-0 py-3 bg-light-lt">
                        <button type="button" class="btn btn-link link-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary ms-auto shadow-sm">Save Record</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Record Modal -->
    <div class="modal modal-blur fade" id="editRecordModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content border-0 shadow-lg">
                <form id="editRecordForm" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-header bg-dark text-white py-3 border-0">
                        <h5 class="modal-title fw-bold">Edit Operational Record</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body py-4">
                        <div class="mb-3">
                            <label class="form-label required">Date</label>
                            <input type="date" name="date" id="edit_date" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label required">Tag / Category</label>
                            <select name="operational_tag_id" id="edit_tag_id" class="form-select" required>
                                @foreach($tags as $tag)
                                    <option value="{{ $tag->id }}">{{ $tag->name }} ({{ ucfirst($tag->type) }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Quantity</label>
                                <input type="number" step="0.01" name="quantity" id="edit_quantity" class="form-control">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Rate (₹)</label>
                                <input type="number" step="0.01" name="rate" id="edit_rate" class="form-control">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label required">Amount (₹)</label>
                            <input type="number" step="0.01" name="amount" id="edit_amount" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Remarks</label>
                            <textarea name="remarks" id="edit_remarks" class="form-control" rows="3"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer border-top-0 py-3 bg-light-lt">
                        <button type="button" class="btn btn-link link-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary ms-auto shadow-sm">Update Record</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Delete Record Modal -->
    <div class="modal modal-blur fade" id="deleteRecordModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-sm modal-dialog-centered" role="document">
            <div class="modal-content border-0 shadow-lg">
                <form id="deleteRecordForm" method="POST">
                    @csrf
                    @method('DELETE')
                    <div class="modal-body text-center py-4">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon mb-2 text-danger icon-lg" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 9v2m0 4v.01" /><path d="M5 19h14a2 2 0 0 0 1.84 -2.75l-7.1 -12.25a2 2 0 0 0 -3.5 0l-7.1 12.25a2 2 0 0 0 1.75 2.75" /></svg>
                        <h3 class="fw-bold">Are you sure?</h3>
                        <div class="text-muted">
                            Do you really want to delete the <strong id="delete_tag_name" class="text-dark"></strong> entry of 
                            <strong id="delete_amount" class="text-dark"></strong> from <strong id="delete_date_display" class="text-dark"></strong>?
                        </div>
                    </div>
                    <div class="modal-footer border-top-0 py-3 bg-light-lt">
                        <div class="w-100">
                            <div class="row">
                                <div class="col">
                                    <a href="#" class="btn btn-link link-secondary w-100" data-bs-dismiss="modal">Cancel</a>
                                </div>
                                <div class="col">
                                    <button type="submit" class="btn btn-danger w-100 shadow-sm">Delete</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Auto calculate amount from quantity and rate
            function setupAutoCalc(qtyId, rateId, amtId) {
                const qtyEl = document.getElementById(qtyId);
                const rateEl = document.getElementById(rateId);
                const amtEl = document.getElementById(amtId);

                if (qtyEl && rateEl && amtEl) {
                    const calc = () => {
                        const q = parseFloat(qtyEl.value) || 0;
                        const r = parseFloat(rateEl.value) || 0;
                        if (q > 0 && r > 0) {
                            amtEl.value = (q * r).toFixed(2);
                        }
                    };
                    qtyEl.addEventListener('input', calc);
                    rateEl.addEventListener('input', calc);
                }
            }

            setupAutoCalc('add_quantity', 'add_rate', 'add_amount');
            setupAutoCalc('edit_quantity', 'edit_rate', 'edit_amount');

            // Populate Edit Modal
            const editRecordModal = document.getElementById('editRecordModal');
            if (editRecordModal) {
                editRecordModal.addEventListener('show.bs.modal', function (event) {
                    const button = event.relatedTarget;
                    const id = button.getAttribute('data-id');
                    const date = button.getAttribute('data-date');
                    const tagId = button.getAttribute('data-tag-id');
                    const quantity = button.getAttribute('data-quantity');
                    const rate = button.getAttribute('data-rate');
                    const amount = button.getAttribute('data-amount');
                    const remarks = button.getAttribute('data-remarks');

                    const form = editRecordModal.querySelector('#editRecordForm');
                    form.action = `/operations/records/${id}`;

                    editRecordModal.querySelector('#edit_date').value = date;
                    editRecordModal.querySelector('#edit_tag_id').value = tagId;
                    editRecordModal.querySelector('#edit_quantity').value = quantity || '';
                    editRecordModal.querySelector('#edit_rate').value = rate || '';
                    editRecordModal.querySelector('#edit_amount').value = amount;
                    editRecordModal.querySelector('#edit_remarks').value = remarks || '';
                });
            }

            // Populate Delete Modal
            const deleteRecordModal = document.getElementById('deleteRecordModal');
            if (deleteRecordModal) {
                deleteRecordModal.addEventListener('show.bs.modal', function (event) {
                    const button = event.relatedTarget;
                    const id = button.getAttribute('data-id');
                    const date = button.getAttribute('data-date');
                    const tagName = button.getAttribute('data-tag');
                    const amount = button.getAttribute('data-amount');

                    const form = deleteRecordModal.querySelector('#deleteRecordForm');
                    form.action = `/operations/records/${id}`;

                    deleteRecordModal.querySelector('#delete_tag_name').textContent = tagName;
                    deleteRecordModal.querySelector('#delete_amount').textContent = '₹' + amount;
                    deleteRecordModal.querySelector('#delete_date_display').textContent = date;
                });
            }
        });
    </script>
    @endpush
</x-tabler-layout>
