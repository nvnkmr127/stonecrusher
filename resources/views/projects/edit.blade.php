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

                <form action="{{ route('projects.update', $project) }}" method="POST" x-data="{ isInternal: {{ old('is_internal', $project->is_internal) ? 'true' : 'false' }} }">
                    @csrf
                    @method('PUT')

                    <x-form.input name="name" label="Project Name" :value="$project->name" required />

                    <div class="mb-3">
                        <label class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="is_internal" value="1" id="is_internal"
                                x-model="isInternal">
                            <span class="form-check-label">Internal Project (Our Own)</span>
                        </label>
                    </div>

                    <div id="client_selection" x-show="!isInternal" x-transition>
                        <x-form.select name="client_id" label="Client" :options="$clients->pluck('name', 'id')->toArray()"
                            :selected="$project->client_id" x-bind:required="!isInternal" x-bind:disabled="isInternal" />
                    </div>

                    <x-form.input name="location" label="Project Location" :value="$project->location"
                        placeholder="e.g., Site Address, City" />

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