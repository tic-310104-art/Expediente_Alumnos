<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DiagnoseDatabase extends Command
{
    protected $signature = 'db:diagnose {table? : Tabla a diagnosticar}';
    protected $description = 'Diagnostica la estructura de la base de datos para encontrar inconsistencias';

    public function handle()
    {
        $table = $this->argument('table') ?? 'servicios_escolares';
        
        $this->info("Diagnosticando tabla: {$table}");
        
        if (!Schema::hasTable($table)) {
            $this->error("La tabla {$table} no existe");
            return Command::FAILURE;
        }
        
        // Obtener columnas de la tabla
        $columns = DB::select("SHOW COLUMNS FROM {$table}");
        
        $this->info("\nColumnas encontradas:");
        foreach ($columns as $column) {
            $this->line("  - {$column->Field} ({$column->Type})");
        }
        
        // Buscar columnas relacionadas con email
        $emailColumns = array_filter($columns, function($column) {
            return stripos($column->Field, 'email') !== false || stripos($column->Field, 'correo') !== false;
        });
        
        if (!empty($emailColumns)) {
            $this->info("\nColumnas relacionadas con email/correo:");
            foreach ($emailColumns as $column) {
                $this->line("  - {$column->Field} ({$column->Type})");
            }
        } else {
            $this->warn("\nNo se encontraron columnas relacionadas con email/correo");
        }
        
        // Mostrar algunos datos de ejemplo
        try {
            $sample = DB::table($table)->limit(3)->get();
            if ($sample->count() > 0) {
                $this->info("\nDatos de ejemplo:");
                foreach ($sample as $row) {
                    $this->line("  ID: {$row->idServicios_Escolares ?? $row->id ?? 'N/A'}");
                    foreach ($emailColumns as $column) {
                        $field = $column->Field;
                        $value = $row->{$field} ?? 'N/A';
                        $this->line("  {$field}: {$value}");
                    }
                    $this->line("");
                }
            }
        } catch (\Exception $e) {
            $this->error("Error al obtener datos: " . $e->getMessage());
        }
        
        return Command::SUCCESS;
    }
}
