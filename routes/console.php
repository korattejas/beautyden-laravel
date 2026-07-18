<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

// Artisan::command('inspire', function () {
//     $this->comment(Inspiring::quote());
// })->purpose('Display an inspiring quote')->hourly();

use Illuminate\Support\Facades\Schedule;

Schedule::call(function () {
    Artisan::call('backup:email-db', ['email' => 'korattejas01@gmail.com']);
})->dailyAt('01:00');

// Process scheduled push notifications & daily birthday wishes
Schedule::call(function () {
    Artisan::call('notifications:process');
})->everyMinute()->name('notifications_process')->withoutOverlapping();
