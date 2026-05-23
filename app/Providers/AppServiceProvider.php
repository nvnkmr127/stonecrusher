<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Register Observers
        \App\Models\DieselEntry::observe(\App\Observers\DieselEntryObserver::class);
        \App\Models\GatePass::observe(\App\Observers\GatePassObserver::class);
        \App\Models\CrusherExpense::observe(\App\Observers\CrusherExpenseObserver::class);
        \App\Models\QuarryDrillingLog::observe(\App\Observers\QuarryDrillingLogObserver::class);
        \App\Models\QuarrySecondaryBlasting::observe(\App\Observers\QuarrySecondaryBlastingObserver::class);
        \App\Models\QuarryLabourSheet::observe(\App\Observers\QuarryLabourSheetObserver::class);
        \App\Models\QuarryBlast::observe(\App\Observers\QuarryBlastObserver::class);
        \App\Models\QuarryBlastingMaterialUsed::observe(\App\Observers\QuarryBlastingMaterialUsedObserver::class);
        \App\Models\OperationalRecord::observe(\App\Observers\OperationalRecordObserver::class);
        \App\Models\ClientTransaction::observe(\App\Observers\ClientTransactionObserver::class);

        // Register Policies
        \Illuminate\Support\Facades\Gate::policy(\App\Models\Attendance::class, \App\Policies\AttendancePolicy::class);

        // Give admin role all permissions
        \Illuminate\Support\Facades\Gate::before(function ($user, $ability) {
            return $user->hasRole('admin') ? true : null;
        });

        // Log IP Address
        \Spatie\Activitylog\Models\Activity::creating(function (\Spatie\Activitylog\Models\Activity $activity) {
            $activity->ip_address = request()->ip();
        });

        // Set App Timezone
        try {
            $timezone = \App\Models\Setting::get('app_timezone');
            if ($timezone) {
                config(['app.timezone' => $timezone]);
                date_default_timezone_set($timezone);
            }
        } catch (\Exception $e) {
            // Ignore if settings table doesn't exist yet
        }

        // Config Overrides for Google Drive
        try {
            $refreshToken = \App\Models\Setting::get('google_drive_refresh_token');
            if ($refreshToken) {
                config(['filesystems.disks.google.refreshToken' => $refreshToken]);
            }
        } catch (\Exception $e) {
            // Setup might not be ready (e.g. migration not run)
        }

        // Dynamically remove 'google' from backup destinations if not configured
        $googleToken = config('filesystems.disks.google.refreshToken');
        if (empty($googleToken)) {
            $destinations = config('backup.backup.destination.disks', []);
            $destinations = array_filter($destinations, function ($disk) {
                return $disk !== 'google';
            });
            config(['backup.backup.destination.disks' => array_values($destinations)]);
        }


        // Register Google Drive Driver
        try {
            \Illuminate\Support\Facades\Storage::extend('google', function ($app, $config) {
                $client = new \Google\Client();
                $client->setClientId($config['clientId']);
                $client->setClientSecret($config['clientSecret']);
                $client->refreshToken($config['refreshToken']);

                $service = new \Google\Service\Drive($client);
                $adapter = new \Masbug\Flysystem\GoogleDriveAdapter($service, $config['folderId'] ?? '/');

                return new \League\Flysystem\Filesystem($adapter);
            });
        } catch (\Exception $e) {
            // handle exception if needed
        }
    }
}
