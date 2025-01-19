<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Schedule::command('check-medicine-status')->monthly();
Schedule::command('queue:work --stop-when-empty')->everyMinute()->withoutOverlapping();

