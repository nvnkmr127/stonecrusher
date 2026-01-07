<x-tabler-layout>
    <x-slot name="header">
        <h2 class="page-title">
            {{ __('System Settings') }}
        </h2>
        <div class="page-subtitle">
            Configure global ERP settings
        </div>
    </x-slot>

    <div class="row row-deck row-cards">
        <div class="col-12">
            <x-card>
                <x-slot name="header">
                    General Settings
                </x-slot>

                <form action="{{ route('settings.update') }}" method="POST">
                    @csrf

                    <div class="row">
                        <!-- Company Information -->
                        <div class="col-md-6">
                            <h3 class="mb-3">Company Information</h3>
                            
                            <x-form.input 
                                name="company_name" 
                                label="Company Name" 
                                :value="$settings['company_name'] ?? ''"
                                required 
                            />

                            <div class="mb-3">
                                <label class="form-label required">App Timezone</label>
                                <select class="form-select" name="app_timezone" required>
                                    @foreach($timezones as $timezone)
                                        <option value="{{ $timezone }}" {{ ($settings['app_timezone'] ?? config('app.timezone')) == $timezone ? 'selected' : '' }}>
                                            {{ $timezone }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <x-form.input 
                                name="currency_symbol" 
                                label="Currency Symbol" 
                                :value="$settings['currency_symbol'] ?? ''"
                                required 
                                placeholder="₹"
                            />

                            <x-form.input 
                                name="financial_year" 
                                label="Financial Year" 
                                :value="$settings['financial_year'] ?? ''"
                                required 
                                placeholder="2024-2025"
                            />

                            <x-form.select 
                                name="date_format" 
                                label="Date Format" 
                                :options="[
                                    'd/m/Y' => 'DD/MM/YYYY (31/12/2024)',
                                    'm/d/Y' => 'MM/DD/YYYY (12/31/2024)',
                                    'Y-m-d' => 'YYYY-MM-DD (2024-12-31)',
                                    'd-M-Y' => 'DD-Mon-YYYY (31-Dec-2024)',
                                ]"
                                :selected="$settings['date_format'] ?? 'd/m/Y'"
                                required
                            />
                        </div>

                        <!-- Operational Settings -->
                        <div class="col-md-6">
                            <h3 class="mb-3">Operational Settings</h3>

                            <div class="row">
                                <div class="col-md-6">
                                    <x-form.input 
                                        name="crusher_latitude" 
                                        label="Crusher Latitude" 
                                        type="number"
                                        step="any"
                                        :value="$settings['crusher_latitude'] ?? '0.0'"
                                        required 
                                    />
                                </div>
                                <div class="col-md-6">
                                    <x-form.input 
                                        name="crusher_longitude" 
                                        label="Crusher Longitude" 
                                        type="number"
                                        step="any"
                                        :value="$settings['crusher_longitude'] ?? '0.0'"
                                        required 
                                    />
                                </div>
                            </div>

                            <x-form.input 
                                name="default_diesel_rate" 
                                label="Default Diesel Rate (per liter)" 
                                type="number"
                                step="0.01"
                                :value="$settings['default_diesel_rate'] ?? '100.00'"
                                required 
                            />

                            <x-form.input 
                                name="rate_per_km" 
                                label="Transport Rate per KM" 
                                type="number"
                                step="0.01"
                                :value="$settings['rate_per_km'] ?? '10.00'"
                                required 
                            />

                            <div class="mb-3">
                                <label class="form-check">
                                    <input class="form-check-input" type="checkbox" name="default_round_trip" value="1" {{ ($settings['default_round_trip'] ?? false) ? 'checked' : '' }}>
                                    <span class="form-check-label">Default to Round Trip Calculation?</span>
                                </label>
                            </div>

                            <x-form.input 
                                name="google_maps_api_key" 
                                label="Google Maps API Key (Optional)" 
                                type="password"
                                :value="$settings['google_maps_api_key'] ?? ''"
                                placeholder="Leave empty to use free OpenStreetMap"
                            />

                            <div class="mb-3">
                                <label class="form-label">Allowed States (Limit Search Results)</label>
                                <select class="form-select" name="allowed_states[]" multiple size="8">
                                    @php
                                        $selectedStates = json_decode($settings['allowed_states'] ?? '[]', true) ?? [];
                                    @endphp
                                    @foreach($indianStates as $state)
                                        <option value="{{ $state }}" {{ in_array($state, $selectedStates) ? 'selected' : '' }}>
                                            {{ $state }}
                                        </option>
                                    @endforeach
                                </select>
                                <small class="form-hint">Hold Ctrl (Windows) or Cmd (Mac) to select multiple states. Leave empty to allow all.</small>
                            </div>

                            <h3 class="mb-3 mt-4">Attendance Settings</h3>
                            <div class="row">
                                <div class="col-md-6">
                                    <x-form.input 
                                        name="attendance_shift_start" 
                                        label="Shift Start Time" 
                                        type="time"
                                        :value="$settings['attendance_shift_start'] ?? '09:30'"
                                        required 
                                    />
                                </div>
                                <div class="col-md-6">
                                    <x-form.input 
                                        name="attendance_shift_end" 
                                        label="Shift End Time" 
                                        type="time"
                                        :value="$settings['attendance_shift_end'] ?? '18:30'"
                                        required 
                                    />
                                </div>
                            </div>
                        </div>
                    </div>

                    <hr class="my-4">

                    <div class="d-flex">
                        <x-button variant="primary" type="submit">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-device-floppy" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M6 4h10l4 4v10a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2v-12a2 2 0 0 1 2 -2" /><path d="M12 14m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0" /><path d="M14 4l0 4l-6 0l0 -4" /></svg>
                            Save Settings
                        </x-button>
                        <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary ms-2">
                            Cancel
                        </a>
                    </div>
                </form>
            </x-card>
        </div>

        <!-- Settings Preview -->
        <div class="col-12">
            <x-card>
                <x-slot name="header">
                    Current Settings Preview
                </x-slot>

                <x-table>
                    <thead>
                        <tr>
                            <th>Setting</th>
                            <th>Value</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><strong>Company Name</strong></td>
                            <td>{{ $settings['company_name'] ?? 'Not set' }}</td>
                        </tr>
                        <tr>
                            <td><strong>Currency Symbol</strong></td>
                            <td>{{ $settings['currency_symbol'] ?? 'Not set' }}</td>
                        </tr>
                        <tr>
                            <td><strong>Financial Year</strong></td>
                            <td>{{ $settings['financial_year'] ?? 'Not set' }}</td>
                        </tr>
                        <tr>
                            <td><strong>Crusher Location</strong></td>
                            <td>{{ $settings['crusher_latitude'] ?? '0.0' }}, {{ $settings['crusher_longitude'] ?? '0.0' }}</td>
                        </tr>
                        <tr>
                            <td><strong>Default Diesel Rate</strong></td>
                            <td>{{ $settings['currency_symbol'] ?? '₹' }}{{ $settings['default_diesel_rate'] ?? '0.00' }} per liter</td>
                        </tr>
                        <tr>
                            <td><strong>Rate per KM</strong></td>
                            <td>{{ $settings['currency_symbol'] ?? '₹' }}{{ $settings['rate_per_km'] ?? '0.00' }}</td>
                        </tr>
                        <tr>
                            <td><strong>Date Format</strong></td>
                            <td>{{ $settings['date_format'] ?? 'd/m/Y' }} ({{ now()->format($settings['date_format'] ?? 'd/m/Y') }})</td>
                        </tr>
                    </tbody>
                </x-table>
            </x-card>
        </div>
    </div>
</x-tabler-layout>
