<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Login;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class LogSuccessfulLogin
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(Login $event): void
    {
        // $event->user is the logged in user
        if ($event->user) {
            activity()
                ->causedBy($event->user)
                ->event('login')
                ->log('User logged in');
        }
    }
}
