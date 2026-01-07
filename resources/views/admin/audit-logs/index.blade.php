<x-tabler-layout>
    <x-slot name="header">
        <h2 class="page-title">
            {{ __('Audit Logs') }}
        </h2>
    </x-slot>

    <div class="row row-cards">
        <div class="col-12">
            <div class="card">
                <div class="card-body">

                    <!-- Filters -->
                    <form method="GET" action="{{ route('audit-logs.index') }}" class="mb-3">
                        <div class="row g-2">
                            <div class="col-md-3">
                                <label class="form-label">{{ __('Description') }}</label>
                                <input type="text" name="description" value="{{ request('description') }}"
                                    class="form-control" placeholder="Search description...">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">{{ __('Event') }}</label>
                                <select name="event" class="form-select">
                                    <option value="">All Events</option>
                                    <option value="created" {{ request('event') == 'created' ? 'selected' : '' }}>Created
                                    </option>
                                    <option value="updated" {{ request('event') == 'updated' ? 'selected' : '' }}>Updated
                                    </option>
                                    <option value="deleted" {{ request('event') == 'deleted' ? 'selected' : '' }}>Deleted
                                    </option>
                                    <option value="login" {{ request('event') == 'login' ? 'selected' : '' }}>Login
                                    </option>
                                    <option value="logout" {{ request('event') == 'logout' ? 'selected' : '' }}>Logout
                                    </option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">{{ __('User') }}</label>
                                <select name="causer_id" class="form-select">
                                    <option value="">All Users</option>
                                    @foreach($users as $user)
                                        <option value="{{ $user->id }}" {{ request('causer_id') == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">{{ __('Module') }}</label>
                                <select name="subject_type" class="form-select">
                                    <option value="">All Modules</option>
                                    @foreach($subjectTypes as $type)
                                        <option value="{{ $type['value'] }}" {{ request('subject_type') == $type['value'] ? 'selected' : '' }}>{{ $type['label'] }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">{{ __('Date Range') }}</label>
                                <div class="input-group">
                                    <input type="date" name="date_from" value="{{ request('date_from') }}"
                                        class="form-control" placeholder="From">
                                    <input type="date" name="date_to" value="{{ request('date_to') }}"
                                        class="form-control" placeholder="To">
                                </div>
                            </div>
                        </div>
                        <div class="mt-3">
                            <button type="submit" class="btn btn-primary">{{ __('Filter') }}</button>
                            <a href="{{ route('audit-logs.index') }}" class="btn btn-link">{{ __('Reset') }}</a>
                        </div>
                    </form>

                    <div class="table-responsive">
                        <table class="table table-vcenter card-table">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>User</th>
                                    <th>Event</th>
                                    <th>Subject</th>
                                    <th>IP Address</th>
                                    <th>Description</th>
                                    <th>Changes</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($activities as $activity)
                                    <tr>
                                        <td class="text-muted text-nowrap">
                                            {{ $activity->created_at->format('d M Y H:i:s') }}
                                        </td>
                                        <td>
                                            {{ $activity->causer ? $activity->causer->name : 'System' }}
                                        </td>
                                        <td>
                                            @php
                                                $color = 'secondary';
                                                if ($activity->event === 'created')
                                                    $color = 'success';
                                                if ($activity->event === 'updated')
                                                    $color = 'warning';
                                                if ($activity->event === 'deleted')
                                                    $color = 'danger';
                                                if ($activity->event === 'login')
                                                    $color = 'blue';
                                            @endphp
                                            <span class="badge bg-{{ $color }}-lt">
                                                {{ ucfirst($activity->event) }}
                                            </span>
                                        </td>
                                        <td class="text-muted">
                                            {{ class_basename($activity->subject_type) }} #{{ $activity->subject_id }}
                                        </td>
                                        <td class="text-muted">
                                            {{ $activity->ip_address ?? '-' }}
                                        </td>
                                        <td>
                                            {{ $activity->description }}
                                        </td>
                                        <td>
                                            @if($activity->properties->count() > 0)
                                                <button class="btn btn-sm btn-ghost-primary" type="button"
                                                    data-bs-toggle="collapse" data-bs-target="#details-{{ $activity->id }}"
                                                    aria-expanded="false">
                                                    View Details
                                                </button>
                                                <div class="collapse mt-2" id="details-{{ $activity->id }}">
                                                    <pre
                                                        class="small p-2 bg-light border rounded mb-0 text-wrap">{{ json_encode($activity->properties, JSON_PRETTY_PRINT) }}</pre>
                                                </div>
                                            @else
                                                -
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center text-muted">No activity logs found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $activities->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-tabler-layout>