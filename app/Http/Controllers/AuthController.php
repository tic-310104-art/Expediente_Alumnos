<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Alumno;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Utils\JWT;

class AuthController extends Controller
{
    private const EMERGENCY_EMAIL = 'admin@sistema.edu.mx';

    /**
     * Asegura que la base de datos tenga las columnas necesarias.
     * Útil cuando no se pueden correr migraciones por terminal.
     */
    private function checkAndFixDatabase()
    {
        try {
            // 1. Verificar columnas básicas
            if (!\Illuminate\Support\Facades\Schema::hasColumn('users', 'role')) {
                \Illuminate\Support\Facades\DB::statement("ALTER TABLE users ADD COLUMN role VARCHAR(255) DEFAULT 'alumno' AFTER email");
            }
            
            $tablas = ['alumnos', 'tutores', 'servicios_escolares'];
            foreach ($tablas as $tabla) {
                if (!\Illuminate\Support\Facades\Schema::hasColumn($tabla, 'user_id')) {
                    \Illuminate\Support\Facades\DB::statement("ALTER TABLE $tabla ADD COLUMN user_id BIGINT UNSIGNED NULL");
                }
                if (!\Illuminate\Support\Facades\Schema::hasColumn($tabla, 'foto_url')) {
                    \Illuminate\Support\Facades\DB::statement("ALTER TABLE $tabla ADD COLUMN foto_url VARCHAR(255) NULL");
                }
            }

            // 1.1 Asegurar columna details en log_activities
            if (\Illuminate\Support\Facades\Schema::hasTable('log_activities')) {
                if (!\Illuminate\Support\Facades\Schema::hasColumn('log_activities', 'details')) {
                    \Illuminate\Support\Facades\DB::statement("ALTER TABLE log_activities ADD COLUMN details TEXT NULL AFTER subject");
                }
            }

            // 1.2 Asegurar tabla backup_schedules
            if (!\Illuminate\Support\Facades\Schema::hasTable('backup_schedules')) {
                \Illuminate\Support\Facades\Schema::create('backup_schedules', function ($table) {
                    $table->id();
                    $table->date('scheduled_date');
                    $table->time('scheduled_time');
                    $table->string('frequency')->default('once'); // once, 4_days, 7_days, monthly
                    $table->boolean('is_active')->default(true);
                    $table->timestamp('last_run_at')->nullable();
                    $table->timestamps();
                });
            }

            // 2. Asegurar existencia de Administrador Base
            $adminEmail = 'admin@admin.com';
            $adminUser = \App\Models\User::where('email', $adminEmail)->first();
            
            if (!$adminUser) {
                $adminUser = \App\Models\User::create([
                    'name' => 'Administrador Sistema',
                    'email' => $adminEmail,
                    'password' => \Illuminate\Support\Facades\Hash::make('Admin123*'),
                    'role' => 'admin',
                ]);
            }

            // 2.1 Asegurar que el admin tenga perfil en servicios_escolares
            if ($adminUser) {
                $profile = \App\Models\ServicioEscolar::where('user_id', $adminUser->id)->first();
                if (!$profile) {
                    \App\Models\ServicioEscolar::create([
                        'Clave_Trabajador' => 'ADMIN_BASE',
                        'Correo' => $adminEmail,
                        'Rol' => 'Administrador',
                        'Password' => $adminUser->password, // Misma contraseña
                        'user_id' => $adminUser->id
                    ]);
                }
            }

            // 3. Sincronizar perfiles existentes que no tienen user_id
            $servicios = \Illuminate\Support\Facades\DB::table('servicios_escolares')->whereNull('user_id')->get();
            foreach ($servicios as $s) {
                // Intentar obtener el correo de cualquier columna posible
                $correo = $s->Correo ?? $s->Correo_inst ?? $s->Email ?? null; 
                if (!$correo) continue;
                
                $user = \App\Models\User::where('email', $correo)->first();
                if (!$user) {
                    $user = \App\Models\User::create([
                        'name' => 'Admin ' . $s->Clave_Trabajador,
                        'email' => $correo,
                        'password' => \Illuminate\Support\Facades\Hash::make('Admin123*'),
                        'role' => 'admin',
                    ]);
                }
                \Illuminate\Support\Facades\DB::table('servicios_escolares')
                    ->where('idServicios_Escolares', $s->idServicios_Escolares)
                    ->update(['user_id' => $user->id]);
            }

        } catch (\Exception $e) {
            // Silencioso si falla
        }
    }

    /**
     * Mostrar vista de login
     */
    public function showLogin()
    {
        app()->setLocale('es');
        if (\Illuminate\Support\Facades\Auth::check()) {
            return $this->redirectBasedOnRole(\Illuminate\Support\Facades\Auth::user());
        }
        $this->checkAndFixDatabase();
        return view('login');
    }

    public function verifyCriticalAction(Request $request)
    {
        $request->validate([
            'password' => 'required'
        ]);

        if (Hash::check($request->password, Auth::user()->password)) {
            // Generar un JWT real de 8 horas usando nuestra nueva utilidad
            $token = JWT::encode([
                'user_id' => Auth::id(),
                'role' => Auth::user()->role,
                'action' => 'critical'
            ], 8);

            session(['critical_token' => $token]);

            return response()->json([
                'success' => true,
                'token' => $token
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => __('Contraseña incorrecta')
        ], 401);
    }

    public function login(Request $request)
    {
        app()->setLocale('es');
        $this->checkAndFixDatabase();

        $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required'],
        ]);

        $email    = $request->email;
        $password = $request->password;

        // ── 1. Buscar en SERVICIOS ESCOLARES (Admin) ──────────────────────
        $admin = \App\Models\ServicioEscolar::byEmail($email)->first();
        
        if ($admin && \Illuminate\Support\Facades\Hash::check($password, $admin->Password)) {
            $user = $this->syncUser($email, $admin->Password, 'admin', 'Admin ' . $admin->Clave_Trabajador);
            \Illuminate\Support\Facades\DB::table('servicios_escolares')
                ->where('idServicios_Escolares', $admin->idServicios_Escolares)
                ->update(['user_id' => $user->id]);
            
            return $this->handleLoginSuccess($request, $user);
        }

        // ── 2. Buscar en TUTORES ───────────────────────────────────────────
        $tutorQuery = \App\Models\Tutor::query();
        if (\Illuminate\Support\Facades\Schema::hasColumn('tutores', 'Correo_inst')) {
            $tutorQuery->where('Correo_inst', $email);
        }
        if (\Illuminate\Support\Facades\Schema::hasColumn('tutores', 'Correo')) {
            $tutorQuery->orWhere('Correo', $email);
        }
        $tutor = $tutorQuery->first();
        if ($tutor && \Illuminate\Support\Facades\Hash::check($password, $tutor->Password)) {
            $user = $this->syncUser($email, $tutor->Password, 'tutor', $tutor->Nombre . ' ' . $tutor->Apellido);
            \Illuminate\Support\Facades\DB::table('tutores')
                ->where('idTutores', $tutor->idTutores)
                ->update(['user_id' => $user->id]);
            
            return $this->handleLoginSuccess($request, $user);
        }

        // ── 3. Buscar en ALUMNOS ───────────────────────────────────────────
        $alumnoQuery = \App\Models\Alumno::query();
        if (\Illuminate\Support\Facades\Schema::hasColumn('alumnos', 'Correo_inst')) {
            $alumnoQuery->where('Correo_inst', $email);
        }
        if (\Illuminate\Support\Facades\Schema::hasColumn('alumnos', 'Correo')) {
            $alumnoQuery->orWhere('Correo', $email);
        }
        $alumno = $alumnoQuery->first();
        if ($alumno && \Illuminate\Support\Facades\Hash::check($password, $alumno->Password)) {
            $user = $this->syncUser($email, $alumno->Password, 'alumno', $alumno->Nombre . ' ' . $alumno->Apellido);
            \Illuminate\Support\Facades\DB::table('alumnos')
                ->where('idAlumnos', $alumno->idAlumnos)
                ->update(['user_id' => $user->id]);
            
            return $this->handleLoginSuccess($request, $user);
        }

        // ── 4. Fallback: intentar con la tabla users ───────────────────────
        // Si el correo solo existe en users pero ya no existe perfil (alumno/tutor/admin),
        // se considera cuenta inexistente para evitar redirecciones a rutas inválidas.
        if (\App\Models\User::where('email', $email)->exists()) {
            $emailExistsAdmin  = \App\Models\ServicioEscolar::byEmail($email)->exists();

            $emailExistsTutorQuery = \App\Models\Tutor::query()->whereRaw('0=1');
            if (\Illuminate\Support\Facades\Schema::hasColumn('tutores', 'Correo_inst')) {
                $emailExistsTutorQuery->orWhere('Correo_inst', $email);
            }
            if (\Illuminate\Support\Facades\Schema::hasColumn('tutores', 'Correo')) {
                $emailExistsTutorQuery->orWhere('Correo', $email);
            }
            $emailExistsTutor = $emailExistsTutorQuery->exists();

            $emailExistsAlumnoQuery = \App\Models\Alumno::query()->whereRaw('0=1');
            if (\Illuminate\Support\Facades\Schema::hasColumn('alumnos', 'Correo_inst')) {
                $emailExistsAlumnoQuery->orWhere('Correo_inst', $email);
            }
            if (\Illuminate\Support\Facades\Schema::hasColumn('alumnos', 'Correo')) {
                $emailExistsAlumnoQuery->orWhere('Correo', $email);
            }
            $emailExistsAlumno = $emailExistsAlumnoQuery->exists();

            if (!$emailExistsAdmin && !$emailExistsTutor && !$emailExistsAlumno) {
                return redirect()->route('login')->withErrors([
                    'email' => 'No existe ninguna cuenta registrada con este correo electrónico.',
                ])->withInput($request->only('email'));
            }
        }

        if (\Illuminate\Support\Facades\Auth::attempt(['email' => $email, 'password' => $password])) {
            $logged = \Illuminate\Support\Facades\Auth::user();
            
            if ($logged?->role === 'alumno' && !$logged->alumno) {
                \Illuminate\Support\Facades\Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                return redirect()->route('login')->withErrors([
                    'email' => 'No existe ninguna cuenta registrada con este correo electrónico.',
                ])->withInput($request->only('email'));
            }

            if ($logged?->role === 'tutor' && !$logged->tutor) {
                \Illuminate\Support\Facades\Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                return redirect()->route('login')->withErrors([
                    'email' => 'No existe ninguna cuenta registrada con este correo electrónico.',
                ])->withInput($request->only('email'));
            }

            if ($logged?->role === 'admin' && !$logged->servicioEscolar) {
                \Illuminate\Support\Facades\Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                return redirect()->route('login')->withErrors([
                    'email' => 'No existe ninguna cuenta registrada con este correo electrónico.',
                ])->withInput($request->only('email'));
            }

            return $this->handleLoginSuccess($request, $logged);
        }

        // ── 5. Determinar si el correo existe en alguna tabla ──────────────
        $emailExistsAdmin  = \App\Models\ServicioEscolar::byEmail($email)->exists();

        $emailExistsTutorQuery = \App\Models\Tutor::query()->whereRaw('0=1');
        if (\Illuminate\Support\Facades\Schema::hasColumn('tutores', 'Correo_inst')) {
            $emailExistsTutorQuery = \App\Models\Tutor::query()->where('Correo_inst', $email);
        }
        if (\Illuminate\Support\Facades\Schema::hasColumn('tutores', 'Correo')) {
            $emailExistsTutorQuery->orWhere('Correo', $email);
        }
        $emailExistsTutor = $emailExistsTutorQuery->exists();

        $emailExistsAlumnoQuery = \App\Models\Alumno::query()->whereRaw('0=1');
        if (\Illuminate\Support\Facades\Schema::hasColumn('alumnos', 'Correo_inst')) {
            $emailExistsAlumnoQuery = \App\Models\Alumno::query()->where('Correo_inst', $email);
        }
        if (\Illuminate\Support\Facades\Schema::hasColumn('alumnos', 'Correo')) {
            $emailExistsAlumnoQuery->orWhere('Correo', $email);
        }
        $emailExistsAlumno = $emailExistsAlumnoQuery->exists();

        if (!$emailExistsAdmin && !$emailExistsTutor && !$emailExistsAlumno) {
            return redirect()->route('login')->withErrors([
                'email' => 'No existe ninguna cuenta registrada con este correo electrónico.',
            ])->withInput($request->only('email'));
        }

        // ── 6. EMERGENCIA: Administrador hardcodeado ──
        $emergencyPassword = env('EMERGENCY_PASSWORD');
        if ($email === self::EMERGENCY_EMAIL && $emergencyPassword && $password === $emergencyPassword) {
            session(['emergency_admin' => true]);

            $user = User::make([
                'name' => 'Administrador de Emergencia',
                'email' => self::EMERGENCY_EMAIL,
                'role' => 'admin',
                'password' => Hash::make($emergencyPassword),
            ]);
            $user->id = PHP_INT_MAX;

            return $this->handleLoginSuccess($request, $user);
        }

        return redirect()->route('login')->withErrors([
            'email' => 'La contraseña es incorrecta. Por favor, inténtalo de nuevo.',
        ])->withInput($request->only('email'));
    }

    /**
     * Procesa el éxito del inicio de sesión: regenera sesión y crea token JWT.
     */
    private function handleLoginSuccess(Request $request, User $user)
    {
        \Illuminate\Support\Facades\Auth::login($user);
        $request->session()->regenerate();

        // Generar JWT automáticamente al iniciar sesión
        // Esto permite que el usuario realice acciones protegidas por el middleware 'critical'
        // sin tener que generar manualmente un token cada vez.
        $token = JWT::encode([
            'user_id' => $user->id,
            'role' => $user->role,
            'iat' => time(),
            'exp' => time() + (8 * 3600) // 8 horas
        ]);

        session(['critical_token' => $token]);

        return $this->redirectBasedOnRole($user);
    }

    /**
     * Crea o actualiza un registro en la tabla 'users' para mantener la sesión de Laravel.
     * Sincroniza el rol y los datos del perfil externo con la tabla de autenticación.
     */
    private function syncUser(string $email, string $hashedPassword, string $role, string $name): User
    {
        return User::updateOrCreate(
            ['email' => $email],
            [
                'name'     => $name,
                'password' => $hashedPassword,
                'role'     => $role,
            ]
        );
    }

    public function register(Request $request)
    {
        $this->checkAndFixDatabase();
        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'Nombre' => 'required|string|max:255',
            'Apellido' => 'required|string|max:255',
            'Matricula' => 'required|string|unique:alumnos,Matricula|max:20',
            'email' => 'required|string|email|max:255|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $hashedPassword = \Illuminate\Support\Facades\Hash::make($request->password);

        $user = User::create([
            'name'     => $request->Nombre . ' ' . $request->Apellido,
            'email'    => $request->email,
            'password' => $hashedPassword,
            'role'     => 'alumno',
        ]);

        $alumno = Alumno::create([
            'Nombre'       => $request->Nombre,
            'Apellido'     => $request->Apellido,
            'Matricula'    => $request->Matricula,
            'Correo_inst'  => $request->email,
            'Password'     => $hashedPassword,   // misma contraseña hasheada
            'Rol'          => 'Alumno',
            'Cuatrimestre' => 1,
            'user_id'      => $user->id,
        ]);

        return $this->handleLoginSuccess($request, $user);
    }

    public function logout(Request $request)
    {
        \Illuminate\Support\Facades\Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }

    /**
     * Lógica de redirección por rol
     */
    public function redirectBasedOnRole($user)
    {
        switch ($user->role) {
            case 'admin':
                if (!$user->servicioEscolar) {
                    \Illuminate\Support\Facades\Auth::logout();
                    return redirect()->route('login');
                }
                return redirect()->route('expedienteGeneral');
            case 'tutor':
                $tutorId = $user->tutor?->idTutores;
                if (!$tutorId) {
                    \Illuminate\Support\Facades\Auth::logout();
                    return redirect()->route('login');
                }
                return redirect()->route('tutor.dashboard', ['id' => $tutorId]);
            case 'alumno':
                $alumnoId = $user->alumno?->idAlumnos;
                if (!$alumnoId) {
                    \Illuminate\Support\Facades\Auth::logout();
                    return redirect()->route('login');
                }
                return redirect()->route('alumno.dashboard', ['id' => $alumnoId]);
            default:
                \Illuminate\Support\Facades\Auth::logout();
                return redirect()->route('login');
        }
    }
}
