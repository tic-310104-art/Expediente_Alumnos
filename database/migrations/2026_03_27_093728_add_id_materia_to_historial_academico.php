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
        Schema::table('historial_academico', function (Blueprint $table) {
            $table->unsignedInteger('idMateria')->nullable()->after('idHistorial');
            $table->foreign('idMateria')->references('idMateria')->on('materias')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('historial_academico', function (Blueprint $table) {
            $table->dropForeign(['idMateria']);
            $table->dropColumn('idMateria');
        });
    }
};
