<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

class BackupService
{
    public function runMysqlDump(string $scheduledDate, string $scheduledTime): array
    {
        if (!function_exists('exec')) {
            return [
                'success' => false,
                'message' => 'La función exec() está deshabilitada en PHP. No se puede ejecutar mysqldump.',
                'path' => null,
                'output' => null,
                'exit_code' => 1,
            ];
        }

        $timePart = $scheduledTime !== '' ? str_replace(':', '-', $scheduledTime) : now()->format('H-i');
        $backupDir = (string) env('BACKUP_PATH', storage_path('app/backups'));

        if (!is_dir($backupDir)) {
            @mkdir($backupDir, 0775, true);
        }

        if (!is_dir($backupDir)) {
            return [
                'success' => false,
                'message' => 'No se pudo crear/acceder al directorio de respaldo.',
                'path' => null,
                'output' => null,
                'exit_code' => 1,
            ];
        }

        $fileName = 'expediente_' . $scheduledDate . '_' . $timePart . '.sql';
        $backupPath = rtrim($backupDir, "\\/") . DIRECTORY_SEPARATOR . $fileName;

        $conn = config('database.connections.mysql');
        $host = $conn['host'] ?? '127.0.0.1';
        $port = (string) ($conn['port'] ?? '3306');
        $database = $conn['database'] ?? '';
        $username = $conn['username'] ?? '';
        $password = $conn['password'] ?? '';

        if ($database === '' || $username === '') {
            return [
                'success' => false,
                'message' => 'Configuración de base de datos incompleta.',
                'path' => null,
                'output' => null,
                'exit_code' => 1,
            ];
        }

        $tmpFile = tempnam(sys_get_temp_dir(), 'mysqldump_');
        if (!$tmpFile) {
            return [
                'success' => false,
                'message' => 'No se pudo crear archivo temporal para mysqldump.',
                'path' => null,
                'output' => null,
                'exit_code' => 1,
            ];
        }

        $ini = "[client]\nuser={$username}\npassword={$password}\nhost={$host}\nport={$port}\n";
        file_put_contents($tmpFile, $ini);

        $mysqldump = 'mysqldump';

        $laragonMysqlPath = 'C:\laragon\bin\mysql';
        if (is_dir($laragonMysqlPath)) {
            $dirs = scandir($laragonMysqlPath);
            foreach ($dirs as $dir) {
                if ($dir !== '.' && $dir !== '..') {
                    $candidate = $laragonMysqlPath . '\\' . $dir . '\\bin\\mysqldump.exe';
                    if (file_exists($candidate)) {
                        $mysqldump = $candidate;
                        break;
                    }
                }
            }
        }

        $command = escapeshellarg($mysqldump)
            . ' --defaults-extra-file=' . escapeshellarg($tmpFile)
            . ' --routines --triggers --single-transaction'
            . ' --column-statistics=0'
            . ' --set-gtid-purged=OFF'
            . ' --result-file=' . escapeshellarg($backupPath)
            . ' ' . escapeshellarg($database)
            . ' 2>&1';

        $output = [];
        $exitCode = 0;
        exec($command, $output, $exitCode);
        @unlink($tmpFile);

        if ($exitCode !== 0) {
            Log::error('Error ejecutando mysqldump', [
                'exit_code' => $exitCode,
                'output_tail' => implode("\n", array_slice($output, -20)),
                'path' => $backupPath,
            ]);
            return [
                'success' => false,
                'message' => 'Error al ejecutar mysqldump.',
                'path' => $backupPath,
                'output' => $output,
                'exit_code' => $exitCode,
            ];
        }

        if (!file_exists($backupPath) || filesize($backupPath) === 0) {
            Log::error('mysqldump terminó pero no generó el archivo esperado', [
                'exit_code' => $exitCode,
                'output_tail' => implode("\n", array_slice($output, -20)),
                'path' => $backupPath,
                'exists' => file_exists($backupPath),
                'size' => file_exists($backupPath) ? filesize($backupPath) : null,
            ]);
            return [
                'success' => false,
                'message' => 'mysqldump finalizó pero no se generó el archivo .sql.',
                'path' => $backupPath,
                'output' => $output,
                'exit_code' => 1,
            ];
        }

        return [
            'success' => true,
            'message' => 'Respaldo creado correctamente.',
            'path' => $backupPath,
            'output' => $output,
            'exit_code' => 0,
        ];
    }
}
