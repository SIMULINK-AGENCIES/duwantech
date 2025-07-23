<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Schedule session cleanup to run every 15 minutes
Schedule::command('sessions:cleanup')->everyFifteenMinutes();

// Schedule daily cleanup with more aggressive settings
Schedule::command('sessions:cleanup --minutes=60')->daily();
