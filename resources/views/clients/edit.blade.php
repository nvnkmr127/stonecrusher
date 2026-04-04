<x-tabler-layout>
    <x-slot name="header">
        <x-page-header title="Edit Profile" subtitle="Update details for {{ $client->name }}">
            <x-slot name="actions">
                <a href="{{ route('clients.show', $client) }}" class="btn btn-outline-primary shadow-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-chart-bar" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"></path><path d="M3 12m0 1a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v6a1 1 0 0 1 -1 1h-4a1 1 0 0 1 -1 -1z"></path><path d="M9 8m0 1a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v10a1 1 0 0 1 -1 1h-4a1 1 0 0 1 -1 -1z"></path><path d="M15 4m0 1a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v14a1 1 0 0 1 -1 1h-4a1 1 0 0 1 -1 -1z"></path><path d="M4 20l14 0"></path></svg>
                    Go Back to Ledger
                </a>
            </x-slot>
        </x-page-header>
    </x-slot>

    <div class="row row-cards justify-content-center">
        <div class="col-md-9 col-lg-7">
            <div class="card border-0 shadow-lg overflow-hidden">
                <div class="card-status-top bg-purple"></div>
                <div class="card-header border-0 bg-transparent py-4">
                    <div class="d-flex align-items-center">
                        <div class="icon-box bg-purple-lt text-purple rounded-circle me-3" style="width: 42px; height: 42px; display: flex; align-items: center; justify-content: center;">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-user-edit" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"></path><path d="M9 7a4 4 0 1 0 8 0a4 4 0 0 0 -8 0"></path><path d="M9 17v-5a2 2 0 0 1 2 -2h2a2 2 0 0 1 2 2v5"></path><path d="M11 21h2"></path></svg>
                        </div>
                        <div>
                            <h3 class="card-title fw-bold">Update Client Information</h3>
                            <p class="text-muted small mb-0">Modify registration data for this partner</p>
                        </div>
                    </div>
                </div>

                <div class="card-body p-4 pt-0">
                    <form action="{{ route('clients.update', $client) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="row g-3">
                            <div class="col-12">
                                <x-form.input name="name" label="Legal / Business Name" :value="$client->name" placeholder="e.g. Acme Construction Ltd" required />
                            </div>
                            
                            <div class="col-md-6">
                                <x-form.input name="email" label="Contact Email" type="email" :value="$client->email" placeholder="client@example.com" />
                            </div>
                            
                            <div class="col-md-6">
                                <x-form.input name="phone" label="Primary Phone" :value="$client->phone" placeholder="+91 XXX XXX XXXX" />
                            </div>

                            <div class="col-12">
                                <x-form.address name="address" label="Mailing Address" :value="$client->address" placeholder="Street, City, State, ZIP" />
                            </div>

                            <div class="col-12">
                                <x-form.textarea name="notes" label="Internal Remarks" :value="$client->notes" rows="4" placeholder="Any specific business terms or delivery instructions..." />
                            </div>

                            <div class="col-12">
                                <div class="bg-light p-3 rounded-3 border d-flex align-items-center justify-content-between">
                                    <div>
                                        <div class="fw-bold fs-4">Active Account Status</div>
                                        <div class="text-muted small">Disable this to prevent new transactions immediately</div>
                                    </div>
                                    <x-form.checkbox name="is_active" label="Enabled" :checked="$client->is_active" />
                                </div>
                            </div>
                        </div>

                        <div class="card-footer bg-transparent border-0 px-0 pt-5">
                            <div class="btn-list justify-content-end">
                                <a href="{{ route('clients.index') }}" class="btn btn-link link-secondary px-4">Cancel Edits</a>
                                <button type="submit" class="btn btn-primary px-5 shadow-sm fw-bold">
                                    Update Member Profile
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="mt-4 text-center">
                <p class="text-muted small">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-history me-1" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"></path><path d="M12 8l0 4l2 2"></path><path d="M3.05 11a9 9 0 1 1 .5 4m-.5 5v-5h5"></path></svg>
                    Profile changes are tracked for security auditing.
                </p>
            </div>
        </div>
    </div>
</x-tabler-layout>