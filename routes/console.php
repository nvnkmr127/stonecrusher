<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Schedule Backups
Illuminate\Support\Facades\Schedule::command('backup:run')->daily()->at('01:00');
Illuminate\Support\Facades\Schedule::command('backup:clean')->daily()->at('02:00');
