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
                    <div class="profile-img-container" onclick="document.getElementById('profile-upload').click()">
                        @php
                            $fotoUrl = $tutor->foto_url ?? "https://ui-avatars.com/api/?name=" . urlencode($tutor->Nombre . '+' . $tutor->Apellido) . "&background=10504B&color=fff&size=100";
                        @endphp
                        <img src="{{ $fotoUrl }}" alt="{{ __('Foto del tutor') }}" class="profile-img" id="profile-display">
                        <div class="change-photo-overlay">
                            <i class="fa-solid fa-camera"></i>
                        </div>
                        <input type="file" id="profile-upload" style="display: none;" accept="image/*">
                    </div>
                    <div class="student-info">
                        <h1>{{ $tutor->Nombre }} {{ $tutor->Apellido }}</h1>
                        <p class="student-id"><i class="fa-solid fa-chalkboard-user"></i> {{ __('Rol') }}: <strong>{{ $tutor->Rol }}</strong></p>
                        <p class="student-career"><i class="fa-solid fa-building"></i> {{ __('Departamento Académico') }}</p>
                    </div>
                </div>
            </header>

            <div class="dashboard-grid">
                <div class="card progress-card" style="flex: 1 1 100%;">
                    <h3><i class="fa-solid fa-chart-pie"></i> {{ __('Resumen de Tutorados') }}</h3>
                    <div style="display: flex; flex-wrap: wrap; gap: 20px;">
                        <div class="stats-container" style="flex: 1; display: flex; gap: 20px; align-items: stretch;">
                            <div class="stat-box" style="flex: 1;">
                                <span class="stat-value">{{ $tutor->alumnos->count() }}</span>
                                <span class="stat-label">{{ __('Alumnos Asignados') }}</span>
                            </div>
                            <div class="stat-box" id="riesgo-box" style="flex: 1; border-left: 1px solid rgba(0,0,0,0.05); cursor: pointer; transition: all 0.3s ease;">
                                <span class="stat-value" style="color: #991b1b;">{{ $riesgoCount ?? 0 }}</span>
                                <span class="stat-label">{{ __('En Riesgo Académico') }} <i class="fa-solid fa-circle-info" style="font-size: 0.8rem; opacity: 0.5;"></i></span>
                            </div>
                        </div>
                    </div>
                </div>
                
                <style>
                    #riesgo-box:hover {
                        background: rgba(153, 27, 27, 0.05);
                        transform: translateY(-2px);
                    }
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

                <div class="card full-width">
                    <h3><i class="fa-solid fa-calendar-days"></i> {{ __('Calendario de Tutorías') }}</h3>
                    <div id='calendar' style="padding: 10px; background: #fff; border-radius: 8px;"></div>
                </div>

                <div class="card full-width">
                    <h3><i class="fa-solid fa-list-ul"></i> Lista de Alumnos Asignados</h3>
                    <div class="table-responsive">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Matrícula</th>
                                    <th>Nombre del Alumno</th>
                                    <th>Cuatrimestre</th>
                                    <th>Estatus</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($tutor->alumnos as $alumno)
                                <tr>
                                    <td><a href="{{ route('alumno.dashboard', $alumno->idAlumnos) }}" style="color:#2b7a78;font-weight:bold;">{{ $alumno->Matricula }}</a></td>
                                    <td>{{ $alumno->Nombre }} {{ $alumno->Apellido }}</td>
                                    <td>{{ $alumno->Cuatrimestre }}</td>
                                    @php
                                        $estatus = strtolower((string) ($alumno->Estatus ?? 'activo'));
                                        $estatusLabel = $estatus === 'baja' ? 'Baja' : ($estatus === 'riesgo' ? 'En riesgo' : 'Activo');
                                        $badgeStyle = $estatus === 'baja'
                                            ? 'background:#fee2e2;color:#991b1b;'
                                            : ($estatus === 'riesgo' ? 'background:#ffedd5;color:#9a3412;' : 'background:#d1fae5;color:#065f46;');
                                    @endphp
                                    <td><span style="display:inline-flex;align-items:center;padding:4px 10px;border-radius:999px;font-weight:700;font-size:12px;border:1px solid rgba(0,0,0,0.06);{{ $badgeStyle }}">{{ __($estatusLabel) }}</span></td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" style="text-align: center;">No tiene alumnos asignados.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </main>
    </div>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Inicializar Calendario Primero para asegurar su carga
            var calendarEl = document.getElementById('calendar');
            if (calendarEl) {
                var calendar = new FullCalendar.Calendar(calendarEl, {
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
                calendar.render();
            }

            // Modal de alumnos en riesgo
            const riesgoBox = document.getElementById('riesgo-box');
            const atRiskData = @json($atRiskList);
            
            if (riesgoBox) {
                riesgoBox.addEventListener('click', () => {
                    if (!atRiskData || atRiskData.length === 0) {
                        Swal.fire({
                            title: @json(__('¡Todo en orden!')),
                            text: @json(__('No hay alumnos en riesgo detectados.')),
                            icon: 'success',
                            confirmButtonColor: '#10504B'
                        });
                        return;
                    }

                    let content = `
                        <div style="text-align: left; max-height: 400px; overflow-y: auto; padding: 5px;">
                            <p style="margin-bottom: 15px; font-size: 0.9rem; color: #64748b;">
                                ${@json(__('Haz clic en un alumno para ver su historial académico completo.'))}
                            </p>
                    `;

                    atRiskData.forEach(alumno => {
                        const historyUrl = "{{ route('historial.show', ':id') }}".replace(':id', alumno.idAlumnos);
                        content += `
                            <a href="${historyUrl}" class="at-risk-modal-item">
                                <div style="display: flex; align-items: center; gap: 12px;">
                                    <img src="https://ui-avatars.com/api/?name=${encodeURIComponent(alumno.Nombre)}+${encodeURIComponent(alumno.Apellido)}&background=dc2626&color=fff" style="width: 32px; height: 32px; border-radius: 50%;">
                                    <div>
                                        <div style="font-weight: 700; font-size: 0.95rem;">${alumno.Nombre} ${alumno.Apellido}</div>
                                        <div style="font-size: 0.75rem; color: #94a3b8;">${alumno.Matricula}</div>
                                    </div>
                                </div>
                                <div style="text-align: right;">
                                    <div style="font-size: 0.7rem; color: #991b1b; font-weight: 700; text-transform: uppercase;">
                                        PROMEDIO: ${alumno.promedio || 'N/A'}
                                    </div>
                                    <i class="fa-solid fa-chevron-right" style="font-size: 0.8rem; color: #cbd5e1;"></i>
                                </div>
                            </a>
                        `;
                    });

                    content += '</div>';

                    Swal.fire({
                        title: `<span style="color: #991b1b;"><i class="fa-solid fa-triangle-exclamation"></i> ${@json(__('Alumnos en Riesgo'))}</span>`,
                        html: content,
                        showConfirmButton: false,
                        showCloseButton: true,
                        width: '500px',
                        padding: '1.5rem',
                        background: document.body.classList.contains('dark-mode') ? '#1e293b' : '#fff'
                    });
                });
            }
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
                    throw new Error((result && result.message) ? result.message : @json(__('El servidor devolvió una respuesta inesperada. Revisa credenciales de Cloudinary / sesión.')));
                }

                profileDisplay.src = result.foto_url;
                Swal.fire(@json(__('¡Éxito!')), @json(__('Foto de perfil actualizada.')), 'success');
            } catch (error) {
                Swal.fire(@json(__('Error')), error.message, 'error');
            }
        });
    </script>
</body>
</html>
