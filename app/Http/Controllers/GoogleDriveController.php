<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;

class GoogleDriveController extends Controller
{
    /**
     * Redirect the user to the Google authentication page.
     */
    public function redirect()
    {
        return Socialite::driver('google')
            ->scopes(['https://www.googleapis.com/auth/drive.file'])
            ->with(['access_type' => 'offline', 'prompt' => 'consent'])
            ->redirect();
    }

    /**
     * Obtain the user information from Google.
     */
    public function callback()
    {
        try {
            $user = Socialite::driver('google')->user();

            $refreshToken = $user->refreshToken;

            if ($refreshToken) {
                Setting::set('google_drive_refresh_token', $refreshToken);
                Setting::set('google_drive_email', $user->email);

                return redirect()->route('backups.index')
                    ->with('success', 'Google Drive connected successfully! Account: ' . $user->email);
            }

            return redirect()->route('backups.index')
                ->with('error', 'Failed to retrieve refresh token. Please try again and ensure you allow offline access.');

        } catch (\Exception $e) {
            return redirect()->route('backups.index')
                ->with('error', 'Google authentication failed: ' . $e->getMessage());
        }
    }
}
