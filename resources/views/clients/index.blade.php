<x-tabler-layout>
    <x-slot name="header">
        <div class="row align-items-center">
            <div class="col">
                <h2 class="page-title">Clients</h2>
                <div class="page-subtitle">Manage client information</div>
            </div>
            <div class="col-auto">
                <a href="{{ route('clients.create') }}" class="btn btn-primary">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24"
                        stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round"
                        stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                        <path d="M12 5l0 14" />
                        <path d="M5 12l14 0" />
                    </svg>
                    Add Client
                </a>
            </div>
        </div>
    </x-slot>

    <div class="row row-deck row-cards">
        <div class="col-12">
            <x-card>
                <x-slot name="header">
                    Clients ({{ $clients->total() }})
                </x-slot>

                <div class="card-body border-bottom py-3">
                    <div class="d-flex">
                        <div class="ms-auto text-muted">
                            Search:
                            <div class="ms-2 d-inline-block">
                                <form method="GET" action="{{ route('clients.index') }}">
                                    <input type="text" name="search" value="{{ request('search') }}" class="form-control form-control-sm" aria-label="Search client" placeholder="Name, Phone, or Email...">
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <x-table>
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Balance</th>
                            <th>Status</th>
                            <th>Created</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($clients as $client)
                            <tr>
                                <td>
                                    <a href="{{ route('clients.show', $client) }}"
                                        class="text-reset fw-bold">{{ $client->name }}</a>
                                </td>
                                <td>{{ $client->email ?? '-' }}</td>
                                <td>{{ $client->phone ?? '-' }}</td>
                                <td>
                                    @php
                                        $bal = $client->balance;
                                    @endphp
                                    <span class="{{ $bal >= 0 ? 'text-success' : 'text-danger' }}">
                                        {{ number_format(abs($bal), 2) }} {{ $bal >= 0 ? 'Cr' : 'Dr' }}
                                    </span>
                                </td>
                                <td>
                                    @if($client->is_active)
                                        <span class="badge bg-success">Active</span>
                                    @else
                                        <span class="badge bg-secondary">Inactive</span>
                                    @endif
                                </td>
                                <td>{{ $client->created_at->format('M d, Y') }}</td>
                                <td>
                                    <div class="btn-list">
                                        <a href="{{ route('clients.show', $client) }}"
                                            class="btn btn-sm btn-outline-info">Ledger</a>
                                        <a href="{{ route('clients.edit', $client) }}"
                                            class="btn btn-sm btn-primary">Edit</a>
                                        <form action="{{ route('clients.destroy', $client) }}" method="POST"
                                            class="d-inline" onsubmit="return confirm('Are you sure?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center">No clients found</td>
                            </tr>
                        @endforelse
                    </tbody>
                </x-table>

                <div class="mt-3">
                    {{ $clients->links() }}
                </div>
            </x-card>
        </div>
    </div>
</x-tabler-layout>