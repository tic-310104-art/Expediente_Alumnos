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
        Schema::table('servicios_escolares', function (Blueprint $table) {
            if (Schema::hasColumn('servicios_escolares', 'Correo') && !Schema::hasColumn('servicios_escolares', 'Correo_inst')) {
                $table->renameColumn('Correo', 'Correo_inst');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('servicios_escolares', function (Blueprint $table) {
            if (Schema::hasColumn('servicios_escolares', 'Correo_inst') && !Schema::hasColumn('servicios_escolares', 'Correo')) {
                $table->renameColumn('Correo_inst', 'Correo');
            }
        });
    }
};
