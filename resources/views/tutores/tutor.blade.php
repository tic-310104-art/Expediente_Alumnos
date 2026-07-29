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
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
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
                                </div>
                                <button class="chart-toggle" type="button" title="{{ __('Minimizar gráfica') }}">
                                    <i class="fa-solid fa-chevron-up"></i>
                                </button>
                            </div>
                            <div class="enhanced-chart-body">
                                @php $chartId = 'chart-' . $grupo->idGrupos . '-' . $index; @endphp
                                <canvas id="{{ $chartId }}" style="width: 100%;"></canvas>
                                @if($sinDatos > 0)
                                <div class="enhanced-chart-footer">
                                    <i class="fa-solid fa-circle-exclamation"></i> {{ $sinDatos }} {{ __('sin calificaciones') }}
                                </div>
                                @endif
                            </div>
                        </div>

                        @php
                            $chartData = $distribucion->values()->map(fn($r) => [
                                'label' => $r['label'],
                                'count' => $r['count'],
                                'color' => $r['color'],
                                'alumnos' => $r['alumnos'],
                            ]);
                        @endphp
                        <script>
                            (function(){
                                var ctx = document.getElementById('{{ $chartId }}').getContext('2d');
                                var chartData = @json($chartData);
                                var labels = chartData.map(function(d) { return d.label; });
                                var counts = chartData.map(function(d) { return d.count; });
                                var colorsData = chartData.map(function(d) { return d.color; });
                                var alumnosData = chartData.map(function(d) { return d.alumnos; });

                                Chart.defaults.font.family = "'Segoe UI', system-ui, sans-serif";

                                function chartThemeColors() {
                                    var isDark = document.body.classList.contains('dark-mode');
                                    return {
                                        textColor: isDark ? '#f1f5f9' : '#2d3748',
                                        gridColor: isDark ? '#334155' : '#e2e8f0'
                                    };
                                }

                                var theme = chartThemeColors();

                                var chart = new Chart(ctx, {
                                    type: 'bar',
                                    data: {
                                        labels: labels,
                                        datasets: [{
                                            data: counts,
                                            backgroundColor: colorsData,
                                            borderRadius: 6,
                                        }]
                                    },
                                    options: {
                                        responsive: true,
                                        maintainAspectRatio: false,
                                        plugins: {
                                            legend: { display: false },
                                            tooltip: {
                                                callbacks: {
                                                    label: function(context) {
                                                        return context.parsed.y + ' {{ __("alumnos") }}';
                                                    }
                                                }
                                            }
                                        },
                                        scales: {
                                            y: {
                                                beginAtZero: true,
                                                ticks: {
                                                    stepSize: 1,
                                                    precision: 0,
                                                    color: theme.textColor
                                                },
                                                grid: { color: theme.gridColor }
                                            },
                                            x: {
                                                grid: { display: false },
                                                ticks: {
                                                    color: theme.textColor,
                                                    maxRotation: 45
                                                }
                                            }
                                        },
                                        onClick: function(e, elements) {
                                            if (elements.length > 0) {
                                                var idx = elements[0].index;
                                                var alumnos = alumnosData[idx];
                                                var label = labels[idx];
                                                var color = colorsData[idx];
                                                var total = alumnos.length;

                                                if (total === 0) {
                                                    Swal.fire({
                                                        title: '<span style="color: ' + color + ';"><i class="fa-solid fa-graduation-cap"></i> ' + label + '</span>',
                                                        text: @json(__('No hay alumnos en este rango.')),
                                                        icon: 'info',
                                                        confirmButtonColor: '#10504B'
                                                    });
                                                    return;
                                                }

                                                var content = '<div style="text-align: left; max-height: 400px; overflow-y: auto; padding: 5px;">' +
                                                    '<p style="margin-bottom: 15px; font-size: 0.9rem; color: #64748b;">' + total + ' {{ __("alumno(s) en este rango") }}</p>';

                                                alumnos.forEach(function(a) {
                                                    var historyUrl = "{{ route('historial.show', ':id') }}".replace(':id', a.idAlumnos);
                                                    var promColor = a.promedio > 0 && a.promedio < 8.5 ? (a.promedio < 8 ? '#dc2626' : '#f59e0b') : '#059669';
                                                    content += '<a href="' + historyUrl + '" class="at-risk-modal-item" style="border-left: 4px solid ' + color + ';">' +
                                                        '<div style="display: flex; align-items: center; gap: 12px;">' +
                                                        '<img src="https://ui-avatars.com/api/?name=' + encodeURIComponent(a.Nombre) + '+' + encodeURIComponent(a.Apellido) + '&background=' + color.replace('#', '') + '&color=fff" style="width: 32px; height: 32px; border-radius: 50%;">' +
                                                        '<div><div style="font-weight: 700; font-size: 0.95rem;">' + a.Nombre + ' ' + a.Apellido + '</div>' +
                                                        '<div style="font-size: 0.75rem; color: #94a3b8;">' + a.Matricula + '</div></div></div>' +
                                                        '<div style="text-align: right;"><div style="font-size: 0.7rem; color: ' + promColor + '; font-weight: 700; text-transform: uppercase;">PROMEDIO: ' + (a.promedio || 'N/A') + '</div>' +
                                                        '<i class="fa-solid fa-chevron-right" style="font-size: 0.8rem; color: #cbd5e1;"></i></div></a>';
                                                });

                                                content += '</div>';

                                                var isDarkMode = document.body.classList.contains('dark-mode');
                                                Swal.fire({
                                                    title: '<span style="color: ' + (isDarkMode ? '#f1f5f9' : color) + ';"><i class="fa-solid fa-graduation-cap"></i> {{ __("Alumnos - ") }} ' + label + '</span>',
                                                    html: content,
                                                    showConfirmButton: false,
                                                    showCloseButton: true,
                                                    width: '500px',
                                                    padding: '1.5rem',
                                                    background: isDarkMode ? '#1e293b' : '#fff',
                                                    color: isDarkMode ? '#f1f5f9' : '#2d3748'
                                                });
                                            }
                                        }
                                    }
                                });

                                window.__charts = window.__charts || [];
                                window.__charts.push(chart);
                            })();
                        </script>

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
                                                <a href="#" onclick="printViaIframe('{{ route('alumno.pdf.resumen', $alumno->idAlumnos) }}'); return false;" class="btn-accion" style="background:#0d9488;" title="{{ __('Descargar Resumen PDF') }}">
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

    <script>
        (function() {
            var charts = window.__charts;
            if (!charts || charts.length === 0) return;

            function updateChartColors() {
                var isDark = document.body.classList.contains('dark-mode');
                var textColor = isDark ? '#f1f5f9' : '#2d3748';
                var gridColor = isDark ? '#334155' : '#e2e8f0';

                charts.forEach(function(chart) {
                    chart.options.scales.y.ticks.color = textColor;
                    chart.options.scales.y.grid.color = gridColor;
                    chart.options.scales.x.ticks.color = textColor;
                    chart.update();
                });
            }

            var observer = new MutationObserver(function(mutations) {
                mutations.forEach(function(mutation) {
                    if (mutation.attributeName === 'class') {
                        updateChartColors();
                    }
                });
            });

            observer.observe(document.body, { attributes: true, attributeFilter: ['class'] });
        })();
    </script>

    {{-- Modal del Calendario --}}
    <div id="calendar-modal" class="calendar-modal-overlay" style="display:none;">
        <div class="calendar-modal-content">
            <button id="calendar-modal-close" class="calendar-modal-close">
                <i class="fa-solid fa-xmark"></i>
            </button>
            <h3 style="margin-bottom:15px;color:var(--text-main);"><i class="fa-solid fa-calendar-days"></i> {{ __('Calendario de Tutorías') }}</h3>
            <div id='calendar' style="padding:10px;background:#fff;border-radius:8px;"></div>
        </div>
    </div>

    <style>
        .enhanced-chart-body canvas {
            max-height: 160px;
        }
        @media (max-width: 600px) {
            .enhanced-chart-body canvas {
                max-height: 130px;
            }
        }
        @media (max-width: 380px) {
            .btn-accion { width: 26px; height: 26px; font-size: 11px; }
            .btn-accion i { font-size: 11px; }
            .acciones-group { gap: 2px; }
            .enhanced-chart-body canvas {
                max-height: 110px;
            }
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
