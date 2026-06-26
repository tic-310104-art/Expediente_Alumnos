<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('Logs de Actividad') }}</title>
    <link rel="stylesheet" href="{{ asset('expedienteG.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="shortcut icon" href="{{ asset('logo-utn.ico') }}" type="image/x-icon">
    <style>
        .logs-shell { display: flex; flex-direction: column; gap: 16px; }

        .logs-header { display:flex; align-items:flex-start; justify-content:space-between; gap: 14px; flex-wrap: wrap; }
        .logs-title { display:flex; gap: 12px; align-items:center; }
        .logs-icon { width: 44px; height: 44px; border-radius: 14px; display:flex; align-items:center; justify-content:center; background: rgba(16,80,75,0.12); color: var(--primary-color); border: 1px solid rgba(16,80,75,0.18); }
        .logs-title h1 { margin: 0; font-size: 22px; letter-spacing: -0.02em; color: var(--text-color); }
        .logs-title p { margin: 2px 0 0 0; color: var(--text-muted); font-size: 13px; }

        .logs-metrics { display:flex; gap: 10px; flex-wrap: wrap; align-items: center; justify-content: flex-end; }
        .metric { background: var(--card-bg); border: 1px solid var(--border-color); border-radius: 14px; padding: 10px 12px; min-width: 160px; box-shadow: 0 8px 22px rgba(0,0,0,0.04); }
        .metric-label { font-size: 11px; color: var(--text-muted); text-transform: uppercase; letter-spacing: .08em; }
        .metric-value { margin-top: 4px; font-size: 22px; font-weight: 900; color: var(--text-color); display:flex; align-items:baseline; gap: 10px; }
        .metric-chip { display:inline-flex; align-items:center; gap:6px; padding: 2px 10px; border-radius: 999px; font-size: 12px; font-weight: 900; background: #fde68a; color:#92400e; border: 1px solid rgba(146,64,14,0.15); }

        .toolbar { background: var(--card-bg); border: 1px solid var(--border-color); border-radius: 16px; padding: 14px; box-shadow: 0 12px 28px rgba(0,0,0,0.04); }
        .filters-bar { display: grid; grid-template-columns: 1.3fr 0.9fr 0.9fr 0.7fr 0.7fr auto auto; gap: 10px; align-items: center; }
        .filter-input { padding: 10px 12px; border: 1px solid var(--border-color); border-radius: 12px; outline: none; background: var(--card-bg); color: var(--text-color); width: 100%; }
        .filter-input[type="date"] { padding: 9px 10px; }
        .btn-inline { height: 40px; border-radius: 12px; }

        .logs-card { border: 1px solid var(--border-color); border-radius: 16px; background: var(--card-bg); overflow: hidden; box-shadow: 0 16px 34px rgba(0,0,0,0.05); }
        .logs-list { max-height: 68vh; overflow: auto; }

        .log-row { display:grid; grid-template-columns: 10px 1fr; border-bottom: 1px solid var(--border-color); }
        .log-accent { background: rgba(148,163,184,0.35); }
        .accent-alta { background: rgba(34,197,94,0.35); }
        .accent-edit { background: rgba(59,130,246,0.35); }
        .accent-baja { background: rgba(239,68,68,0.35); }
        .accent-new { box-shadow: inset 0 0 0 9999px rgba(245,158,11,0.18); }

        .log-body { padding: 14px 16px; display:flex; flex-direction:column; gap: 8px; }
        .log-top { display:flex; justify-content:space-between; gap: 12px; align-items:flex-start; }
        .log-subject { font-weight: 900; color: var(--text-color); line-height: 1.2; display:flex; align-items:center; gap: 10px; flex-wrap: wrap; }
        .log-time { font-size: 12px; color: var(--text-muted); white-space: nowrap; display:flex; align-items:center; gap: 8px; }

        .log-meta { display:flex; gap: 10px; flex-wrap: wrap; align-items:center; color: var(--text-color); font-size: 13px; }
        .meta-pill { display:inline-flex; align-items:center; gap: 8px; padding: 6px 10px; border-radius: 999px; border: 1px solid var(--border-color); background: rgba(15, 23, 42, 0.03); }
        .meta-pill.mono { font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace; font-size: 12px; }

        .badge { padding: 4px 10px; border-radius: 999px; font-size: 11px; font-weight: 900; text-transform: uppercase; letter-spacing: 0.06em; border: 1px solid rgba(0,0,0,0.06); }
        .badge-new { background: #fde68a; color: #92400e; border-color: rgba(146,64,14,0.15); }
        .badge-admin { background: #fee2e2; color: #991b1b; }
        .badge-tutor { background: #dcfce7; color: #166534; }
        .badge-alumno { background: #dbeafe; color: #1e40af; }
        .badge-alta { background: #dcfce7; color: #166534; }
        .badge-edit { background: #dbeafe; color: #1e40af; }
        .badge-baja { background: #fee2e2; color: #991b1b; }

        .pagination-bar { display:flex; justify-content:space-between; gap: 12px; align-items:center; padding: 12px 14px; border-top: 1px solid var(--border-color); }
        .pagination-actions { display:flex; gap: 10px; align-items:center; }
        .pagination-btn { display:inline-flex; align-items:center; gap: 8px; padding: 9px 12px; border-radius: 12px; border: 1px solid var(--border-color); background: var(--card-bg); color: var(--text-color); text-decoration:none; font-weight: 900; font-size: 13px; }
        .pagination-btn[aria-disabled="true"] { opacity: 0.45; pointer-events:none; }
        .pagination-meta { font-size: 13px; color: var(--text-muted); }

        @media (max-width: 1100px) { .filters-bar { grid-template-columns: 1fr 1fr; } }
    </style>
</head>
<body>

    <div class="dashboard-container">
        @include('partials.sidebar', ['active' => 'logs'])

        <main class="main-content">
            <div class="logs-shell">
                <div class="logs-header">
                    <div class="logs-title">
                        <div class="logs-icon"><i class="fa-solid fa-list-check"></i></div>
                        <div>
                            <h1>{{ __('Logs de Actividad') }}</h1>
                            <p>{{ __('Historial de movimientos en el sistema') }}</p>
                        </div>
                    </div>
                    <div class="logs-metrics">
                        <div class="metric">
                            <div class="metric-label">{{ __('Total') }}</div>
                            <div class="metric-value">{{ $logs->total() }}</div>
                        </div>
                        <div class="metric">
                            <div class="metric-label">{{ __('Nuevo') }}</div>
                            <div class="metric-value">
                                {{ (int) ($newCount ?? 0) }}
                                @if((int) ($newCount ?? 0) > 0)
                                    <span class="metric-chip"><i class="fa-solid fa-bell"></i> {{ __('Nuevo') }}</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <div class="toolbar">
                    <form action="{{ route('logs.index') }}" method="GET" class="filters-bar">
                        <input type="text" name="search" placeholder="{{ __('Buscar por acción o IP...') }}" value="{{ request('search') }}" class="filter-input">
                        <select name="role" class="filter-input">
                            <option value="">{{ __('Todos los Roles') }}</option>
                            <option value="admin" {{ request('role') == 'admin' ? 'selected' : '' }}>{{ __('Administradores') }}</option>
                            <option value="tutor" {{ request('role') == 'tutor' ? 'selected' : '' }}>{{ __('Tutores') }}</option>
                            <option value="alumno" {{ request('role') == 'alumno' ? 'selected' : '' }}>{{ __('Alumnos') }}</option>
                        </select>
                        <select name="crud" class="filter-input">
                            <option value="">{{ __('Tipo (CRUD)') }}</option>
                            <option value="alta" {{ request('crud') == 'alta' ? 'selected' : '' }}>{{ __('Alta') }}</option>
                            <option value="edit" {{ request('crud') == 'edit' ? 'selected' : '' }}>{{ __('Edición') }}</option>
                            <option value="baja" {{ request('crud') == 'baja' ? 'selected' : '' }}>{{ __('Baja') }}</option>
                        </select>
                        <input type="date" name="from" value="{{ request('from') }}" class="filter-input">
                        <input type="date" name="to" value="{{ request('to') }}" class="filter-input">
                        <button type="submit" class="btn-primary btn-inline">{{ __('Filtrar') }}</button>
                        <a href="{{ route('logs.print', request()->query()) }}" target="_blank" class="btn-primary btn-inline" style="text-decoration:none; display:flex; align-items:center; justify-content:center; background: #b45309; border-color: #b45309; color: white;">
                             <i class="fa-solid fa-print" style="margin-right: 8px;"></i> {{ __('Imprimir') }}
                        </a>
                        <a href="{{ route('logs.index') }}" class="btn-secondary btn-inline" style="text-decoration:none; display:flex; align-items:center; justify-content:center;">{{ __('Limpiar') }}</a>
                    </form>
                </div>

                <div class="logs-card">
                    <div class="logs-list">
                        @forelse($logs as $log)
                            @php
                                $method = strtoupper((string) $log->method);
                                $crud = $method === 'POST' ? 'alta' : (($method === 'PUT' || $method === 'PATCH') ? 'edit' : ($method === 'DELETE' ? 'baja' : ''));
                                $subjectLower = strtolower((string) $log->subject);
                                if (str_contains($subjectLower, 'baja') || str_contains($subjectLower, 'elimin')) $crud = 'baja';
                                if (str_contains($subjectLower, 'edición') || str_contains($subjectLower, 'edicion')) $crud = 'edit';
                                if (str_contains($subjectLower, 'alta') || str_contains($subjectLower, 'creación') || str_contains($subjectLower, 'creacion')) $crud = $crud ?: 'alta';
                                $crudLabel = $crud === 'alta' ? 'Alta' : ($crud === 'edit' ? 'Edición' : ($crud === 'baja' ? 'Baja' : ''));

                                $parts = @parse_url($log->url);
                                $path = is_array($parts) ? ($parts['path'] ?? $log->url) : $log->url;
                                $segments = array_values(array_filter(explode('/', (string)$path), fn($s) => $s !== ''));
                                $masked = [];
                                foreach ($segments as $seg) {
                                    if (ctype_digit($seg)) { $masked[] = ':id'; continue; }
                                    if (preg_match('/^[0-9a-f]{8}-[0-9a-f]{4}-[1-5][0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/i', $seg)) { $masked[] = ':uuid'; continue; }
                                    if (preg_match('/^[0-9a-f]{24,}$/i', $seg)) { $masked[] = ':hash'; continue; }
                                    $masked[] = $seg;
                                }
                                $prettyPath = '/' . implode('/', $masked);
                                $queryStr = is_array($parts) ? ($parts['query'] ?? '') : '';
                                $params = [];
                                if ($queryStr) { @parse_str($queryStr, $params); }
                                $keys = is_array($params) ? array_keys($params) : [];
                                $queryPreview = '';
                                if (!empty($keys)) {
                                    $sample = array_slice($keys, 0, 3);
                                    $queryPreview = '?' . implode('&', $sample) . (count($keys) > 3 ? '&…' : '');
                                }
                                $cleanUrl = $prettyPath . $queryPreview;
                                $isNew = isset($lastSeenId) && (int) $log->id > (int) $lastSeenId;
                            @endphp

                            <div class="log-row">
                                <div class="log-accent accent-{{ $crud }} {{ $isNew ? 'accent-new' : '' }}"></div>
                                <div class="log-body">
                                    <div class="log-top">
                                        <div class="log-subject">
                                            {{ $log->subject }}
                                            @if($isNew)
                                                <span class="badge badge-new">{{ __('Nuevo') }}</span>
                                            @endif
                                            @if($crud)
                                                <span class="badge badge-{{ $crud }}">{{ __($crudLabel) }}</span>
                                            @endif
                                        </div>
                                        <div class="log-time">
                                            <i class="fa-solid fa-clock"></i> {{ $log->created_at->diffForHumans() }}
                                            <span style="opacity:.8;">({{ $log->created_at->format('d/m/Y H:i') }})</span>
                                        </div>
                                    </div>

                                    <div class="log-meta">
                                        <span class="meta-pill">
                                            <i class="fa-solid fa-user"></i>
                                            <strong>{{ $log->user ? $log->user->name : 'Sistema' }}</strong>
                                            @if($log->user)
                                                <span class="badge badge-{{ $log->user->role }}">{{ strtoupper((string) $log->user->role) }}</span>
                                            @endif
                                        </span>

                                        <span class="meta-pill mono" title="{{ $log->ip }}">
                                            <i class="fa-solid fa-network-wired"></i> {{ $log->ip }}
                                        </span>

                                        <span class="meta-pill mono" title="{{ $log->url }}">
                                            <i class="fa-solid fa-link"></i> {{ $method }} {{ \Illuminate\Support\Str::limit($cleanUrl, 110) }}
                                        </span>
                                    </div>

                                    @if($log->details)
                                        <div class="log-details" style="background: rgba(15, 23, 42, 0.02); border: 1px dashed var(--border-color); border-radius: 10px; padding: 10px 14px; font-size: 12px; color: var(--text-color); margin-top: 4px;">
                                            <i class="fa-solid fa-circle-info" style="color: var(--primary-color); margin-right: 6px;"></i>
                                            <strong>{{ __('Detalles:') }}</strong> {{ preg_replace('/(password|Password|Contraseña|Password_confirmation|swal-token): [^|]+/i', '$1: ********', $log->details) }}
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <div style="padding: 54px 18px; text-align:center; color: var(--text-muted);">
                                <div style="width:56px;height:56px;border-radius:16px;margin:0 auto 12px auto;display:flex;align-items:center;justify-content:center;background:rgba(15,23,42,0.06);border:1px solid var(--border-color);">
                                    <i class="fa-solid fa-folder-open" style="font-size: 22px;"></i>
                                </div>
                                <div style="font-weight:900; color: var(--text-color); margin-bottom: 6px;">{{ __('Logs de Actividad') }}</div>
                                <div>{{ __('No se encontraron registros de actividad.') }}</div>
                            </div>
                        @endforelse
                    </div>

                    <div class="pagination-bar">
                        <div class="pagination-actions">
                            <a class="pagination-btn" href="{{ $logs->appends(request()->query())->previousPageUrl() ?? '#' }}" aria-disabled="{{ $logs->onFirstPage() ? 'true' : 'false' }}">
                                <i class="fa-solid fa-chevron-left"></i> {{ __('Anterior') }}
                            </a>
                            <a class="pagination-btn" href="{{ $logs->appends(request()->query())->nextPageUrl() ?? '#' }}" aria-disabled="{{ $logs->hasMorePages() ? 'false' : 'true' }}">
                                {{ __('Siguiente') }} <i class="fa-solid fa-chevron-right"></i>
                            </a>
                        </div>
                        <div class="pagination-meta">
                            {{ __('Página') }} {{ $logs->currentPage() }} / {{ $logs->lastPage() }}
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

</body>
</html>
