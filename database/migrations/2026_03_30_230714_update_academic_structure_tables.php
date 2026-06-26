<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Relacionar Grupos con Carreras y Tutor
        Schema::table('grupos', function (Blueprint $table) {
            if (!Schema::hasColumn('grupos', 'idCarreras')) {
                $table->unsignedInteger('idCarreras')->nullable();
                $table->foreign('idCarreras')->references('idCarreras')->on('carreras')->onDelete('cascade');
            }
            if (!Schema::hasColumn('grupos', 'idTutores')) {
                $table->unsignedInteger('idTutores')->nullable();
                $table->foreign('idTutores')->references('idTutores')->on('tutores')->onDelete('set null');
            }
        });

        // 2. Relacionar Tutores con Carreras
        Schema::table('tutores', function (Blueprint $table) {
            if (!Schema::hasColumn('tutores', 'idCarreras')) {
                $table->unsignedInteger('idCarreras')->nullable();
                $table->foreign('idCarreras')->references('idCarreras')->on('carreras')->onDelete('set null');
            }
        });

        // 3. Crear tabla de Carga Académica para el Grupo (Matriz de Materia-Maestro)
        if (!Schema::hasTable('grupo_materias')) {
            Schema::create('grupo_materias', function (Blueprint $table) {
                $table->increments('id');
                $table->unsignedInteger('idGrupos');
                $table->unsignedInteger('idMateria');
                $table->string('Maestro')->nullable();
                $table->string('Horario')->nullable();
                $table->timestamps();

                $table->foreign('idGrupos')->references('idGrupos')->on('grupos')->onDelete('cascade');
                $table->foreign('idMateria')->references('idMateria')->on('materias')->onDelete('cascade');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('grupo_materias');
        
        Schema::table('tutores', function (Blueprint $table) {
            $table->dropColumn('idCarreras');
        });

        Schema::table('grupos', function (Blueprint $table) {
            $table->dropColumn(['idCarreras', 'idTutores']);
        });
    }
};
