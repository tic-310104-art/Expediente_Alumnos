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
        Schema::create('reportes_desempeno', function (Blueprint $table) {
            $table->increments('idReporte');
            $table->unsignedInteger('Alumno_id');
            $table->unsignedInteger('Tutor_id');
            $table->date('Fecha');
            $table->string('Nivel_Riesgo')->comment('Bajo, Medio, Alto');
            $table->text('Observaciones')->nullable();
            $table->text('Recomendaciones')->nullable();
            $table->timestamps();

            $table->foreign('Alumno_id')->references('idAlumnos')->on('alumnos')->onDelete('cascade');
            $table->foreign('Tutor_id')->references('idTutores')->on('tutores')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reportes_desempeno');
    }
};
