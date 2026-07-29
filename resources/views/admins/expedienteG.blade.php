<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('Administración General') }}</title>
    <link rel="stylesheet" href="{{ asset('expedienteG.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="shortcut icon" href="{{ asset('logo-utn.ico') }}" type="image/x-icon">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        .chart-doughnut-wrapper {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: center;
            gap: 16px;
            padding: 6px 0;
        }
        .chart-doughnut-canvas {
            flex: 0 0 240px;
            max-width: 240px;
            filter: drop-shadow(0 4px 12px rgba(0,0,0,0.12));
            transition: filter 0.3s;
        }
        .chart-doughnut-canvas:hover {
            filter: drop-shadow(0 6px 20px rgba(0,0,0,0.18));
        }
        .chart-doughnut-canvas canvas {
            width: 100% !important;
            height: auto !important;
            aspect-ratio: 1;
        }
        .chart-doughnut-legend {
            flex: 0 1 280px;
            min-width: 160px;
            display: flex;
            flex-direction: column;
            gap: 6px;
        }
        @media (max-width: 700px) {
            .chart-doughnut-wrapper {
                flex-direction: column;
                text-align: center;
            }
            .chart-doughnut-canvas {
                flex: 0 0 200px;
                max-width: 200px;
            }
            .chart-doughnut-legend {
                width: 100%;
                min-width: unset;
                flex: unset;
            }
        }
        @media (max-width: 400px) {
            .chart-doughnut-canvas {
                flex: 0 0 170px;
                max-width: 170px;
            }
        }
    </style>
</head>
<body>

    <div class="dashboard-container">
        @include('partials.sidebar', ['active' => 'resumen'])

        <main class="main-content">
            <header class="student-header">
                <div class="student-profile">
                    <div class="profile-img-container" onclick="document.getElementById('profile-upload').click()">
                        @php
                            $fotoUrl = ($admin_profile && $admin_profile->foto_url) ? $admin_profile->foto_url : "https://ui-avatars.com/api/?name=Admin+General&background=10504B&color=fff&size=100";
                        @endphp
                        <img src="{{ $fotoUrl }}" alt="{{ __('Foto del administrador') }}" class="profile-img" id="profile-display">
                        <div class="change-photo-overlay">
                            <i class="fa-solid fa-camera"></i>
                        </div>
                        <input type="file" id="profile-upload" style="display:none;" accept="image/*">
                    </div>
                    <div class="student-info">
                        <h1>{{ __('Administración General') }}</h1>
                        <p><i class="fa-solid fa-shield-halved"></i> {{ __('Rol') }}: <strong>{{ __('Servicios Escolares') }}</strong></p>
                        <p style="font-size:14px;color:var(--text-muted);margin-top:2px;"><i class="fa-solid fa-university"></i> {{ __('Universidad Tecnológica de Nayarit') }}</p>
                    </div>
                </div>
            </header>

            <div class="dashboard-grid">
                <div class="card full-width">
                    <h3><i class="fa-solid fa-chart-pie"></i> {{ __('Distribución General del Sistema') }}</h3>
                    <div class="chart-doughnut-wrapper">
                        <div class="chart-doughnut-canvas">
                            <canvas id="chartAdminResumen"></canvas>
                        </div>
                        <div class="chart-doughnut-legend" id="chartAdminLegend"></div>
                    </div>
                </div>

                <div class="card">
                    <h3 class="toggle-header" onclick="toggleSection('alumnos-list', 'alumnos-icon')">
                        <span><i class="fa-solid fa-laptop-code"></i> {{ __('Alumnos Activos por Carrera') }}</span>
                        <i id="alumnos-icon" class="fa-solid fa-chevron-down toggle-icon" style="transform: rotate(180deg);"></i>
                    </h3>
                    <ul class="info-list" id="alumnos-list">
                        @foreach($carreras as $carrera)
                        <li><strong>{{ $carrera->Nombre }}:</strong> <span>{{ $carrera->total_alumnos }} {{ __('Alumnos') }}</span></li>
                        @endforeach
                    </ul>
                </div>

                 <div class="card">
                    <h3 class="toggle-header" onclick="toggleSection('tutores-list', 'tutores-icon')">
                        <span><i class="fa-solid fa-chalkboard-user"></i> {{ __('Tutores por Carrera') }}</span>
                        <i id="tutores-icon" class="fa-solid fa-chevron-down toggle-icon" style="transform: rotate(180deg);"></i>
                    </h3>
                    <ul class="info-list" id="tutores-list">
                        @foreach($carreras as $carrera)
                        <li><strong>{{ $carrera->Nombre }}:</strong> <span>{{ $carrera->total_tutores }} {{ __('Tutores') }}</span></li>
                        @endforeach
                    </ul>
                </div>

                <div class="card">
                    <h3><i class="fa-solid fa-database"></i> {{ __('Respaldos Automatizados') }}</h3>
                    
                    @if($activeBackup)
                        <div class="backup-banner">
                            <div class="backup-banner-icon">
                                <i class="fa-solid fa-calendar-check"></i>
                            </div>
                            <div>
                                <h4 class="backup-banner-title">{{ __('Respaldo Agendado en el Sistema') }}</h4>
                                <p class="backup-banner-text">
                                    {{ __('Siguiente ejecución automática:') }} <strong>{{ \Carbon\Carbon::parse($activeBackup->scheduled_date)->format('d/m/Y') }}</strong> {{ __('a las') }} <strong>{{ $activeBackup->scheduled_time }}</strong>
                                    <br>
                                    <span class="backup-banner-chip">
                                        <i class="fa-solid fa-repeat"></i> {{ __('Frecuencia:') }} 
                                        @if($activeBackup->frequency == 'once') {{ __('Una sola vez') }}
                                        @elseif($activeBackup->frequency == '4_days') {{ __('Cada 4 días') }}
                                        @elseif($activeBackup->frequency == '7_days') {{ __('Cada semana') }}
                                        @elseif($activeBackup->frequency == 'monthly') {{ __('Cada mes') }}
                                        @endif
                                    </span>
                                </p>
                            </div>
                        </div>
                    @endif

                    <div class="backup-grid">
                        
                        <div class="backup-card">
                            <form id="form-schedule" action="{{ route('backup.schedule') }}" method="POST">
                                @csrf
                                <h4 class="backup-card-title">
                                    <i class="fa-solid fa-clock"></i> {{ __('Programar Nuevo Respaldo') }}
                                </h4>
                                <div class="form-group">
                                    <label class="backup-label">{{ __('Fecha de inicio') }}</label>
                                    <input type="date" name="backup_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                                </div>
                                <div class="form-group" style="margin-top:15px;">
                                    <label class="backup-label">{{ __('Hora sugerida') }}</label>
                                    <input type="time" name="backup_time" class="form-control" value="02:00" required>
                                </div>
                                <div class="form-group" style="margin-top:15px;">
                                    <label class="backup-label">{{ __('Periodicidad') }}</label>
                                    <select name="frequency" class="form-control" required>
                                        <option value="once">{{ __('Una sola vez (Hoy)') }}</option>
                                        <option value="4_days">{{ __('Cada 4 días') }}</option>
                                        <option value="7_days">{{ __('Cada semana') }}</option>
                                        <option value="monthly">{{ __('Cada mes') }}</option>
                                    </select>
                                </div>
                                <button type="button" id="btn-schedule" class="btn-primary backup-submit">
                                    <i class="fa-solid fa-shield-halved"></i> {{ __('Aplicar Configuración') }}
                                </button>
                            </form>
                        </div>

                        <div class="backup-card">
                            <form id="form-manual-backup" action="{{ route('backup.manual') }}" method="POST">
                                @csrf
                                <h4 class="backup-card-title">
                                    <i class="fa-solid fa-bolt"></i> {{ __('Respaldo Manual Inmediato') }}
                                </h4>
                                <p class="backup-card-text">
                                    {{ __('Genera un respaldo completo de la base de datos en este mismo momento.') }}
                                </p>
                                <button type="button" id="btn-manual-backup" class="btn-primary backup-submit">
                                    <i class="fa-solid fa-database"></i> {{ __('Ejecutar Respaldo Manual') }}
                                </button>
                            </form>
                        </div>

                        <div class="backup-card restore-card">
                            <form id="form-import" action="{{ route('backup.import') }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <h4 class="backup-card-title" style="color:#b45309;">
                                    <i class="fa-solid fa-file-import"></i> {{ __('Restauración Manual') }}
                                </h4>
                                <div class="form-group">
                                    <label class="backup-label" style="color:#b45309;">{{ __('Seleccionar archivo .sql') }}</label>
                                    <input type="file" name="backup_file" class="form-control" accept=".sql" required>
                                </div>
                                <button type="button" id="btn-import" class="btn-primary backup-submit" style="background-color:#d97706;">
                                    <i class="fa-solid fa-shield-halved"></i> {{ __('Iniciar Restauración') }}
                                </button>
                            </form>
                        </div>
                    </div>
                </div> {{-- card --}}
            </div> {{-- dashboard-grid --}}
        </main> {{-- main-content --}}
    </div> {{-- dashboard-container --}}

    <script>
        const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

        function toggleSection(listId, iconId) {
            const list = document.getElementById(listId);
            const icon = document.getElementById(iconId);
            if (list.style.display === 'none') {
                list.style.display = 'block';
                icon.style.transform = 'rotate(180deg)';
            } else {
                list.style.display = 'none';
                icon.style.transform = 'rotate(0deg)';
            }
        }

        // --- Gráfica doughnut de distribución ---
        function initAdminChart() {
            var ctx = document.getElementById('chartAdminResumen');
            if (!ctx) return;

            Chart.defaults.font.family = "'Segoe UI', system-ui, sans-serif";

            var labels = [
                '{{ __("Alumnos Activos") }}',
                '{{ __("Bajas") }}',
                '{{ __("Tutores") }}',
                '{{ __("Administradores") }}'
            ];
            var data = [
                {{ $totalAlumnos }},
                {{ $totalBajas }},
                {{ $totalTutores }},
                {{ $totalAdmins }}
            ];
            var colors = ['#F59E0B', '#EF4444', '#8B5CF6', '#06B6D4'];

            function buildLegend() {
                var container = document.getElementById('chartAdminLegend');
                if (!container) return;
                var html = '';
                for (var i = 0; i < labels.length; i++) {
                    html += '<div style="display:flex;align-items:center;gap:8px;padding:5px 10px;border-radius:6px;border:1px solid var(--border-color);">' +
                        '<span style="width:14px;height:14px;border-radius:4px;background:' + colors[i] + ';flex-shrink:0;"></span>' +
                        '<div style="flex:1;"><div style="font-weight:700;font-size:0.95rem;">' + data[i] + '</div>' +
                        '<div style="font-size:0.7rem;color:var(--text-muted);line-height:1.2;">' + labels[i] + '</div></div></div>';
                }
                container.innerHTML = html;
            }

            function themeText() {
                return document.body.classList.contains('dark-mode') ? '#f1f5f9' : '#2d3748';
            }

            var totalAll = data.reduce(function(a, b) { return a + b; }, 0);

            var centerTextPlugin = {
                id: 'centerText',
                afterDraw: function(chart) {
                    var ctx = chart.ctx;
                    var meta = chart.getDatasetMeta(0);
                    if (!meta || !meta.data || !meta.data.length) return;
                    var centerX = meta.data[0].x;
                    var centerY = meta.data[0].y;
                    var isDark = document.body.classList.contains('dark-mode');
                    ctx.save();
                    ctx.textAlign = 'center';
                    ctx.textBaseline = 'middle';
                    ctx.font = 'bold 32px "Segoe UI", system-ui, sans-serif';
                    ctx.fillStyle = isDark ? '#f1f5f9' : '#1e293b';
                    ctx.fillText(totalAll, centerX, centerY - 8);
                    ctx.font = '12px "Segoe UI", system-ui, sans-serif';
                    ctx.fillStyle = isDark ? '#94a3b8' : '#64748b';
                    ctx.fillText('Total', centerX, centerY + 18);
                    ctx.restore();
                }
            };

            var chart = new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: labels,
                    datasets: [{
                        data: data,
                        backgroundColor: colors,
                        borderWidth: 2,
                        borderColor: '#ffffff',
                        hoverBorderColor: '#ffffff',
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    cutout: '40%',
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    var total = context.dataset.data.reduce(function(a, b) { return a + b; }, 0);
                                    var pct = ((context.parsed / total) * 100).toFixed(1);
                                    return context.label + ': ' + context.parsed + ' (' + pct + '%)';
                                }
                            }
                        }
                    }
                },
                plugins: [centerTextPlugin]
            });

            buildLegend();

            // Observer dark mode
            var obs = new MutationObserver(function() {
                buildLegend();
                chart.update();
            });
            obs.observe(document.body, { attributes: true, attributeFilter: ['class'] });
        }

        document.addEventListener('DOMContentLoaded', initAdminChart);

        async function promptTokenAndActivate() {
            return Swal.fire({
                title: @json(__('Confirmación de Seguridad')),
                html: `
                    <div style="text-align: center; padding: 10px;">
                        <div style="background: var(--bg-color); padding: 15px; border-radius: 12px; margin-bottom: 20px;">
                            <i class="fa-solid fa-shield-halved" style="font-size: 3rem; color: var(--primary-color); margin-bottom: 15px;"></i>
                            <p style="color: var(--text-main); font-weight: 600; margin-bottom: 5px;">@json(__('Acción Crítica Detectada'))</p>
                            <p style="color: var(--text-muted); font-size: 0.9rem; margin: 0;">@json(__('Para proteger la integridad del sistema, por favor ingresa tu token de seguridad.'))</p>
                        </div>
                        <label style="display: block; text-align: left; margin-bottom: 8px; font-weight: 600; color: var(--text-main);">@json(__('Token JWT'))</label>
                        <input id="swal-token" class="swal2-input" placeholder="eyJhbGciOiJIUzI1Ni..." style="width: 100%; margin: 0; padding: 12px; border-radius: 8px; border: 1px solid var(--border-color); box-sizing: border-box; background: var(--card-bg); color: var(--text-main);">
                    </div>
                `,
                showCancelButton: true,
                confirmButtonText: @json(__('Autorizar Acción')),
                cancelButtonText: @json(__('Cancelar')),
                confirmButtonColor: '#0d9488',
                cancelButtonColor: '#6b7280',
                width: '500px',
                padding: '1.5rem',
                focusConfirm: false,
                showLoaderOnConfirm: true,
                background: document.body.classList.contains('dark-mode') ? '#1e293b' : '#fff',
                color: document.body.classList.contains('dark-mode') ? '#f1f5f9' : '#2d3748',
                preConfirm: () => {
                    const token = document.getElementById('swal-token').value;
                    if (!token) {
                        Swal.showValidationMessage(@json(__('El token es obligatorio')));
                        return false;
                    }
                    return fetch("{{ route('jwt.verify') }}", {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({ token: token })
                    })
                    .then(response => {
                        if (!response.ok) return response.json().then(json => { throw new Error(json.message) });
                        return response.json();
                    })
                    .catch(error => Swal.showValidationMessage(`Error: ${error.message}`));
                },
                allowOutsideClick: () => !Swal.isLoading()
            }).then(result => result.isConfirmed);
        }

        document.addEventListener('DOMContentLoaded', function() {
            // Botón: Programar Respaldo
            const btnSchedule = document.getElementById('btn-schedule');
            if (btnSchedule) {
                btnSchedule.addEventListener('click', async function() {
                    const form = document.getElementById('form-schedule');
                    if (!form.checkValidity()) { form.reportValidity(); return; }
                    const ok = await promptTokenAndActivate();
                    if (ok) {
                        Swal.fire({
                            title: @json(__('¡Autorizado!')),
                            text: @json(__('Agendando respaldo...')),
                            icon: 'success',
                            timer: 1500,
                            showConfirmButton: false,
                            willClose: () => form.submit()
                        });
                    }
                });
            }

            // Botón: Respaldo Manual
            const btnManualBackup = document.getElementById('btn-manual-backup');
            if (btnManualBackup) {
                btnManualBackup.addEventListener('click', async function() {
                    const form = document.getElementById('form-manual-backup');
                    const ok = await promptTokenAndActivate();
                    if (ok) {
                        Swal.fire({
                            title: @json(__('Generando respaldo...')),
                            text: @json(__('Por favor espera, esto puede tardar algunos segundos.')),
                            allowOutsideClick: false,
                            didOpen: () => Swal.showLoading()
                        });

                        try {
                            const formData = new FormData(form);
                            const resp = await fetch(form.action, {
                                method: 'POST',
                                headers: {
                                    'X-CSRF-TOKEN': csrfToken,
                                    'X-Requested-With': 'XMLHttpRequest',
                                    'Accept': 'application/json'
                                },
                                body: formData
                            });

                            const raw = await resp.text();
                            let json = null;
                            try { json = JSON.parse(raw); } catch (e) { json = null; }

                            if (!json || !resp.ok) {
                                throw new Error((json && json.message) ? json.message : @json(__('El servidor devolvió una respuesta inesperada.')));
                            }

                            if (!json.success) {
                                throw new Error(json.message || @json(__('No se pudo crear el respaldo.')));
                            }

                            Swal.fire(@json(__('¡Respaldo Exitoso!')), json.message, 'success');
                        } catch (e) {
                            Swal.fire(@json(__('Error')), e.message, 'error');
                        }
                    }
                });
            }

            // Botón: Restaurar Backup
            const btnImport = document.getElementById('btn-import');
            if (btnImport) {
                btnImport.addEventListener('click', async function() {
                    const form = document.getElementById('form-import');
                    if (!form.checkValidity()) { form.reportValidity(); return; }
                    const ok = await promptTokenAndActivate();
                    if (ok) {
                        Swal.fire({
                            title: @json(__('Restaurando...')),
                            text: @json(__('Por favor espera, esto puede tardar algunos segundos.')),
                            allowOutsideClick: false,
                            didOpen: () => Swal.showLoading()
                        });

                        try {
                            const formData = new FormData(form);
                            const resp = await fetch(form.action, {
                                method: 'POST',
                                headers: {
                                    'X-CSRF-TOKEN': csrfToken,
                                    'X-Requested-With': 'XMLHttpRequest',
                                    'Accept': 'application/json'
                                },
                                body: formData
                            });

                            const raw = await resp.text();
                            let json = null;
                            try { json = JSON.parse(raw); } catch (e) { json = null; }

                            if (!json || !resp.ok) {
                                throw new Error((json && json.message) ? json.message : @json(__('El servidor devolvió una respuesta inesperada.')));
                            }

                            if (!json.success) {
                                throw new Error(json.message || @json(__('No se pudo restaurar la base de datos.')));
                            }

                            Swal.fire(@json(__('Listo')), json.message || @json(__('Base de datos restaurada correctamente.')), 'success');
                        } catch (e) {
                            Swal.fire(@json(__('Error')), e.message, 'error');
                        }
                    }
                });
            }
        });

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

                // Guardar el MySQL
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

                if (result.success) {
                    profileDisplay.src = result.foto_url;
                    Swal.fire(@json(__('¡Éxito!')), @json(__('Foto de perfil actualizada.')), 'success');
                } else {
                    throw new Error(result.message);
                }
            } catch (error) {
                Swal.fire(@json(__('Error')), error.message, 'error');
            }
        });

        // Alertas para sesiones (se fusionan con el DOMContentLoaded de arriba vía bloque separado)
        document.addEventListener('DOMContentLoaded', function() {
            @if(session('success'))
                Swal.fire({
                    title: @json(__('¡Éxito!')),
                    text: @json(session('success')),
                    icon: 'success',
                    confirmButtonText: @json(__('Ok'))
                });
            @endif

            @if(session('import_success'))
                Swal.fire({
                    title: @json(__('¡Base de Datos Restaurada!')),
                    text: @json(session('import_success')),
                    icon: 'success',
                    confirmButtonText: @json(__('Excelente'))
                });
            @endif

            @if(session('import_error'))
                Swal.fire({
                    title: @json(__('Error de Importación')),
                    text: @json(session('import_error')),
                    icon: 'error',
                    confirmButtonText: @json(__('Entendido'))
                });
            @endif
        });
    </script>
</body>
</html>
