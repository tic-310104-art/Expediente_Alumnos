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
        if (!Schema::hasColumn('alumnos', 'user_id')) {
            Schema::table('alumnos', function (Blueprint $row) {
                $row->foreignId('user_id')->nullable()->constrained('users')->onDelete('cascade');
            });
        }

        if (!Schema::hasColumn('tutores', 'user_id')) {
            Schema::table('tutores', function (Blueprint $row) {
                $row->foreignId('user_id')->nullable()->constrained('users')->onDelete('cascade');
            });
        }

        if (!Schema::hasColumn('servicios_escolares', 'user_id')) {
            Schema::table('servicios_escolares', function (Blueprint $row) {
                $row->foreignId('user_id')->nullable()->constrained('users')->onDelete('cascade');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('alumnos', function (Blueprint $row) {
            $row->dropColumn('user_id');
        });

        Schema::table('tutores', function (Blueprint $row) {
            $row->dropColumn('user_id');
        });

        Schema::table('servicios_escolares', function (Blueprint $row) {
            $row->dropColumn('user_id');
        });
    }
};
