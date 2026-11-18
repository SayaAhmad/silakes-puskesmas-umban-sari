<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Retensi data pribadi: kosongkan nama/NIK/HP/keluhan pada antrian lama (PRD F-05).
Schedule::command('antrian:anonimkan')->dailyAt('01:00');
