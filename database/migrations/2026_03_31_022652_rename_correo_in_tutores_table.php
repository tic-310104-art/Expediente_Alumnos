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
        Schema::table('tutores', function (Blueprint $table) {
            if (Schema::hasColumn('tutores', 'Correo') && !Schema::hasColumn('tutores', 'Correo_inst')) {
                $table->renameColumn('Correo', 'Correo_inst');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tutores', function (Blueprint $table) {
            if (Schema::hasColumn('tutores', 'Correo_inst') && !Schema::hasColumn('tutores', 'Correo')) {
                $table->renameColumn('Correo_inst', 'Correo');
            }
        });
    }
};
