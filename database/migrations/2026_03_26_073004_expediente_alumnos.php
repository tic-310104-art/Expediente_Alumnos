<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        //  SERVICIOS ESCOLARES
        Schema::create('servicios_escolares', function (Blueprint $table) {
            $table->increments('idServicios_Escolares');
            $table->string('Clave_Trabajador')->nullable();
            $table->string('Correo')->nullable();
            $table->string('Telefono')->nullable();
            $table->string('Rol')->nullable();
            $table->string('Password')->nullable();
        });

        //  GRUPOS
        Schema::create('grupos', function (Blueprint $table) {
            $table->increments('idGrupos');
            $table->string('Grupo')->nullable();
            $table->integer('Cantidad_Alumnos')->nullable();
        });

        //  TUTORES
        Schema::create('tutores', function (Blueprint $table) {
            $table->increments('idTutores');
            $table->string('Nombre')->nullable();
            $table->string('Apellido')->nullable();
            $table->string('Clave_Trabajador')->nullable();
            $table->string('Correo')->nullable();
            $table->string('Telefono')->nullable();
            $table->string('Rol')->nullable();
            $table->string('Password')->nullable();

            $table->unsignedInteger('Servicios_Escolares_id')->nullable();
            $table->foreign('Servicios_Escolares_id')
                  ->references('idServicios_Escolares')
                  ->on('servicios_escolares');
        });

        //  CARRERAS
        Schema::create('carreras', function (Blueprint $table) {
            $table->increments('idCarreras');
            $table->string('Nombre')->nullable();

            $table->unsignedInteger('Servicios_Escolares_id')->nullable();
            $table->foreign('Servicios_Escolares_id')
                  ->references('idServicios_Escolares')
                  ->on('servicios_escolares');
        });

        //  ALUMNOS
        Schema::create('alumnos', function (Blueprint $table) {
            $table->increments('idAlumnos');
            $table->string('Nombre')->nullable();
            $table->string('Apellido')->nullable();
            $table->string('Cuatrimestre')->nullable();
            $table->string('Matricula')->nullable();
            $table->string('Correo_inst')->nullable();
            $table->string('Telefono')->nullable();
            $table->string('Rol')->nullable();
            $table->string('Password')->nullable();

            $table->unsignedInteger('Grupos_id')->nullable();
            $table->unsignedInteger('Tutores_id')->nullable();
            $table->unsignedInteger('Servicios_Escolares_id')->nullable();

            $table->foreign('Grupos_id')->references('idGrupos')->on('grupos');
            $table->foreign('Tutores_id')->references('idTutores')->on('tutores');
            $table->foreign('Servicios_Escolares_id')->references('idServicios_Escolares')->on('servicios_escolares');
        });

        //  ASESORIA
        Schema::create('asesoria', function (Blueprint $table) {
            $table->increments('idAsesoria');
            $table->string('Motivo')->nullable();
            $table->string('Fecha')->nullable();
        });

        //  ALUMNOS_ASESORIA
        Schema::create('alumnos_asesoria', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('Alumno_id')->nullable();
            $table->unsignedInteger('Asesoria_id')->nullable();

            $table->foreign('Alumno_id')->references('idAlumnos')->on('alumnos');
            $table->foreign('Asesoria_id')->references('idAsesoria')->on('asesoria');
        });

        //  CARRERAS_ALUMNOS
        Schema::create('carreras_alumnos', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('Carreras_id')->nullable();
            $table->unsignedInteger('Alumnos_id')->nullable();

            $table->foreign('Carreras_id')->references('idCarreras')->on('carreras');
            $table->foreign('Alumnos_id')->references('idAlumnos')->on('alumnos');
        });

        //  CITAS PSICOLOGIA
        Schema::create('citas_psicologia', function (Blueprint $table) {
            $table->increments('idCita');
            $table->string('Fecha')->nullable();
            $table->string('Asistencia')->nullable();

            $table->unsignedInteger('Tutores_id')->nullable();
            $table->foreign('Tutores_id')->references('idTutores')->on('tutores');
        });

        //  CITAS PSICOLOGIA ALUMNOS
        Schema::create('citas_psicologia_alumnos', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('Cita_id')->nullable();
            $table->unsignedInteger('Alumno_id')->nullable();

            $table->foreign('Cita_id')->references('idCita')->on('citas_psicologia');
            $table->foreign('Alumno_id')->references('idAlumnos')->on('alumnos');
        });

        //  CITAS TUTORIAS
        Schema::create('citas_tutorias', function (Blueprint $table) {
            $table->increments('idCitas');
            $table->string('Fecha')->nullable();
            $table->string('Motivo')->nullable();

            $table->unsignedInteger('Tutores_id')->nullable();
            $table->foreign('Tutores_id')->references('idTutores')->on('tutores');
        });

        //  HISTORIAL ACADEMICO
        Schema::create('historial_academico', function (Blueprint $table) {
            $table->increments('idHistorial');
            $table->string('Materia')->nullable();
            $table->string('Profesor')->nullable();
            $table->string('Calificacion')->nullable();
            $table->string('Horario')->nullable();
            $table->string('Ciclo')->nullable();

            $table->unsignedInteger('Alumno_id')->nullable();
            $table->foreign('Alumno_id')->references('idAlumnos')->on('alumnos');
        });
    }

    public function down()
    {
        Schema::dropIfExists('historial_academico');
        Schema::dropIfExists('citas_tutorias');
        Schema::dropIfExists('citas_psicologia_alumnos');
        Schema::dropIfExists('citas_psicologia');
        Schema::dropIfExists('carreras_alumnos');
        Schema::dropIfExists('alumnos_asesoria');
        Schema::dropIfExists('asesoria');
        Schema::dropIfExists('alumnos');
        Schema::dropIfExists('carreras');
        Schema::dropIfExists('tutores');
        Schema::dropIfExists('grupos');
        Schema::dropIfExists('servicios_escolares');
    }
};