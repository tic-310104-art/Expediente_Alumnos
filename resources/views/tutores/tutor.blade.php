<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('Panel de Tutoría') }}</title>
    <link rel="stylesheet" href="{{ asset('expedienteG.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="shortcut icon" href="{{ asset('logo-utn.ico') }}" type="image/x-icon">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- FullCalendar CDN -->
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js'></script>
    <style>
        #calendar {
            max-width: 100%;
            margin: 0 auto;
            font-family: inherit;
        }
        .fc {
            background: #ffffff !important;
            color: #1a202c !important;
            border-radius: 12px;
            overflow: hidden;
            border: 1px solid var(--border-color);
            box-shadow: var(--shadow-md);
        }
        .fc-header-toolbar {
            padding: 1rem !important;
            margin-bottom: 0 !important;
            background: var(--bg-color);
            border-bottom: 1px solid var(--border-color);
        }
        .fc-toolbar-title {
            font-size: 1rem !important;
            color: var(--text-main) !important;
            font-weight: 700 !important;
            text-transform: uppercase;
            letter-spacing: 0.025em;
        }
        .fc-button-primary {
            background-color: var(--card-bg) !important;
            border: 1px solid var(--border-color) !important;
            color: var(--text-main) !important;
            font-size: 0.8rem !important;
            font-weight: 600 !important;
            text-transform: capitalize !important;
            padding: 0.4rem 0.8rem !important;
            transition: all 0.2s;
        }
        .fc-button-primary:hover {
            background-color: var(--bg-color) !important;
        }
        .fc-button-active {
            background-color: var(--primary-color) !important;
            border-color: var(--primary-color) !important;
            color: #fff !important;
        }
        .fc-daygrid-day-number {
            font-size: 0.85rem;
            color: var(--text-muted);
            padding: 4px 8px !important;
            text-decoration: none !important;
        }
        .fc-day-today {
            background-color: rgba(16, 80, 75, 0.05) !important;
        }
        .fc-event {
            border: none !important;
            padding: 2px 4px !important;
            font-size: 0.75rem !important;
            border-radius: 4px !important;
            font-weight: 600 !important;
            color: #fff !important;
        }
        .fc-col-header-cell-cushion {
            font-size: 0.8rem;
            font-weight: 700;
            color: var(--text-muted);
            text-transform: uppercase;
        }
        /* Altura del calendario */
        .fc-view-harness { height: 450px !important; }

        /* ARREGLO PARA VISIBILIDAD EN AMBOS TEMAS */
        .fc-toolbar-title, 
        .fc-col-header-cell-cushion,
        .fc-daygrid-day-number {
            color: #1a202c !important; /* Negro profesional para modo claro */
            font-weight: 700 !important;
        }

        body.dark-mode .fc {
            background-color: #ffffff !important;
            border-color: #334155 !important;
        }
        body.dark-mode .fc-toolbar-title, 
        body.dark-mode .fc-col-header-cell-cushion,
        body.dark-mode .fc-daygrid-day-number,
        body.dark-mode .fc-list-day-text,
        body.dark-mode .fc-list-day-side-text {
            color: #1a202c !important; /* Forzado a oscuro sobre el fondo blanco */
        }
        
        /* Eventos: Texto siempre blanco para contrastar con fondos azul/rojo */
        .fc-event-title, .fc-event-main, .fc-event-title-container {
            color: #ffffff !important;
            font-weight: 700 !important;
            text-shadow: 0 1px 2px rgba(0,0,0,0.2); /* Sombra para legibilidad extra */
        }
        
        .fc-button-primary {
            background-color: var(--card-bg) !important;
            border-color: var(--border-color) !important;
            color: var(--text-main) !important;
        }

        /* Asegurar que los eventos de fondo (colores de celda) sean visibles */
        .fc-bg-event {
            opacity: 1 !important;
        }
    </style>
</head>
<body>

    <div class="dashboard-container">
        @include('partials.sidebar', ['active' => 'inicio'])

        <main class="main-content">
            <header class="student-header">
                <div class="student-profile">
                    <div class="profile-img-container" ondblclick="document.getElementById('profile-upload').click()">
                        @php
                            $fotoUrl = $tutor->foto_url ?? "https://ui-avatars.com/api/?name=" . urlencode($tutor->Nombre . '+' . $tutor->Apellido) . "&background=10504B&color=fff&size=100";
                            $hasFoto = !is_null($tutor->foto_url);
                        @endphp
                        <img src="{{ $fotoUrl }}" alt="{{ __('Foto del tutor') }}" class="profile-img" id="profile-display">
                        <input type="file" id="profile-upload" style="display: none;" accept="image/*">
                        @if($hasFoto)
                        <button type="button" id="delete-photo-btn" class="profile-delete-btn" title="{{ __('Eliminar foto') }}">
                            <i class="fa-solid fa-xmark"></i>
                        </button>
                        @endif
                    </div>
                    <div class="student-info">
                        <h1>{{ $tutor->Nombre }} {{ $tutor->Apellido }}</h1>
                        <p class="student-id"><i class="fa-solid fa-chalkboard-user"></i> {{ __('Rol') }}: <strong>{{ $tutor->Rol }}</strong></p>
                        <p class="student-career"><i class="fa-solid fa-building"></i> {{ __('Departamento Académico') }}</p>
                    </div>
                </div>
            </header>

            <div class="dashboard-grid">
                <style>
                    .at-risk-modal-item {
                        display: flex;
                        align-items: center;
                        justify-content: space-between;
                        padding: 12px;
                        margin-bottom: 8px;
                        background: #f8fafc;
                        border-radius: 10px;
                        border: 1px solid #e2e8f0;
                        transition: all 0.2s;
                        text-decoration: none;
                        color: inherit;
                    }
                    .at-risk-modal-item:hover {
                        background: #f1f5f9;
                        border-color: #cbd5e1;
                        transform: scale(1.01);
                    }
                    .dark-mode .at-risk-modal-item {
                        background: #1e293b;
                        border-color: #334155;
                    }
                    .dark-mode .at-risk-modal-item:hover {
                        background: #334155;
                    }
                </style>

                @if($tutor->grupos->count() > 0)
                @foreach($tutor->grupos as $index => $grupo)
                <div class="card full-width" style="margin-bottom: 20px;">
                    <div class="card-collapsible-header" onclick="toggleCardCollapse(this)">
                        <h3><i class="fa-solid fa-layer-group"></i> {{ $grupo->carrera->Nombre ?? __('Sin carrera') }} - {{ $grupo->Grupo }} ({{ $grupo->alumnos->count() }} {{ __('alumnos') }})</h3>
                        <button class="card-collapsible-toggle" type="button" title="{{ __('Minimizar') }}">
                            <i class="fa-solid fa-chevron-up"></i>
                        </button>
                    </div>
                    <div class="card-collapsible-body">
                        @php
                            $capacidad = $grupo->Cantidad_Alumnos ?? $grupo->alumnos->count();
                            $ocupados = $grupo->alumnos->count();
                            $disponibles = max($capacidad - $ocupados, 0);
                            $denominador = max($capacidad, 1);

                            $rangosDef = [
                                ['label' => '< 8', 'min' => 0, 'max' => 8, 'color' => '#dc2626'],
                                ['label' => '8-8.5', 'min' => 8, 'max' => 8.5, 'color' => '#f59e0b'],
                                ['label' => '8.5-9', 'min' => 8.5, 'max' => 9, 'color' => '#16a34a'],
                                ['label' => '9-9.5', 'min' => 9, 'max' => 9.5, 'color' => '#059669'],
                                ['label' => '9.5-10', 'min' => 9.5, 'max' => 10, 'color' => '#0d9488'],
                            ];
                            $distribucion = collect($rangosDef)->map(function($r) use ($grupo) {
                                $alumnosEnRango = $grupo->alumnos->filter(function($a) use ($r) {
                                    $p = $a->promedio;
                                    return $p > 0 && $p >= $r['min'] && $p < $r['max'];
                                });
                                $r['alumnos'] = $alumnosEnRango->map(function($a) {
                                    return [
                                        'idAlumnos' => $a->idAlumnos,
                                        'Nombre' => $a->Nombre,
                                        'Apellido' => $a->Apellido,
                                        'Matricula' => $a->Matricula,
                                        'promedio' => $a->promedio,
                                    ];
                                })->values();
                                $r['count'] = $alumnosEnRango->count();
                                return $r;
                            });
                            $sinDatos = $grupo->alumnos->filter(function($a) { return $a->promedio == 0; })->count();
                        @endphp

                        <div class="enhanced-chart">
                            <div class="enhanced-chart-header" onclick="toggleChart(this)">
                                <div class="enhanced-chart-capacity">
                                    <span><i class="fa-solid fa-users"></i> <strong>{{ __('Cupo') }}:</strong> {{ $capacidad }}</span>
                                    <span><strong>{{ __('Asignados') }}:</strong> {{ $ocupados }}</span>
                                    @if($disponibles > 0)
                                    <span style="color: var(--text-muted);"><strong>{{ __('Disponibles') }}:</strong> {{ $disponibles }}</span>
                                    @endif
                                </div>
                                <button class="chart-toggle" type="button" title="{{ __('Minimizar gráfica') }}">
                                    <i class="fa-solid fa-chevron-up"></i>
                                </button>
                            </div>
                            <div class="enhanced-chart-body">
                                <div class="vbar-container">
                                    @php
                                        $tubeData = $distribucion->values()->all();
                                    @endphp
                                    @foreach($tubeData as $idx => $tube)
                                    <div class="vbar-item" onclick="showTubeAlumnos(this)" data-alumnos='{{ json_encode($tube['alumnos']) }}' data-label="{{ $tube['label'] }}" data-color="{{ $tube['color'] }}">
                                        <div class="vbar-value">{{ $tube['count'] }}</div>
                                        <div class="vbar-track">
                                            @if($tube['count'] > 0)
                                            <div class="vbar-fill" style="height: {{ min(100, ($tube['count'] / $denominador) * 100) }}%; background: {{ $tube['color'] }};"></div>
                                            @endif
                                        </div>
                                        <div class="vbar-label">{{ $tube['label'] }}</div>
                                        <div class="vbar-hover-popup">
                                            @php $alumnosColl = $tube['alumnos']; $maxHover = 6; @endphp
                                            @forelse($alumnosColl->take($maxHover) as $a)
                                            <div class="vbar-hover-item">
                                                <span class="vbar-hover-name">{{ $a['Nombre'] }} {{ $a['Apellido'] }}</span>
                                                <span class="vbar-hover-prom" style="color: {{ $a['promedio'] < 8 ? '#dc2626' : ($a['promedio'] < 8.5 ? '#f59e0b' : '#059669') }}">{{ number_format($a['promedio'], 1) }}</span>
                                            </div>
                                            @empty
                                            <div class="vbar-hover-empty">{{ __('Sin alumnos') }}</div>
                                            @endforelse
                                            @if($alumnosColl->count() > $maxHover)
                                            <div class="vbar-hover-more">+{{ $alumnosColl->count() - $maxHover }} {{ __('más') }}</div>
                                            @endif
                                        </div>
                                    </div>
                                    @endforeach
                                    @if($disponibles > 0)
                                    <div class="vbar-item">
                                        <div class="vbar-value" style="color: var(--text-muted);">{{ $disponibles }}</div>
                                        <div class="vbar-track">
                                            <div class="vbar-fill" style="height: {{ min(100, ($disponibles / $denominador) * 100) }}%; background: #e2e8f0;"></div>
                                        </div>
                                        <div class="vbar-label">{{ __('Vac.') }}</div>
                                        <div class="vbar-hover-popup">
                                            <div class="vbar-hover-empty">{{ __('Espacios disponibles') }}</div>
                                        </div>
                                    </div>
                                    @endif
                                </div>
                                @if($sinDatos > 0)
                                <div class="enhanced-chart-footer">
                                    <i class="fa-solid fa-circle-exclamation"></i> {{ $sinDatos }} {{ __('sin calificaciones') }}
                                </div>
                                @endif
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="data-table">
                                <thead>
                                    <tr>
                                        <th>{{ __('Matrícula') }}</th>
                                        <th>{{ __('Nombre Completo') }}</th>
                                        <th>{{ __('Correo') }}</th>
                                        <th>{{ __('Cuatrimestre') }}</th>
                                        <th>{{ __('Promedio') }}</th>
                                        <th>{{ __('Estatus') }}</th>
                                        <th>{{ __('Acciones') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($grupo->alumnos as $alumno)
                                    @php
                                        $promedio = $alumno->promedio;
                                        $estatus = strtolower((string) ($alumno->Estatus ?? 'activo'));
                                        $estatusLabel = $estatus === 'baja' ? 'Baja' : ($estatus === 'riesgo' ? 'En riesgo' : 'Activo');
                                        $badgeStyle = $estatus === 'baja'
                                            ? 'background:#fee2e2;color:#991b1b;'
                                            : ($estatus === 'riesgo' ? 'background:#ffedd5;color:#9a3412;' : 'background:#d1fae5;color:#065f46;');
                                        $promColor = $promedio > 0 ? ($promedio < 8 ? '#dc2626' : ($promedio < 8.5 ? '#f59e0b' : ($promedio < 9.5 ? '#15803d' : '#059669'))) : '#4b5563';
                                    @endphp
                                    <tr>
                                        <td data-label="{{ __('Matrícula') }}"><a href="{{ route('alumno.dashboard', $alumno->idAlumnos) }}" style="color:#2b7a78;font-weight:bold;">{{ $alumno->Matricula }}</a></td>
                                        <td data-label="{{ __('Nombre') }}">{{ $alumno->Nombre }} {{ $alumno->Apellido }}</td>
                                        <td data-label="{{ __('Correo') }}">{{ $alumno->Correo_inst }}</td>
                                        <td data-label="{{ __('Cuatrimestre') }}">{{ $alumno->Cuatrimestre }}</td>
                                        <td data-label="{{ __('Promedio') }}">
                                            <span style="display:inline-flex;align-items:center;padding:4px 10px;border-radius:999px;font-weight:700;font-size:12px;color:#fff;background:{{ $promColor }};">
                                                {{ $promedio > 0 ? number_format($promedio, 1) : 'N/A' }}
                                            </span>
                                        </td>
                                        <td data-label="{{ __('Estatus') }}"><span style="display:inline-flex;align-items:center;padding:4px 10px;border-radius:999px;font-weight:700;font-size:12px;border:1px solid rgba(0,0,0,0.06);{{ $badgeStyle }}">{{ __($estatusLabel) }}</span></td>
                                        <td data-label="{{ __('Acciones') }}">
                                            <div class="acciones-group">
                                                <a href="{{ route('tutor.alumnos.calificaciones', ['id' => $tutor->idTutores, 'alumnoId' => $alumno->idAlumnos]) }}" class="btn-accion" style="background:#dc2626;" title="{{ __('Asignar Calificaciones') }}">
                                                    <i class="fa-solid fa-pen-to-square"></i>
                                                </a>
                                                <a href="{{ route('historial.show', $alumno->idAlumnos) }}" class="btn-accion" style="background:#10504B;" title="{{ __('Ver Calificaciones') }}">
                                                    <i class="fa-solid fa-graduation-cap"></i>
                                                </a>
                                                <a href="{{ route('tutor.citas', ['id' => $tutor->idTutores, 'alumno_id' => $alumno->idAlumnos]) }}" class="btn-accion" style="background:#2b7a78;" title="{{ __('Agendar Tutoría') }}">
                                                    <i class="fa-solid fa-calendar-plus"></i>
                                                </a>
                                                <a href="{{ route('tutor.psicologia', ['id' => $tutor->idTutores, 'alumno_id' => $alumno->idAlumnos]) }}" class="btn-accion" style="background:#6366f1;" title="{{ __('Cita Psicología') }}">
                                                    <i class="fa-solid fa-brain"></i>
                                                </a>
                                                <a href="{{ route('tutor.asesorias', $tutor->idTutores) }}" class="btn-accion" style="background:#f59e0b;" title="{{ __('Agendar Asesoría') }}">
                                                    <i class="fa-solid fa-chalkboard-user"></i>
                                                </a>
                                                <a href="{{ route('alumno.pdf.resumen', $alumno->idAlumnos) }}" target="_blank" class="btn-accion" style="background:#0d9488;" title="{{ __('Descargar Resumen PDF') }}">
                                                    <i class="fa-solid fa-file-pdf"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="7" style="text-align: center; padding: 20px;">{{ __('No hay alumnos en este grupo.') }}</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                @endforeach
                @else
                <div class="card full-width">
                    <div style="width: 100%; text-align: center; padding: 40px; color: var(--text-muted);">
                        <i class="fa-solid fa-folder-open" style="font-size: 2.5rem; margin-bottom: 10px; display: block;"></i>
                        {{ __('No tiene grupos asignados.') }}
                    </div>
                </div>
                @endif
            </div>
        </main>
    </div>

    {{-- Modal del Calendario --}}
    <div id="calendar-modal" class="calendar-modal-overlay" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.6); z-index: 9999; justify-content: center; align-items: center; backdrop-filter: blur(4px);">
        <div class="calendar-modal-content" style="background: var(--card-bg); border-radius: 16px; width: 90%; max-width: 900px; max-height: 90vh; overflow-y: auto; padding: 25px; position: relative; box-shadow: 0 20px 60px rgba(0,0,0,0.3);">
            <button id="calendar-modal-close" style="position: absolute; top: 15px; right: 15px; background: none; border: none; font-size: 24px; cursor: pointer; color: var(--text-muted); z-index: 10;">
                <i class="fa-solid fa-xmark"></i>
            </button>
            <h3 style="margin-bottom: 15px; color: var(--text-main);"><i class="fa-solid fa-calendar-days"></i> {{ __('Calendario de Tutorías') }}</h3>
            <div id='calendar' style="padding: 10px; background: #fff; border-radius: 8px;"></div>
        </div>
    </div>

    <style>
        .calendar-modal-overlay {
            animation: fadeIn 0.2s ease;
        }
        .calendar-modal-content {
            animation: slideUp 0.3s ease;
        }
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        @keyframes slideUp {
            from { transform: translateY(30px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }
        body.dark-mode .calendar-modal-content {
            background: #1e293b;
        }

        .card-collapsible-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            cursor: pointer;
            user-select: none;
            padding: 4px 0;
        }
        .card-collapsible-header h3 {
            margin: 0;
        }
        .card-collapsible-toggle {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 28px;
            height: 28px;
            border: 1px solid var(--border-color);
            border-radius: 6px;
            background: var(--card-bg);
            color: var(--text-muted);
            cursor: pointer;
            font-size: 12px;
            transition: all 0.2s;
        }
        .card-collapsible-toggle:hover {
            background: var(--bg-color);
            color: var(--text-main);
            border-color: var(--primary-color);
        }
        .card-collapsible-toggle i {
            transition: transform 0.3s ease;
        }
        .card-collapsible-toggle.collapsed i {
            transform: rotate(180deg);
        }
        .card-collapsible-body {
            overflow: hidden;
            transition: max-height 0.4s ease, opacity 0.3s ease, padding 0.3s ease;
            max-height: 5000px;
            opacity: 1;
            padding-top: 8px;
        }
        .card-collapsible-body.collapsed {
            max-height: 0;
            opacity: 0;
            padding-top: 0;
        }

        .enhanced-chart {
            padding: 16px 0;
            border-bottom: 1px solid var(--border-color);
            margin-bottom: 14px;
        }
        .enhanced-chart-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            cursor: pointer;
            user-select: none;
        }
        .enhanced-chart-capacity {
            display: flex;
            gap: 20px;
            font-size: 0.82rem;
            color: var(--text-main);
        }
        .enhanced-chart-capacity i {
            margin-right: 4px;
            color: var(--primary-color);
        }
        .chart-toggle {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 28px;
            height: 28px;
            border: 1px solid var(--border-color);
            border-radius: 6px;
            background: var(--card-bg);
            color: var(--text-muted);
            cursor: pointer;
            font-size: 12px;
            transition: all 0.2s;
            flex-shrink: 0;
        }
        .chart-toggle:hover {
            background: var(--bg-color);
            color: var(--text-main);
            border-color: var(--primary-color);
        }
        .chart-toggle.collapsed i {
            transform: rotate(180deg);
        }
        .chart-toggle i {
            transition: transform 0.3s ease;
        }
        .enhanced-chart-body {
            overflow: hidden;
            transition: max-height 0.35s ease, opacity 0.25s ease, padding 0.3s ease;
            max-height: 600px;
            opacity: 1;
            padding-top: 14px;
        }
        .enhanced-chart-body.collapsed {
            max-height: 0;
            opacity: 0;
            padding-top: 0;
        }
        /* Estilos de gráfica de barras verticales */
        .vbar-container {
            display: flex;
            align-items: flex-end;
            justify-content: center;
            gap: 20px;
            padding: 20px 10px;
            flex-wrap: wrap;
        }
        .vbar-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 6px;
            min-width: 50px;
            cursor: pointer;
            position: relative;
        }
        .vbar-item:hover .vbar-fill {
            filter: brightness(1.1);
        }
        .vbar-item:active .vbar-fill {
            filter: brightness(0.9);
        }
        .vbar-value {
            font-size: 0.95rem;
            font-weight: 800;
            color: var(--text-main);
            text-align: center;
        }
        .vbar-track {
            width: 40px;
            height: 150px;
            background: rgba(0,0,0,0.05);
            border-radius: 8px;
            overflow: hidden;
            display: flex;
            flex-direction: column-reverse;
            position: relative;
        }
        .dark-mode .vbar-track {
            background: rgba(255,255,255,0.07);
        }
        .vbar-fill {
            width: 100%;
            border-radius: 8px;
            transition: height 0.8s ease;
            min-height: 4px;
        }
        .vbar-label {
            font-size: 0.75rem;
            font-weight: 700;
            color: var(--text-muted);
            text-align: center;
            margin-top: 2px;
        }
        .vbar-hover-popup {
            position: absolute;
            bottom: 100%;
            left: 50%;
            transform: translateX(-50%);
            background: var(--card-bg);
            border: 1px solid var(--border-color);
            border-radius: 10px;
            padding: 8px 10px;
            min-width: 180px;
            max-width: 220px;
            box-shadow: 0 8px 24px rgba(0,0,0,0.12);
            opacity: 0;
            visibility: hidden;
            transition: all 0.2s ease;
            z-index: 20;
            pointer-events: none;
            margin-bottom: 10px;
        }
        .vbar-item:hover .vbar-hover-popup {
            opacity: 1;
            visibility: visible;
        }
        .dark-mode .vbar-hover-popup {
            background: #1e293b;
            border-color: #334155;
        }
        .vbar-hover-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 3px 0;
            font-size: 0.75rem;
            border-bottom: 1px solid rgba(0,0,0,0.04);
        }
        .dark-mode .vbar-hover-item {
            border-bottom-color: rgba(255,255,255,0.06);
        }
        .vbar-hover-item:last-child {
            border-bottom: none;
        }
        .vbar-hover-name {
            font-weight: 600;
            color: var(--text-main);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 120px;
        }
        .vbar-hover-prom {
            font-weight: 800;
            font-size: 0.7rem;
            margin-left: 8px;
            flex-shrink: 0;
        }
        .vbar-hover-empty {
            font-size: 0.75rem;
            color: var(--text-muted);
            text-align: center;
            padding: 4px 0;
        }
        .vbar-hover-more {
            font-size: 0.7rem;
            color: var(--text-muted);
            text-align: center;
            padding-top: 4px;
            font-weight: 700;
        }
        .enhanced-chart-footer {
            margin-top: 10px;
            text-align: center;
            font-size: 0.78rem;
            color: var(--text-muted);
        }
        .enhanced-chart-footer i {
            color: #f59e0b;
        }

        .acciones-group {
            display: flex;
            gap: 4px;
            flex-wrap: nowrap;
        }
        .btn-accion {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 30px;
            height: 30px;
            border-radius: 6px;
            color: #fff;
            text-decoration: none;
            font-size: 12px;
            transition: all 0.2s;
            border: none;
        }
        .btn-accion:hover {
            transform: translateY(-1px);
            box-shadow: 0 2px 8px rgba(0,0,0,0.2);
        }
        .btn-accion i {
            font-size: 13px;
        }
        .profile-img-container {
            position: relative;
        }
        .profile-delete-btn {
            position: absolute;
            top: -6px;
            right: -6px;
            width: 26px;
            height: 26px;
            border-radius: 50%;
            border: 2px solid var(--card-bg);
            background: #dc2626;
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            font-size: 14px;
            transition: all 0.2s;
            z-index: 5;
            padding: 0;
            line-height: 1;
        }
        .profile-delete-btn:hover {
            background: #b91c1c;
            transform: scale(1.1);
        }
        .profile-delete-btn i {
            font-size: 12px;
        }
        @media (max-width: 480px) {
            .acciones-group { flex-wrap: wrap; gap: 3px; }
            .btn-accion { width: 26px; height: 26px; font-size: 10px; }
            .btn-accion i { font-size: 11px; }
            .vbar-container { gap: 10px; padding: 10px 5px; }
            .vbar-hover-popup { min-width: 140px; max-width: 180px; left: auto; right: 0; }
            .enhanced-chart-capacity { gap: 10px; font-size: 0.75rem; flex-wrap: wrap; }
        }
    </style>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var calendarInstance = null;

            function initCalendar() {
                if (calendarInstance) return;
                var calendarEl = document.getElementById('calendar');
                if (!calendarEl) return;
                calendarInstance = new FullCalendar.Calendar(calendarEl, {
                    initialView: 'dayGridMonth',
                    locale: 'es',
                    height: 'auto',
                    contentHeight: 400,
                    headerToolbar: {
                        left: 'today prev,next',
                        center: 'title',
                        right: 'dayGridMonth,timeGridWeek'
                    },
                    buttonText: {
                        today: 'Hoy',
                        month: 'Mes',
                        week: 'Sem'
                    },
                    events: @json($citasCalendar),
                    eventClick: function(info) {
                        Swal.fire({
                            title: info.event.title,
                            html: `
                                <div style="text-align: left;">
                                    <p><strong>Fecha:</strong> ${info.event.start.toLocaleString()}</p>
                                    <p><strong>Motivo:</strong> ${info.event.extendedProps.description || 'Sin motivo'}</p>
                                </div>
                            `,
                            icon: 'info',
                            confirmButtonColor: '#10504B'
                        });
                    }
                });
                calendarInstance.render();
            }

            // Toggle calendario desde el icono en sidebar
            const calendarToggle = document.getElementById('calendar-toggle');
            const calendarModal = document.getElementById('calendar-modal');
            const calendarClose = document.getElementById('calendar-modal-close');

            if (calendarToggle && calendarModal) {
                calendarToggle.addEventListener('click', function() {
                    calendarModal.style.display = 'flex';
                    document.body.style.overflow = 'hidden';
                    initCalendar();
                    setTimeout(function() {
                        if (calendarInstance) calendarInstance.updateSize();
                    }, 300);
                });

                function closeCalendar() {
                    calendarModal.style.display = 'none';
                    document.body.style.overflow = '';
                }

                if (calendarClose) {
                    calendarClose.addEventListener('click', closeCalendar);
                }

                calendarModal.addEventListener('click', function(e) {
                    if (e.target === calendarModal) closeCalendar();
                });

                document.addEventListener('keydown', function(e) {
                    if (e.key === 'Escape' && calendarModal.style.display === 'flex') {
                        closeCalendar();
                    }
                });
            }

            // Toggle colapsar/expandir gráfica
            window.toggleChart = function(header) {
                var chart = header.closest('.enhanced-chart');
                var body = chart.querySelector('.enhanced-chart-body');
                var toggle = chart.querySelector('.chart-toggle');
                body.classList.toggle('collapsed');
                toggle.classList.toggle('collapsed');
                toggle.title = body.classList.contains('collapsed')
                    ? '{{ __("Expandir gráfica") }}'
                    : '{{ __("Minimizar gráfica") }}';
            };

            // Toggle colapsar/expandir todo el card de Grupos Asignados
            window.toggleCardCollapse = function(header) {
                var card = header.closest('.card');
                var body = card.querySelector('.card-collapsible-body');
                var toggle = card.querySelector('.card-collapsible-toggle');
                body.classList.toggle('collapsed');
                toggle.classList.toggle('collapsed');
                toggle.title = body.classList.contains('collapsed')
                    ? '{{ __("Expandir") }}'
                    : '{{ __("Minimizar") }}';
            };
        });
    </script>

    <script>
        // Modal de alumnos por rango de calificación (tubos)
        window.showTubeAlumnos = function(el) {
            var alumnos = JSON.parse(el.getAttribute('data-alumnos') || '[]');
            var label = el.getAttribute('data-label');
            var color = el.getAttribute('data-color');
            var total = alumnos.length;

            if (total === 0) {
                Swal.fire({
                    title: `<span style="color: ${color};"><i class="fa-solid fa-graduation-cap"></i> ${label}</span>`,
                    text: @json(__('No hay alumnos en este rango.')),
                    icon: 'info',
                    confirmButtonColor: '#10504B'
                });
                return;
            }

            var content = `
                <div style="text-align: left; max-height: 400px; overflow-y: auto; padding: 5px;">
                    <p style="margin-bottom: 15px; font-size: 0.9rem; color: #64748b;">
                        ${total} {{ __('alumno(s) en este rango') }}
                    </p>
            `;

            alumnos.forEach(function(a) {
                var historyUrl = "{{ route('historial.show', ':id') }}".replace(':id', a.idAlumnos);
                var promColor = a.promedio > 0 && a.promedio < 8.5 ? (a.promedio < 8 ? '#dc2626' : '#f59e0b') : '#059669';
                content += `
                    <a href="${historyUrl}" class="at-risk-modal-item" style="border-left: 4px solid ${color};">
                        <div style="display: flex; align-items: center; gap: 12px;">
                            <img src="https://ui-avatars.com/api/?name=${encodeURIComponent(a.Nombre)}+${encodeURIComponent(a.Apellido)}&background=${color.replace('#', '')}&color=fff" style="width: 32px; height: 32px; border-radius: 50%;">
                            <div>
                                <div style="font-weight: 700; font-size: 0.95rem;">${a.Nombre} ${a.Apellido}</div>
                                <div style="font-size: 0.75rem; color: #94a3b8;">${a.Matricula}</div>
                            </div>
                        </div>
                        <div style="text-align: right;">
                            <div style="font-size: 0.7rem; color: ${promColor}; font-weight: 700; text-transform: uppercase;">
                                PROMEDIO: ${a.promedio || 'N/A'}
                            </div>
                            <i class="fa-solid fa-chevron-right" style="font-size: 0.8rem; color: #cbd5e1;"></i>
                        </div>
                    </a>
                `;
            });

            content += '</div>';

            const isDarkMode = document.body.classList.contains('dark-mode');
            Swal.fire({
                title: `<span style="color: ${isDarkMode ? '#f1f5f9' : color};"><i class="fa-solid fa-graduation-cap"></i> {{ __('Alumnos - ') }} ${label}</span>`,
                html: content,
                showConfirmButton: false,
                showCloseButton: true,
                width: '500px',
                padding: '1.5rem',
                background: isDarkMode ? '#1e293b' : '#fff',
                color: isDarkMode ? '#f1f5f9' : '#2d3748'
            });
        };
    </script>

    <script>
        const fileInput = document.getElementById('profile-upload');
        const profileDisplay = document.getElementById('profile-display');

        fileInput.addEventListener('change', async (e) => {
            const file = e.target.files[0];
            if (!file) return;

            if (file.size > 2 * 1024 * 1024) {
                Swal.fire(@json(__('Error')), @json(__('La imagen supera 2MB. Elige una más ligera.')), 'error');
                fileInput.value = '';
                return;
            }

            Swal.fire({
                title: @json(__('Subiendo imagen...')),
                text: @json(__('Por favor espera')),
                allowOutsideClick: false,
                didOpen: () => Swal.showLoading()
            });

            try {
                const formData = new FormData();
                formData.append('photo', file);

                const response = await fetch(@json(route('perfil.foto.update')), {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    },
                    body: formData
                });

                const raw = await response.text();
                let result = null;
                try { result = JSON.parse(raw); } catch (e) { result = null; }

                if (!result || !response.ok || !result.success) {
                    throw new Error((result && result.message) ? result.message : @json(__('El servidor devolvió una respuesta inesperada. Revisa tu sesión e intenta de nuevo.')));
                }

                profileDisplay.src = result.foto_url;
                Swal.fire(@json(__('¡Éxito!')), @json(__('Foto de perfil actualizada.')), 'success');
            } catch (error) {
                Swal.fire(@json(__('Error')), error.message, 'error');
            }
        });

        const deleteBtn = document.getElementById('delete-photo-btn');
        if (deleteBtn) {
            deleteBtn.addEventListener('click', async () => {
                const result = await Swal.fire({
                    title: @json(__('¿Eliminar foto?')),
                    text: @json(__('Se mostrarán tus iniciales.')),
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc2626',
                    cancelButtonColor: '#64748b',
                    confirmButtonText: @json(__('Eliminar')),
                    cancelButtonText: @json(__('Cancelar'))
                });

                if (!result.isConfirmed) return;

                try {
                    const response = await fetch(@json(route('perfil.foto.delete')), {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        }
                    });

                    const data = await response.json();

                    if (!response.ok || !data.success) {
                        throw new Error(data.message || @json(__('Error al eliminar foto')));
                    }

                    profileDisplay.src = data.foto_url;
                    deleteBtn.remove();

                    Swal.fire(@json(__('Eliminada')), @json(__('Foto eliminada correctamente.')), 'success');
                } catch (error) {
                    Swal.fire(@json(__('Error')), error.message, 'error');
                }
            });
        }
    </script>
</body>
</html>
