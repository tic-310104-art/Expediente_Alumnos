<?php

namespace App\Console\Commands;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

#[Signature('db:backup')]
#[Description('Realiza un respaldo de la base de datos MySQL')]
class BackupDatabase extends Command
{
    /**
     * Execute the console command.
     */
    public function handle()
    {
        $filename = "expediente_" . date("Y-m-d_His") . ".sql";
        $path = env('BACKUP_PATH', 'C:\Users\gilbe\Downloads\TRABAJOS\respaldo_automatizado_expediente_alumnos');
        
        if (!file_exists($path)) {
            mkdir($path, 0777, true);
        }

        $fullPath = $path . DIRECTORY_SEPARATOR . $filename;
        
        $dbHost = env('DB_HOST', '127.0.0.1');
        $dbName = env('DB_DATABASE', 'expediente');
        $dbUser = env('DB_USERNAME', 'root');
        $dbPass = env('DB_PASSWORD', '');

        // Intentar encontrar mysqldump en Laragon
        $mysqldump = 'C:\laragon\bin\mysql\mysql-8.0.30-winx64\bin\mysqldump.exe';
        if (!file_exists($mysqldump)) {
            $mysqldump = 'mysqldump'; // Intentar en el PATH
        }

        $command = sprintf(
            '"%s" --user=%s --password=%s --host=%s %s > "%s"',
            $mysqldump,
            $dbUser,
            $dbPass,
            $dbHost,
            $dbName,
            $fullPath
        );

        $output = [];
        $returnVar = null;
        exec($command, $output, $returnVar);

        if ($returnVar === 0) {
            $this->info("✅ Respaldo creado con éxito: $filename");
            $this->info("📍 Ubicación: $fullPath");
            
            // Registrar en log
            Log::info("Respaldo de base de datos creado: $filename");
            
            // Limpiar respaldos antiguos (mantener últimos 10)
            $this->cleanOldBackups($path);
            
            return 0;
        } else {
            $this->error("❌ Error al crear el respaldo. Código de error: $returnVar");
            Log::error("Error al crear respaldo de base de datos. Código: $returnVar");
            return 1;
        }
    }
    
    /**
     * Limpia respaldos antiguos manteniendo solo los últimos 10
     */
    private function cleanOldBackups($backupPath)
    {
        $files = glob($backupPath . '/expediente_*.sql');
        
        if (count($files) <= 10) {
            return;
        }
        
        // Ordenar archivos por fecha de modificación (más nuevos primero)
        usort($files, function($a, $b) {
            return filemtime($b) - filemtime($a);
        });
        
        // Eliminar archivos antiguos (mantener solo los primeros 10)
        $filesToDelete = array_slice($files, 10);
        $deletedCount = 0;
        
        foreach ($filesToDelete as $file) {
            if (unlink($file)) {
                $deletedCount++;
                $this->info("🗑️  Respaldo antiguo eliminado: " . basename($file));
            }
        }
        
        if ($deletedCount > 0) {
            $this->info("🧹 Se eliminaron $deletedCount respaldos antiguos.");
            Log::info("Se eliminaron $deletedCount respaldos antiguos de la base de datos.");
        }
    }
}
