<x-tabler-layout>
    <x-slot name="header">
        <h2 class="page-title">Create Project</h2>
        <div class="page-subtitle">Add a new project</div>
    </x-slot>

    <div class="row row-deck row-cards">
        <div class="col-md-8 mx-auto">
            <x-card>
                <x-slot name="header">
                    Project Information
                </x-slot>

                <form action="{{ route('projects.store') }}" method="POST">
                    @csrf

                    <x-form.input name="name" label="Project Name" required />

                    <div class="mb-3">
                        <label class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="is_internal" value="1" id="is_internal"
                                onchange="toggleClientRequired(this.checked)">
                            <span class="form-check-label">Internal Project (Our Own)</span>
                        </label>
                    </div>

                    <div id="client_selection">
                        <x-form.select name="client_id" label="Client" :options="$clients->pluck('name', 'id')->toArray()"
                            required />
                    </div>

                    <script>
                        function toggleClientRequired(isInternal) {
                            const clientSelect = document.querySelector('select[name="client_id"]');
                            const clientContainer = document.getElementById('client_selection');
                            if (isInternal) {
                                clientSelect.value = '';
                                clientSelect.removeAttribute('required');
                                clientContainer.style.display = 'none';
                            } else {
                                clientSelect.setAttribute('required', 'required');
                                clientContainer.style.display = 'block';
                            }
                        }
                    </script>

                    <x-form.address name="location" label="Project Location" placeholder="e.g., Site Address, City" />

                    <x-form.textarea name="description" label="Description" rows="3" />

                    <x-form.input name="estimated_quantity" label="Estimated Quantity (Tons)" type="number" step="0.01"
                        placeholder="0.00" />

                    <div class="row">
                        <div class="col-md-6">
                            <x-form.input name="start_date" label="Start Date" type="date" />
                        </div>
                        <div class="col-md-6">
                            <x-form.input name="end_date" label="End Date" type="date" />
                        </div>
                    </div>

                    <x-form.select name="status" label="Status" :options="[
        'pending' => 'Pending',
        'active' => 'Active',
        'completed' => 'Completed',
        'cancelled' => 'Cancelled'
    ]" :selected="'pending'" required />

                    <div class="mb-3">
                        <label class="form-label">Progress (%)</label>
                        <input type="range" class="form-range" name="progress" id="progress" min="0" max="100" value="0"
                            oninput="document.getElementById('progressValue').textContent = this.value">
                        <div class="text-muted">Current: <span id="progressValue">0</span>%</div>
                    </div>

                    <hr class="my-4">

                    <div class="d-flex">
                        <x-button variant="primary" type="submit">Create Project</x-button>
                        <a href="{{ route('projects.index') }}" class="btn btn-secondary ms-2">Cancel</a>
                    </div>
                </form>
            </x-card>
        </div>
    </div>
</x-tabler-layout>