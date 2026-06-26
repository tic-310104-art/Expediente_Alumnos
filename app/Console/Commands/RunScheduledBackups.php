<?php

namespace App\Console\Commands;

use App\Models\BackupSchedule;
use App\Services\BackupService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class RunScheduledBackups extends Command
{
    protected $signature = 'backup:schedules-run';

    protected $description = 'Ejecuta respaldos agendados en backup_schedules (una vez o recurrente).';

    public function handle(BackupService $backupService): int
    {
        if (!Schema::hasTable('backup_schedules')) {
            return self::SUCCESS;
        }

        $activeSchedules = BackupSchedule::where('is_active', true)->orderBy('id', 'asc')->get();
        if ($activeSchedules->isEmpty()) {
            return self::SUCCESS;
        }

        $appTz = (string) config('app.timezone', 'UTC');
        $now = Carbon::now($appTz);
        $ranAny = false;
        $hadFailures = false;

        Log::debug('Revisión de respaldos agendados', [
            'timezone' => $appTz,
            'now' => $now->toDateTimeString(),
            'active_count' => $activeSchedules->count(),
        ]);
        $this->line('Revisión de respaldos agendados | tz=' . $appTz . ' | now=' . $now->toDateTimeString() . ' | activos=' . $activeSchedules->count());

        foreach ($activeSchedules as $schedule) {
            $scheduledDate = $schedule->scheduled_date?->format('Y-m-d') ?? (string) $schedule->scheduled_date;
            $scheduledTime = trim((string) $schedule->scheduled_time);

            if ($scheduledDate === '' || $scheduledTime === '') {
                Log::warning('Respaldo agendado inválido (fecha/hora vacía)', [
                    'schedule_id' => $schedule->id,
                    'scheduled_date' => $scheduledDate,
                    'scheduled_time' => $scheduledTime,
                ]);
                $this->line('Schedule #' . $schedule->id . ' inválido (fecha/hora vacía).');
                continue;
            }

            $dateTimeString = $scheduledDate . ' ' . $scheduledTime;
            $scheduledDateTime = null;
            try {
                if (preg_match('/^\d{4}-\d{2}-\d{2}\s\d{2}:\d{2}:\d{2}$/', $dateTimeString)) {
                    $scheduledDateTime = Carbon::createFromFormat('Y-m-d H:i:s', $dateTimeString, $appTz);
                } elseif (preg_match('/^\d{4}-\d{2}-\d{2}\s\d{2}:\d{2}$/', $dateTimeString)) {
                    $scheduledDateTime = Carbon::createFromFormat('Y-m-d H:i', $dateTimeString, $appTz);
                } else {
                    $scheduledDateTime = Carbon::parse($dateTimeString, $appTz);
                }
            } catch (\Throwable $e) {
                Log::error('No se pudo interpretar fecha/hora de respaldo agendado', [
                    'schedule_id' => $schedule->id,
                    'scheduled_raw' => $dateTimeString,
                    'timezone' => $appTz,
                    'error' => $e->getMessage(),
                ]);
                continue;
            }

            if ($now->lessThan($scheduledDateTime)) {
                Log::debug('Respaldo agendado aún no vence', [
                    'schedule_id' => $schedule->id,
                    'now' => $now->toDateTimeString(),
                    'scheduled_at' => $scheduledDateTime->toDateTimeString(),
                    'timezone' => $appTz,
                    'frequency' => $schedule->frequency,
                ]);
                $this->line('Schedule #' . $schedule->id . ' aún no vence | programado=' . $scheduledDateTime->toDateTimeString());
                continue;
            }

            if ($schedule->last_run_at && Carbon::parse($schedule->last_run_at)->greaterThanOrEqualTo($scheduledDateTime)) {
                if ($schedule->frequency === 'once') {
                    $schedule->update(['is_active' => false]);
                }
                continue;
            }

            Log::info('Ejecutando respaldo agendado', [
                'schedule_id' => $schedule->id,
                'now' => $now->toDateTimeString(),
                'scheduled_at' => $scheduledDateTime->toDateTimeString(),
                'timezone' => $appTz,
                'frequency' => $schedule->frequency,
            ]);
            $this->line('Ejecutando schedule #' . $schedule->id . ' | programado=' . $scheduledDateTime->toDateTimeString() . ' | freq=' . (string) $schedule->frequency);

            $result = $backupService->runMysqlDump($scheduledDate, $scheduledTime);
            if (!($result['success'] ?? false)) {
                $hadFailures = true;
                Log::error('Respaldo agendado falló', [
                    'schedule_id' => $schedule->id,
                    'scheduled_at' => $scheduledDateTime->toDateTimeString(),
                    'timezone' => $appTz,
                    'frequency' => $schedule->frequency,
                    'message' => $result['message'] ?? null,
                    'path' => $result['path'] ?? null,
                    'exit_code' => $result['exit_code'] ?? null,
                    'output_tail' => isset($result['output']) ? implode("\n", array_slice((array) $result['output'], -20)) : null,
                ]);
                $this->error('Falló schedule #' . $schedule->id . ': ' . (string) ($result['message'] ?? 'Error'));
                continue;
            }

            $ranAny = true;
            $schedule->update(['last_run_at' => now()]);

            Log::info('Respaldo agendado ejecutado', [
                'schedule_id' => $schedule->id,
                'scheduled_at' => $scheduledDateTime->toDateTimeString(),
                'timezone' => $appTz,
                'frequency' => $schedule->frequency,
                'path' => $result['path'] ?? null,
            ]);
            $this->info('OK schedule #' . $schedule->id . ' | archivo=' . (string) ($result['path'] ?? ''));

            if ($schedule->frequency === 'once') {
                $schedule->update(['is_active' => false]);
                continue;
            }

            $nextDateTime = $scheduledDateTime->copy();
            while (!$nextDateTime->greaterThan($now)) {
                switch ($schedule->frequency) {
                    case '4_days':
                        $nextDateTime->addDays(4);
                        break;
                    case '7_days':
                        $nextDateTime->addDays(7);
                        break;
                    case 'monthly':
                        $nextDateTime->addMonth();
                        break;
                    default:
                        $nextDateTime->addDays(7);
                        break;
                }
            }

            $schedule->update([
                'scheduled_date' => $nextDateTime->format('Y-m-d'),
                'scheduled_time' => $nextDateTime->format('H:i'),
            ]);
        }

        if ($hadFailures && !$ranAny) {
            return self::FAILURE;
        }

        return self::SUCCESS;
    }
}
