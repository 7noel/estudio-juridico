<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('test', function () {
    $this->comment('ok');
});

Schedule::command('reminders:quotas')
    ->everyMinute();

Schedule::command('reminders:inactive-clients')
    ->everyMinute();

Schedule::command('reminders:agenda')
    ->everyMinute();