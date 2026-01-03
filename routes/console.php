<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Schedule::command('habits:verify-streaks')->dailyAt('00:01');
Schedule::command('habits:generate-report')->weeklyOn(7, '23:55');
Schedule::command('auth:cleanup-login-otps --days=7')->daily();
Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');
