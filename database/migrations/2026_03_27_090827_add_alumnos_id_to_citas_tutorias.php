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
        Schema::table('citas_tutorias', function (Blueprint $table) {
            $table->unsignedInteger('Alumnos_id')->nullable()->after('Tutores_id');
            $table->foreign('Alumnos_id')->references('idAlumnos')->on('alumnos')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('citas_tutorias', function (Blueprint $table) {
            $table->dropForeign(['Alumnos_id']);
            $table->dropColumn('Alumnos_id');
        });
    }
};
