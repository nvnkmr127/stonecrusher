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

                <form action="{{ route('projects.store') }}" method="POST" x-data="{ isInternal: {{ old('is_internal', 0) ? 'true' : 'false' }} }">
                    @csrf

                    <x-form.input name="name" label="Project Name" required />

                    <div class="mb-3">
                        <label class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="is_internal" value="1" id="is_internal"
                                x-model="isInternal">
                            <span class="form-check-label">Internal Project (Our Own)</span>
                        </label>
                    </div>

                    <div id="client_selection" x-show="!isInternal" x-transition>
                        <x-form.select name="client_id" label="Client" :options="$clients->pluck('name', 'id')->toArray()"
                            x-bind:required="!isInternal" x-bind:disabled="isInternal" />
                    </div>

                    <x-form.address name="location" label="Project Location" placeholder="e.g., Site Address, City" />

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