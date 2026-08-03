<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Schedule::command('reservations:delete-old')
    ->daily();

Schedule::command('admin-activity:delete-old')
    ->daily();

Schedule::command('google-calendar:sync-serbian-holidays')
    ->daily();

Schedule::command('google-calendar:sync-hungarian-holidays')
    ->daily();

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');
