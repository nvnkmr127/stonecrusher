<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class SettingsController extends Controller
{
    /**
     * Display the settings form.
     */
    public function index()
    {
        $settings = Setting::all()->pluck('value', 'key');
        $timezones = \DateTimeZone::listIdentifiers();
        $indianStates = [
            'Andhra Pradesh',
            'Arunachal Pradesh',
            'Assam',
            'Bihar',
            'Chhattisgarh',
            'Goa',
            'Gujarat',
            'Haryana',
            'Himachal Pradesh',
            'Jharkhand',
            'Karnataka',
            'Kerala',
            'Madhya Pradesh',
            'Maharashtra',
            'Manipur',
            'Meghalaya',
            'Mizoram',
            'Nagaland',
            'Odisha',
            'Punjab',
            'Rajasthan',
            'Sikkim',
            'Tamil Nadu',
            'Telangana',
            'Tripura',
            'Uttar Pradesh',
            'Uttarakhand',
            'West Bengal',
            'Andaman and Nicobar Islands',
            'Chandigarh',
            'Dadra and Nagar Haveli and Daman and Diu',
            'Delhi',
            'Jammu and Kashmir',
            'Ladakh',
            'Lakshadweep',
            'Puducherry'
        ];

        return view('settings.index', compact('settings', 'timezones', 'indianStates'));
    }

    /**
     * Update the settings.
     */
    public function update(Request $request)
    {
        $validated = $request->validate([
            'company_name' => 'required|string|max:255',
            'app_timezone' => 'required|string|max:255',
            'allowed_states' => 'nullable|array',
            'allowed_states.*' => 'string',
            'currency_symbol' => 'required|string|max:10',
            'financial_year' => 'required|string|max:20',
            'crusher_latitude' => 'required|numeric',
            'crusher_longitude' => 'required|numeric',
            'default_diesel_rate' => 'required|numeric|min:0',
            'rate_per_km' => 'required|numeric|min:0',
            'date_format' => 'required|string|max:20',
            'google_maps_api_key' => 'nullable|string|max:255',
            'attendance_shift_start' => 'required|date_format:H:i',
            'attendance_shift_end' => 'required|date_format:H:i|after:attendance_shift_start',
            'default_round_trip' => 'nullable|boolean',
        ]);

        foreach ($validated as $key => $value) {
            if (is_array($value)) {
                $value = json_encode($value);
            }
            Setting::set($key, $value);
        }

        // Clear all setting caches
        Cache::flush();

        return redirect()
            ->route('settings.index')
            ->with('success', 'Settings updated successfully!');
    }
}
