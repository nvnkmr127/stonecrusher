<x-tabler-layout>
    <x-slot name="header">
        <h2 class="page-title">Edit Project</h2>
        <div class="page-subtitle">Update project information</div>
    </x-slot>

    <div class="row row-deck row-cards">
        <div class="col-md-8 mx-auto">
            <x-card>
                <x-slot name="header">
                    Project Information
                </x-slot>

                <form action="{{ route('projects.update', $project) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <x-form.input name="name" label="Project Name" :value="$project->name" required />

                    <div class="mb-3">
                        <label class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="is_internal" value="1" id="is_internal"
                                {{ $project->is_internal ? 'checked' : '' }} onchange="toggleClientRequired(this.checked)">
                            <span class="form-check-label">Internal Project (Our Own)</span>
                        </label>
                    </div>

                    <div id="client_selection" style="{{ $project->is_internal ? 'display: none;' : '' }}">
                        <x-form.select name="client_id" label="Client" :options="$clients->pluck('name', 'id')->toArray()"
                            :selected="$project->client_id" :required="!$project->is_internal" />
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

                    <x-form.input name="location" label="Project Location" :value="$project->location"
                        placeholder="e.g., Site Address, City" />

                    <x-form.textarea name="description" label="Description" :value="$project->description" rows="3" />

                    <x-form.input name="estimated_quantity" label="Estimated Quantity (Tons)" type="number" step="0.01"
                        :value="$project->estimated_quantity" placeholder="0.00" />

                    <div class="row">
                        <div class="col-md-6">
                            <x-form.input name="start_date" label="Start Date" type="date"
                                :value="$project->start_date?->format('Y-m-d')" />
                        </div>
                        <div class="col-md-6">
                            <x-form.input name="end_date" label="End Date" type="date"
                                :value="$project->end_date?->format('Y-m-d')" />
                        </div>
                    </div>

                    <x-form.select name="status" label="Status" :options="[
        'pending' => 'Pending',
        'active' => 'Active',
        'completed' => 'Completed',
        'cancelled' => 'Cancelled'
    ]" :selected="$project->status" required />

                    <div class="mb-3">
                        <label class="form-label">Progress (%)</label>
                        <input type="range" class="form-range" name="progress" id="progress" min="0" max="100"
                            value="{{ $project->progress ?? 0 }}"
                            oninput="document.getElementById('progressValue').textContent = this.value">
                        <div class="text-muted">Current: <span id="progressValue">{{ $project->progress ?? 0 }}</span>%
                        </div>
                    </div>

                    <hr class="my-4">

                    <div class="d-flex">
                        <x-button variant="primary" type="submit">Update Project</x-button>
                        <a href="{{ route('projects.index') }}" class="btn btn-secondary ms-2">Cancel</a>
                    </div>
                </form>
            </x-card>
        </div>
    </div>
</x-tabler-layout>