<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

class RepairSystem extends Command
{
    protected $signature = 'app:repair {--force : Ejecuta reparaciones incluso si el entorno no es local}';

    protected $description = 'Repara estructura base de la BD y sincroniza perfiles con users.user_id';

    public function handle(): int
    {
        $env = (string) config('app.env');
        $isLocal = in_array($env, ['local', 'development'], true);
        if (!$isLocal && !$this->option('force')) {
            $this->error('Comando bloqueado fuera de local. Usa --force si estás seguro.');
            return self::FAILURE;
        }

        $this->info('Iniciando reparación del sistema...');

        try {
            if (!Schema::hasColumn('users', 'role')) {
                DB::statement("ALTER TABLE users ADD COLUMN role VARCHAR(255) DEFAULT 'alumno' AFTER email");
                $this->info("Agregada columna users.role");
            }

            $tablas = ['alumnos', 'tutores', 'servicios_escolares'];
            foreach ($tablas as $tabla) {
                if (!Schema::hasColumn($tabla, 'user_id')) {
                    DB::statement("ALTER TABLE $tabla ADD COLUMN user_id BIGINT UNSIGNED NULL");
                    $this->info("Agregada columna {$tabla}.user_id");
                }
            }

            $adminEmail = 'admin@admin.com';
            $adminPassword = 'Admin123*';
            $adminUser = User::updateOrCreate(
                ['email' => $adminEmail],
                ['name' => 'Administrador Sistema', 'password' => Hash::make($adminPassword), 'role' => 'admin']
            );
            $this->info("Admin base asegurado: {$adminUser->email}");

            $this->syncProfileUserIds('servicios_escolares', 'idServicios_Escolares', 'Correo', 'Admin ');
            $this->syncProfileUserIds('tutores', 'idTutores', 'Correo_inst', 'Tutor ');
            $this->syncProfileUserIds('alumnos', 'idAlumnos', 'Correo_inst', 'Alumno ');

            $this->info('Reparación completada.');
            $this->line("Credenciales admin base (si no existía): {$adminEmail} / {$adminPassword}");

            return self::SUCCESS;
        } catch (\Throwable $e) {
            $this->error('Error: ' . $e->getMessage());
            return self::FAILURE;
        }
    }

    private function syncProfileUserIds(string $table, string $pk, string $emailCol, string $namePrefix): void
    {
        if (!Schema::hasTable($table)) {
            return;
        }

        $rows = DB::table($table)->whereNull('user_id')->get();
        foreach ($rows as $row) {
            $email = $row->{$emailCol} ?? null;
            if (!is_string($email) || $email === '') {
                continue;
            }

            $user = User::where('email', $email)->first();
            if (!$user) {
                $role = $table === 'servicios_escolares' ? 'admin' : ($table === 'tutores' ? 'tutor' : 'alumno');
                $user = User::create([
                    'name' => $namePrefix . ($row->Clave_Trabajador ?? $row->Matricula ?? $row->{$pk}),
                    'email' => $email,
                    'password' => Hash::make('Admin123*'),
                    'role' => $role,
                ]);
            }

            DB::table($table)->where($pk, $row->{$pk})->update(['user_id' => $user->id]);
        }
    }
}

