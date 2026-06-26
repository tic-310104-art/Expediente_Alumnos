<?php

namespace App\Http\Controllers;

use App\Models\LogActivity;
use Illuminate\Http\Request;

class LogController extends Controller
{
    /**
     * Muestra la interfaz de logs con filtros y scroll.
     */
    public function index(Request $request)
    {
        $lastSeenId = (int) session('logs_last_seen_id', 0);
        $newCount = $lastSeenId > 0 ? LogActivity::where('id', '>', $lastSeenId)->count() : LogActivity::count();
        $query = $this->buildQuery($request);

        $logs = $query->paginate(50); // Usamos paginación para manejar el scroll infinito o carga por partes

        $maxId = (int) (LogActivity::max('id') ?? 0);
        if ($maxId > $lastSeenId) {
            session(['logs_last_seen_id' => $maxId]);
        }

        return view('admins.logs', [
            'logs' => $logs,
            'lastSeenId' => $lastSeenId,
            'newCount' => $newCount,
        ]);
    }

    /**
     * Muestra la vista de impresión de los logs filtrados.
     */
    public function print(Request $request)
    {
        $logs = $this->buildQuery($request)->get();
        return view('admins.pdf_logs_print', compact('logs'));
    }

    /**
     * Construye la consulta de logs basada en la request.
     */
    protected function buildQuery(Request $request)
    {
        $query = LogActivity::with('user')->orderBy('created_at', 'desc');

        // Filtrar por rol si se solicita
        if ($request->has('role') && !empty($request->role)) {
            $query->whereHas('user', function($q) use ($request) {
                $q->where('role', $request->role);
            });
        }

        if ($request->filled('crud')) {
            $crud = $request->input('crud');
            $methods = match ($crud) {
                'alta' => ['POST'],
                'edit' => ['PUT', 'PATCH'],
                'baja' => ['DELETE'],
                default => [],
            };

            if (!empty($methods)) {
                $query->where(function ($q) use ($methods, $crud) {
                    $q->whereIn('method', $methods);

                    if ($crud === 'alta') {
                        $q->orWhere('subject', 'LIKE', '%Alta%')
                          ->orWhere('subject', 'LIKE', '%Creación%');
                    } elseif ($crud === 'edit') {
                        $q->orWhere('subject', 'LIKE', '%Edición%');
                    } elseif ($crud === 'baja') {
                        $q->orWhere('subject', 'LIKE', '%Baja%')
                          ->orWhere('subject', 'LIKE', '%Elimin%');
                    }
                });
            }
        }

        $from = $request->input('from');
        $to = $request->input('to');
        if ($from && preg_match('/^\d{4}-\d{2}-\d{2}$/', $from)) {
            $fromDate = $from . ' 00:00:00';
            $toDate = $from . ' 23:59:59';
            if ($to && preg_match('/^\d{4}-\d{2}-\d{2}$/', $to)) {
                $toDate = $to . ' 23:59:59';
            }
            $query->whereBetween('created_at', [$fromDate, $toDate]);
        } elseif ($to && preg_match('/^\d{4}-\d{2}-\d{2}$/', $to)) {
            $query->whereBetween('created_at', [$to . ' 00:00:00', $to . ' 23:59:59']);
        }

        // Búsqueda por sujeto o IP
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('subject', 'LIKE', "%$search%")
                  ->orWhere('ip', 'LIKE', "%$search%");
            });
        }

        return $query;
    }

    /**
     * Método estático para registrar actividades fácilmente.
     */
    public static function addToLog($subject)
    {
        $log = [];
        $log['subject'] = $subject;
        $log['url'] = request()->fullUrl();
        $log['method'] = request()->method();
        $log['ip'] = request()->ip();
        $log['agent'] = request()->header('user-agent');
        $log['user_id'] = auth()->check() ? auth()->user()->id : null;
        LogActivity::create($log);
    }
}
