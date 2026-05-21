<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule; // Importamos el Facade para programar tareas

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Programamos el envío de reportes diarios (Fase 2)
// Esto se ejecutará todos los días a las 08:00 a.m.
Schedule::command('reports:send-daily')->dailyAt('08:00');
