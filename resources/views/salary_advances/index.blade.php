<x-tabler-layout>
    <x-slot name="header">
        <div class="row align-items-center">
            <div class="col">
                <h2 class="page-title">Salary Advances</h2>
                <div class="page-subtitle">Track payments made in advance to employees</div>
            </div>
            <div class="col-auto">
                <a href="{{ route('salary-advances.create') }}" class="btn btn-primary">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24"
                        stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round"
                        stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                        <path d="M12 5l0 14"></path>
                        <path d="M5 12l14 0"></path>
                    </svg>
                    Add Advance
                </a>
            </div>
        </div>
    </x-slot>

    <div class="row row-cards">
        <div class="col-12">
            <x-card>
                <x-table>
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Employee</th>
                            <th>Amount</th>
                            <th>Mode</th>
                            <th>Remarks</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($advances as $advance)
                            <tr>
                                <td>{{ $advance->date->format('d M, Y') }}</td>
                                <td>{{ $advance->user->name }}</td>
                                <td class="fw-bold">₹ {{ number_format($advance->amount, 2) }}</td>
                                <td>
                                    @if($advance->payment_mode)
                                        <span class="badge bg-purple-lt">{{ $advance->payment_mode }}</span>
                                    @else
                                        -
                                    @endif
                                </td>
                                <td>{{ $advance->remarks ?? '-' }}</td>
                                <td>
                                    @php
                                        $isLocked = \App\Models\PayrollPeriod::isLocked($advance->date->month, $advance->date->year);
                                    @endphp
                                    @if(!$isLocked)
                                        <div class="btn-list">
                                            <a href="{{ route('salary-advances.edit', $advance) }}"
                                                class="btn btn-sm btn-primary">Edit</a>
                                            <form action="{{ route('salary-advances.destroy', $advance) }}" method="POST"
                                                class="d-inline" onsubmit="return confirm('Are you sure?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                                            </form>
                                        </div>
                                    @else
                                        <span class="badge bg-secondary">Locked</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center">No salary advances found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </x-table>
                <div class="mt-3">
                    {{ $advances->links() }}
                </div>
            </x-card>
        </div>
    </div>
</x-tabler-layout>