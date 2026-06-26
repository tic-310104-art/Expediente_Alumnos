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
        Schema::create('becas', function (Blueprint $table) {
            $table->increments('idBecas');
            $table->string('Nombre');
            $table->text('Descripcion')->nullable();
            $table->decimal('Monto', 10, 2)->nullable();
            $table->timestamps();
        });

        Schema::create('alumno_beca', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('Alumno_id');
            $table->unsignedInteger('Beca_id');
            $table->date('Fecha_Asignacion')->nullable();
            $table->timestamps();

            $table->foreign('Alumno_id')->references('idAlumnos')->on('alumnos')->onDelete('cascade');
            $table->foreign('Beca_id')->references('idBecas')->on('becas')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('alumno_beca');
        Schema::dropIfExists('becas');
    }
};
