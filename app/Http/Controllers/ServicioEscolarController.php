<?php

namespace App\Http\Controllers;

use App\Models\ServicioEscolar;
use App\Models\Alumno;
use App\Models\Tutor;
use App\Models\Carrera;
use App\Models\BackupSchedule;
use App\Services\BackupService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class ServicioEscolarController extends Controller
{
    private BackupService $backupService;

    public function __construct(BackupService $backupService)
    {
        $this->backupService = $backupService;
    }
  
    public function index()
    {
        $admins = ServicioEscolar::all();
        return view('admins.gestion_admins', compact('admins'));
    }

    public function dashboard()
    {
        $user = auth()->user();
        $admin_profile = $user->servicioEscolar;

        $totalAlumnos = Alumno::where(function($q) {
            $q->whereIn('Estatus', ['activo', 'riesgo'])->orWhereNull('Estatus');
        })->count();
        $totalBajas = Alumno::where('Estatus', 'baja')->count();
        $totalTutores = Tutor::count();
        $totalAdmins = ServicioEscolar::count();

        $carreras = Carrera::withCount([
            'alumnos as total_alumnos' => function($q) {
                $q->where(function($sq) {
                    $sq->whereIn('Estatus', ['activo', 'riesgo'])->orWhereNull('Estatus');
                });
            },
            'tutores as total_tutores'
        ])->get();

        if (!\Illuminate\Support\Facades\Schema::hasTable('backup_schedules')) {
            try {
                \Illuminate\Support\Facades\Schema::create('backup_schedules', function ($table) {
                    $table->id();
                    $table->date('scheduled_date');
                    $table->time('scheduled_time');
                    $table->string('frequency')->default('once');
                    $table->boolean('is_active')->default(true);
                    $table->timestamp('last_run_at')->nullable();
                    $table->timestamps();
                });
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::warning('No se pudo crear tabla backup_schedules: ' . $e->getMessage());
            }
        }

        $activeBackup = BackupSchedule::where('is_active', true)->orderBy('id', 'desc')->first();

        return view('admins.expedienteG', compact('totalAlumnos', 'totalBajas', 'totalTutores', 'totalAdmins', 'carreras', 'admin_profile', 'activeBackup'));
    }

    public function scheduleBackup(Request $request)
    {
        $request->validate([
            'backup_date' => 'required|date',
            'backup_time' => 'required',
            'frequency' => 'required|in:once,4_days,7_days,monthly'
        ]);

        // Desactivar cualquier respaldo previo activo
        BackupSchedule::where('is_active', true)->update(['is_active' => false]);

        // Crear el nuevo agendamiento
        $schedule = BackupSchedule::create([
            'scheduled_date' => $request->backup_date,
            'scheduled_time' => $request->backup_time,
            'frequency' => $request->frequency,
            'is_active' => true
        ]);
        
        return back()->with('success', __('Respaldo agendado correctamente para el ') . $request->backup_date . ' ' . __('a las') . ' ' . $request->backup_time);
    }

    private function updateToNextSchedule($schedule)
    {
        $nextDate = \Carbon\Carbon::parse($schedule->scheduled_date);
        
        switch ($schedule->frequency) {
            case '4_days':
                $nextDate->addDays(4);
                break;
            case '7_days':
                $nextDate->addDays(7);
                break;
            case 'monthly':
                $nextDate->addMonth();
                break;
        }

        // Si por alguna razón la siguiente fecha aún es pasada, seguir sumando
        while ($nextDate->format('Y-m-d') <= now()->format('Y-m-d')) {
            switch ($schedule->frequency) {
                case '4_days': $nextDate->addDays(4); break;
                case '7_days': $nextDate->addDays(7); break;
                case 'monthly': $nextDate->addMonth(); break;
            }
        }

        $schedule->update([
            'scheduled_date' => $nextDate,
            'last_run_at' => now()
        ]);
    }

    public function importBackup(Request $request)
    {
        if (!app()->environment('local')) {
            $message = __('La restauración de base de datos solo está disponible en entorno local.');
            if ($request->expectsJson()) return response()->json(['success' => false, 'message' => $message], 403);
            return back()->with('import_error', $message);
        }

        $request->validate([
            'backup_file' => 'required|file',
        ]);

        $file = $request->file('backup_file');
        
        if (!$file->isValid()) {
            $message = __('El archivo superó el límite de tamaño permitido (' . ini_get('upload_max_filesize') . ') o hubo un error en la carga.');
            if ($request->expectsJson()) return response()->json(['success' => false, 'message' => $message], 422);
            return back()->with('import_error', $message);
        }

        if ($file->getClientOriginalExtension() !== 'sql') {
            $message = __('El archivo debe tener la extensión .sql');
            if ($request->expectsJson()) return response()->json(['success' => false, 'message' => $message], 422);
            return back()->with('import_error', $message);
        }

        // Guardar el archivo temporalmente en el storage primero
        // Usamos move() original en lugar de storeAs de Flysystem para evitar ValueError en getRealPath() de Windows
        $fileName = 'import_' . time() . '.sql';
        $targetDir = storage_path('app/backups_imports');
        
        $file->move($targetDir, $fileName);
        $fullPath = $targetDir . DIRECTORY_SEPARATOR . $fileName;

        if (!file_exists($fullPath)) {
            $message = __('Error grave: no se pudo mover el archivo a la carpeta local del sistema.');
            if ($request->expectsJson()) return response()->json(['success' => false, 'message' => $message], 500);
            return back()->with('import_error', $message);
        }

        // Revisar al menos un poco del contenido para comprobar compatibilidad
        $content = @file_get_contents($fullPath, false, null, 0, 8192);
        
        if ($content === false) {
            @unlink($fullPath);
            $message = __('No se pudo leer el archivo de forma segura.');
            if ($request->expectsJson()) return response()->json(['success' => false, 'message' => $message], 422);
            return back()->with('import_error', $message);
        }

        if (strpos($content, 'alumnos') === false && strpos($content, 'tutores') === false) {
            @unlink($fullPath);
            $message = __('El archivo no parece ser un respaldo compatible con este proyecto. No se encontraron las tablas esperadas.');
            if ($request->expectsJson()) return response()->json(['success' => false, 'message' => $message], 422);
            return back()->with('import_error', $message);
        }

        $conn = config('database.connections.mysql');
        $host = $conn['host'] ?? '127.0.0.1';
        $port = (string)($conn['port'] ?? '3306');
        $database = $conn['database'] ?? '';
        $username = $conn['username'] ?? '';
        $password = $conn['password'] ?? '';

        $tmpFile = tempnam(sys_get_temp_dir(), 'mysql_');
        $ini = "[client]\nuser={$username}\npassword={$password}\nhost={$host}\nport={$port}\n";
        file_put_contents($tmpFile, $ini);

        $mysql = 'mysql';
        $candidates = [
            'C:\laragon\bin\mysql\mysql-8.0.30-winx64\bin\mysql.exe',
            'C:\laragon\bin\mysql\mysql-8.0.29-winx64\bin\mysql.exe',
            'C:\laragon\bin\mysql\mysql-8.0.28-winx64\bin\mysql.exe',
        ];
        foreach ($candidates as $candidate) {
            if (file_exists($candidate)) {
                $mysql = $candidate;
                break;
            }
        }

        $command = escapeshellarg($mysql)
            . ' --defaults-extra-file=' . escapeshellarg($tmpFile)
            . ' ' . escapeshellarg($database)
            . ' < ' . escapeshellarg($fullPath) . ' 2>&1';

        exec($command, $output, $returnVar);
        @unlink($tmpFile);
        @unlink($fullPath);

        if ($returnVar !== 0) {
            \Illuminate\Support\Facades\Log::error('Error Restaurando BD: ' . implode("\n", $output));
            $message = __('Hubo un error al restaurar la base de datos. Verifica el archivo.');
            if ($request->expectsJson()) return response()->json(['success' => false, 'message' => $message], 500);
            return back()->with('import_error', $message);
        }

        $message = __('Base de datos restaurada correctamente.');
        if ($request->expectsJson()) return response()->json(['success' => true, 'message' => $message]);
        return back()->with('import_success', $message);
    }

    /**
     * Guarda un nuevo administrador
     */
    public function store(Request $request)
    {
        $table = 'servicios_escolares';
        $emailColumn = 'Correo'; // Default
        
        if (Schema::hasColumn($table, 'Correo')) {
            $emailColumn = 'Correo';
        } elseif (Schema::hasColumn($table, 'Correo_inst')) {
            $emailColumn = 'Correo_inst';
        } elseif (Schema::hasColumn($table, 'Email')) {
            $emailColumn = 'Email';
        }

        $request->validate([
            'Correo' => "required|email|unique:servicios_escolares,$emailColumn",
            'Password' => 'required|min:6',
            'Clave_Trabajador' => 'required|unique:servicios_escolares,Clave_Trabajador'
        ]);

        $data = $request->all();
        
        if ($emailColumn !== 'Correo') {
            $data[$emailColumn] = $request->Correo;
            unset($data['Correo']);
        }

        if ($request->filled('Password')) {
            $data['Password'] = Hash::make($request->Password);
        }

        $admin = ServicioEscolar::create($data);

        $userData = [
            'name' => 'Admin ' . ($admin->Nombre ?? $admin->Clave_Trabajador),
            'password' => $admin->Password,
            'role' => 'admin'
        ];

        $user = \App\Models\User::updateOrCreate(
            ['email' => $request->Correo],
            $userData
        );

        $admin->update(['user_id' => $user->id]);

        return redirect()->route('servicios.index')
            ->with('success', __('Administrador creado correctamente y vinculado a usuario.'));
    }

    /*
      Muestra el formulario de edición
     */
    public function edit($id)
    {
        $admin = ServicioEscolar::findOrFail($id);
        return view('admins.edit_admins', compact('admin'));
    }

    /* Actualiza los datos en la BD
     */
    public function update(Request $request, $id)
    {
        $admin = ServicioEscolar::findOrFail($id);
        $table = 'servicios_escolares';
        $emailColumn = 'Correo';

        if (Schema::hasColumn($table, 'Correo')) {
            $emailColumn = 'Correo';
        } elseif (Schema::hasColumn($table, 'Correo_inst')) {
            $emailColumn = 'Correo_inst';
        } elseif (Schema::hasColumn($table, 'Email')) {
            $emailColumn = 'Email';
        }

        $request->validate([
            'Correo' => "required|email|unique:servicios_escolares,$emailColumn,".$id.",idServicios_Escolares",
            'Clave_Trabajador' => 'required|unique:servicios_escolares,Clave_Trabajador,'.$id.',idServicios_Escolares'
        ]);

        $data = $request->all();

        // Mapear el campo 'Correo' del formulario a la columna real de la BD
        if ($emailColumn !== 'Correo') {
            $data[$emailColumn] = $request->Correo;
            unset($data['Correo']);
        }

        // Solo actualizamos la contraseña si el usuario escribió una nueva
        if ($request->filled('Password')) {
            $data['Password'] = Hash::make($request->Password);
        } else {
            // Si está vacío, quitamos el campo del array para no borrar la contraseña actual
            unset($data['Password']); 
        }

        $admin->update($data);

        // Asegurar que el usuario asociado esté actualizado (email, nombre y password si cambió)
        if ($admin->user_id) {
            $user = \App\Models\User::find($admin->user_id);
        } else {
            // Intentar buscarlo por correo si user_id era nulo por migración previa
            $user = \App\Models\User::where('email', $admin->getOriginal($emailColumn))->first();
        }

        if ($user) {
            $updateData = [
                'email' => $request->Correo,
                'name' => 'Admin ' . ($admin->Nombre ?? $admin->Clave_Trabajador)
            ];
            
            if ($request->filled('Password')) {
                $updateData['password'] = Hash::make((string) $request->Password);
            }
            
            $user->update($updateData);
            
            // Si no estaba vinculado, vincularlo ahora
            if (!$admin->user_id) {
                $admin->update(['user_id' => $user->id]);
            }
        } else {
            // Si no existía el usuario, crearlo para que no quede huérfano
            $newUser = \App\Models\User::create([
                'name' => 'Admin ' . ($admin->Nombre ?? $admin->Clave_Trabajador),
                'email' => $request->Correo,
                'password' => $request->filled('Password') ? Hash::make($request->Password) : $admin->Password,
                'role' => 'admin'
            ]);
            $admin->update(['user_id' => $newUser->id]);
        }

        return redirect()->route('servicios.index')
            ->with('success', __('Administrador actualizado correctamente.'));
    }

    /**
     * Elimina el registro
     */
    public function destroy($id)
{
    // 1. Buscamos al administrador por su ID primaria real
    $admin = ServicioEscolar::findOrFail($id);

    // 2. Limpiamos la relación en la tabla 'alumnos'
    // Usamos el nombre exacto que nos dio el error: 'Servicios_Escolares_id'
    \DB::table('alumnos')
        ->where('Servicios_Escolares_id', $id)
        ->update(['Servicios_Escolares_id' => null]);

    \DB::table('tutores')
        ->where('Servicios_Escolares_id', $id)
        ->update(['Servicios_Escolares_id' => null]);

    \DB::table('carreras')
        ->where('Servicios_Escolares_id', $id)
        ->update(['Servicios_Escolares_id' => null]);

    // 3. Ahora que los alumnos ya no están "amarrados" a él, lo borramos
    $admin->delete();

    return redirect()->route('servicios.index')
        ->with('success', __('Administrador eliminado y registros desvinculados.'));
}
}
