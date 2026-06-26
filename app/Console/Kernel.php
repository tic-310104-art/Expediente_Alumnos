<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // Programar respaldo automático diario a las 2:00 AM
        $schedule->command('db:backup')
                 ->dailyAt('02:00')
                 ->description('Respaldo automático de la base de datos')
                 ->onSuccess(function () {
                     \Log::info('Respaldo automático ejecutado exitosamente');
                 })
                 ->onFailure(function () {
                     \Log::error('Falló el respaldo automático de la base de datos');
                 });
        
        // También programar respaldo semanal los domingos a las 3:00 AM
        $schedule->command('db:backup')
                 ->weeklyOn(0, '03:00')
                 ->description('Respaldo semanal de la base de datos');

        // Ejecutar respaldos agendados desde el panel (backup_schedules)
        $schedule->command('backup:schedules-run')
                 ->everyMinute()
                 ->description('Ejecuta respaldos agendados por fecha/hora/frecuencia');
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
