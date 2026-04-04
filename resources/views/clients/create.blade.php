<x-tabler-layout>
    <x-slot name="header">
        <x-page-header title="New Client Registration" subtitle="Register a fresh account in the system">
            <x-slot name="actions">
                <a href="{{ route('clients.index') }}" class="btn btn-outline-primary">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-arrow-left" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"></path><path d="M5 12l14 0"></path><path d="M5 12l6 6"></path><path d="M5 12l6 -6"></path></svg>
                    Back to List
                </a>
            </x-slot>
        </x-page-header>
    </x-slot>

    <div class="row row-cards justify-content-center">
        <div class="col-md-9 col-lg-7">
            <div class="card border-0 shadow-lg overflow-hidden">
                <div class="card-status-top bg-primary"></div>
                <div class="card-header border-0 bg-transparent py-4">
                    <div class="d-flex align-items-center">
                        <div class="icon-box bg-primary-lt text-primary rounded-circle me-3" style="width: 42px; height: 42px; display: flex; align-items: center; justify-content: center;">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-user-plus" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"></path><path d="M8 7a4 4 0 1 0 8 0a4 4 0 0 0 -8 0"></path><path d="M16 19h6"></path><path d="M19 16v6"></path><path d="M6 21v-2a4 4 0 0 1 4 -4h4"></path></svg>
                        </div>
                        <div>
                            <h3 class="card-title fw-bold">Identification Details</h3>
                            <p class="text-muted small mb-0">Basic profile and contact information</p>
                        </div>
                    </div>
                </div>

                <div class="card-body p-4 pt-0">
                    <form action="{{ route('clients.store') }}" method="POST">
                        @csrf

                        <div class="row g-3">
                            <div class="col-md-12">
                                <x-form.input name="name" label="Legal / Business Name" placeholder="e.g. Acme Construction Ltd" required />
                            </div>
                            
                            <div class="col-md-6">
                                <x-form.input name="email" label="Contact Email" type="email" placeholder="client@example.com" />
                            </div>
                            
                            <div class="col-md-6">
                                <x-form.input name="phone" label="Primary Phone" placeholder="+91 XXX XXX XXXX" />
                            </div>

                            <div class="col-12">
                                <x-form.address name="address" label="Mailing Address" placeholder="Street, City, State, ZIP" />
                            </div>

                            <div class="col-12">
                                <x-form.textarea name="notes" label="Internal Remarks" rows="4" placeholder="Any specific business terms or delivery instructions..." />
                            </div>

                            <div class="col-12">
                                <div class="bg-light p-3 rounded-3 border d-flex align-items-center justify-content-between">
                                    <div>
                                        <div class="fw-bold fs-4">Active Account Status</div>
                                        <div class="text-muted small">Immediately allow transactions for this client</div>
                                    </div>
                                    <x-form.checkbox name="is_active" label="Enabled" :checked="true" />
                                </div>
                            </div>
                        </div>

                        <div class="card-footer bg-transparent border-0 px-0 pt-5">
                            <div class="btn-list justify-content-end">
                                <a href="{{ route('clients.index') }}" class="btn btn-link link-secondary px-4">Cancel</a>
                                <button type="submit" class="btn btn-primary px-5 shadow-sm fw-bold">
                                    Register Client
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            
            <div class="mt-4 text-center">
                <p class="text-muted small">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-shield-check me-1" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"></path><path d="M9 12l2 2l4 -4"></path><path d="M12 3a12 12 0 0 0 8.5 3a12 12 0 0 1 -8.5 15a12 12 0 0 1 -8.5 -15a12 12 0 0 0 8.5 -3"></path></svg>
                    Data is stored securely according to enterprise policy.
                </p>
            </div>
        </div>
    </div>
</x-tabler-layout>