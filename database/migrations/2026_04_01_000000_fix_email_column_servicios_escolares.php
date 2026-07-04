<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $tableName = 'servicios_escolares';
        
        // Verificar si la tabla existe
        if (!Schema::hasTable($tableName)) {
            return;
        }
        
        // Caso 1: Si existe 'Correo' pero no 'Email', dejar como está
        if (Schema::hasColumn($tableName, 'Correo') && !Schema::hasColumn($tableName, 'Email')) {
            info("Columna 'Correo' existe, 'Email' no existe. Manteniendo estructura actual.");
            return;
        }
        
        // Caso 2: Si existe 'Email' pero no 'Correo', renombrar 'Email' a 'Correo'
        if (Schema::hasColumn($tableName, 'Email') && !Schema::hasColumn($tableName, 'Correo')) {
            info("Renombrando columna 'Email' a 'Correo' en servicios_escolares...");
            
            // Obtener los datos antes de renombrar
            $emails = DB::table($tableName)->pluck('Email', 'idServicios_Escolares');
            
            // Renombrar la columna
            Schema::table($tableName, function (Blueprint $table) {
                $table->renameColumn('Email', 'Correo');
            });
            
            info("Columna renombrada exitosamente.");
            return;
        }
        
        // Caso 3: Si ambas existen, mantener 'Correo' y eliminar 'Email'
        if (Schema::hasColumn($tableName, 'Correo') && Schema::hasColumn($tableName, 'Email')) {
            info("Ambas columnas existen. Eliminando 'Email' y manteniendo 'Correo'...");
            
            // Migrar datos de Email a Correo si Correo está vacío
            DB::table($tableName)
                ->whereNull('Correo')
                ->whereNotNull('Email')
                ->update(['Correo' => DB::raw('Email')]);
            
            // Eliminar la columna Email
            Schema::table($tableName, function (Blueprint $table) {
                $table->dropColumn('Email');
            });
            
            info("Columna 'Email' eliminada, datos migrados a 'Correo'.");
            return;
        }
        
        // Caso 4: Si ninguna existe, crear 'Correo'
        if (!Schema::hasColumn($tableName, 'Correo') && !Schema::hasColumn($tableName, 'Email')) {
            info("Ninguna columna existe. Creando 'Correo'...");
            
            // Crear la columna
            Schema::table($tableName, function (Blueprint $table) {
                $table->string('Correo')->nullable()->after('Clave_Trabajador');
            });
            
            info("Columna 'Correo' creada.");
            return;
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // En caso de rollback, no hacer nada para evitar perder datos
        info("Rollback: No se realizarán cambios para proteger los datos.");
    }
};