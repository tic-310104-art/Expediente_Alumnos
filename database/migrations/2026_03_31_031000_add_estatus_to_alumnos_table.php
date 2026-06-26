<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('alumnos', function (Blueprint $table) {
            if (!Schema::hasColumn('alumnos', 'Estatus')) {
                $table->string('Estatus')->default('activo')->after('Telefono');
            }
        });
    }

    public function down(): void
    {
        Schema::table('alumnos', function (Blueprint $table) {
            if (Schema::hasColumn('alumnos', 'Estatus')) {
                $table->dropColumn('Estatus');
            }
        });
    }
};

