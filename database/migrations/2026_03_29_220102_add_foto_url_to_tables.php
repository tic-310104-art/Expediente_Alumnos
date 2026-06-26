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
        Schema::table('alumnos', function (Blueprint $table) {
            $table->string('foto_url', 255)->nullable()->after('Password');
        });

        Schema::table('tutores', function (Blueprint $table) {
            $table->string('foto_url', 255)->nullable()->after('Password');
        });

        Schema::table('servicios_escolares', function (Blueprint $table) {
            $table->string('foto_url', 255)->nullable()->after('Password');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('alumnos', function (Blueprint $table) {
            $table->dropColumn('foto_url');
        });

        Schema::table('tutores', function (Blueprint $table) {
            $table->dropColumn('foto_url');
        });

        Schema::table('servicios_escolares', function (Blueprint $table) {
            $table->dropColumn('foto_url');
        });
    }
};
