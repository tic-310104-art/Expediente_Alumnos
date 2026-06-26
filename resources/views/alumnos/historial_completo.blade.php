<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Historial Académico - {{ $alumno->Nombre }} {{ $alumno->Apellido }}</title>
    <link rel="stylesheet" href="{{ asset('expedienteG.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="shortcut icon" href="{{ asset('logo-utn.ico') }}" type="image/x-icon">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body>

    <div class="dashboard-container">
        @include('partials.sidebar', ['active' => 'historial'])

        <main class="main-content">
            <header class="student-header">
                <div class="student-profile">
                    <div class="profile-img-container">
                        @php
                            $fotoUrl = ($alumno->foto_url) ? $alumno->foto_url : "https://ui-avatars.com/api/?name=" . urlencode($alumno->Nombre . '+' . $alumno->Apellido) . "&background=10504B&color=fff&size=100";
                        @endphp
                        <img src="{{ $fotoUrl }}" alt="Foto del alumno" class="profile-img">
                    </div>
                    <div class="student-info">
                        <h1>{{ $alumno->Nombre }} {{ $alumno->Apellido }}</h1>
                        <p class="student-id"><i class="fa-solid fa-id-card"></i> Matrícula: <strong>{{ $alumno->Matricula }}</strong></p>
                        <p class="student-career"><i class="fa-solid fa-graduation-cap"></i> {{ $alumno->carreras->first()->Nombre ?? 'Sin carrera' }}</p>
                        <p class="student-group"><i class="fa-solid fa-users"></i> Grupo: <strong>{{ $alumno->grupo->Nombre ?? 'Sin grupo' }}</strong></p>
                        <p class="student-tutor"><i class="fa-solid fa-chalkboard-teacher"></i> Tutor: <strong>{{ $alumno->tutor->Nombre . ' ' . $alumno->tutor->Apellido ?? 'Sin tutor' }}</strong></p>
                    </div>
                </div>
            </header>

            <div class="dashboard-grid">
                <div class="card full-width">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                        <h3><i class="fa-solid fa-graduation-cap"></i> Historial Académico Completo</h3>
                        <div>
                            <button onclick="window.print()" class="btn-secondary">
                                <i class="fa-solid fa-print"></i> Imprimir
                            </button>
                            <a href="{{ route('alumno.expediente.pdf', $alumno->idAlumnos) }}" class="btn-primary" style="margin-left: 10px;">
                                <i class="fa-solid fa-file-pdf"></i> PDF
                            </a>
                        </div>
                    </div>
                    
                    @if($historial->count() > 0)
                        <div class="alert alert-info" role="alert" style="border-left: 6px solid #10504B; padding: 18px; margin-bottom: 20px; background: #e9f6f6; color: #0c4f50;">
                            <h4 style="margin-top: 0; margin-bottom: 10px;"><i class="fa-solid fa-chart-bar"></i> Resumen Académico</h4>
                            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(160px, 1fr)); gap: 10px; margin-bottom: 12px;">
                                <div style="background: #fff; padding: 10px; border-radius: 8px; box-shadow: inset 0 0 0 1px rgba(16,80,75,0.15);">
                                    <div style="font-size: 1.3rem; font-weight: 700;">{{ $historial->count() }}</div>
                                    <div style="font-size: 0.82rem; color: #2d4d51;">Total Materias</div>
                                </div>
                                <div style="background: #fff; padding: 10px; border-radius: 8px; box-shadow: inset 0 0 0 1px rgba(16,80,75,0.15);">
                                    <div style="font-size: 1.3rem; font-weight: 700;">{{ number_format($historial->where('Calificacion', '>=', 70)->count(), 0) }}</div>
                                    <div style="font-size: 0.82rem; color: #2d4d51;">Materias Aprobadas</div>
                                </div>
                                <div style="background: #fff; padding: 10px; border-radius: 8px; box-shadow: inset 0 0 0 1px rgba(16,80,75,0.15);">
                                    <div style="font-size: 1.3rem; font-weight: 700;">{{ number_format($historial->avg('Calificacion'), 1) }}</div>
                                    <div style="font-size: 0.82rem; color: #2d4d51;">Promedio</div>
                                </div>
                                <div style="background: #fff; padding: 10px; border-radius: 8px; box-shadow: inset 0 0 0 1px rgba(16,80,75,0.15);">
                                    <div style="font-size: 1.3rem; font-weight: 700;">{{ number_format($historial->max('Calificacion'), 1) }}</div>
                                    <div style="font-size: 0.82rem; color: #2d4d51;">Máx</div>
                                </div>
                            </div>
                            <button id="toggleHistorialDetail" type="button" class="btn-primary" style="background: #10504B; border-color: #10504B; padding: 10px 14px; font-weight: 700;">Ver detalle del historial</button>
                        </div>

                        <div id="detalle-historial" style="display: none;">
                            <div class="table-responsive">
                                <table class="info-table" id="historial-table">
                                    <thead>
                                        <tr>
                                            <th>Materia</th>
                                            <th>Maestro</th>
                                            <th>Cuatrimestre</th>
                                            <th>Calificación</th>
                                            <th>Periodo</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($historial as $registro)
                                            <tr>
                                                <td>
                                                    <strong>{{ optional($registro->grupoMateria)->materia->Nombre ?? $registro->Materia }}</strong>
                                                    <br>
                                                    <small style="color: var(--text-muted);">
                                                        {{ optional(optional($registro->grupoMateria)->materia)->Cuatrimestre ? optional($registro->grupoMateria)->materia->Cuatrimestre . '° Cuatrimestre' : '' }}
                                                    </small>
                                                </td>
                                                <td>{{ optional($registro->grupoMateria)->Maestro ?? $registro->Profesor }}</td>
                                                <td>{{ optional(optional($registro->grupoMateria)->materia)->Cuatrimestre ?? 'N/A' }}</td>
                                                <td>{{ number_format($registro->Calificacion, 1) }}</td>
                                                <td>{{ $registro->Ciclo ?? 'N/A' }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="resumen-calificaciones" style="margin-top: 30px; padding: 20px; background-color: var(--card-bg); border-radius: 8px; border: 1px solid var(--border-color);">
                            <h4 style="margin-bottom: 15px;"><i class="fa-solid fa-chart-bar"></i> Resumen Académico</h4>
                            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px;">
                                <div class="stat-item">
                                    <div class="stat-value">{{ $historial->count() }}</div>
                                    <div class="stat-label">Total de Materias</div>
                                </div>
                                <div class="stat-item">
                                    <div class="stat-value">{{ number_format($historial->where('Calificacion', '>=', 70)->count(), 0) }}</div>
                                    <div class="stat-label">Materias Aprobadas</div>
                                </div>
                                <div class="stat-item">
                                    <div class="stat-value">{{ number_format($historial->avg('Calificacion'), 1) }}</div>
                                    <div class="stat-label">Promedio General</div>
                                </div>
                                <div class="stat-item">
                                    <div class="stat-value">{{ number_format($historial->max('Calificacion'), 1) }}</div>
                                    <div class="stat-label">Calificación Más Alta</div>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="empty-state">
                            <i class="fa-solid fa-book-open" style="font-size: 48px; color: var(--text-muted); margin-bottom: 15px;"></i>
                            <h4>No hay historial académico registrado</h4>
                            <p>Este alumno aún no tiene calificaciones asignadas.</p>
                            @if(auth()->user()->role === 'tutor')
                                <a href="{{ route('calificaciones.asignar', $alumno->idAlumnos) }}" class="btn-primary">
                                    <i class="fa-solid fa-plus"></i> Asignar Calificaciones
                                </a>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </main>
    </div>

    <style>
        .table-responsive {
            overflow-x: auto;
        }
        
        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        
        .info-table th,
        .info-table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid var(--border-color);
        }
        
        .info-table th {
            background-color: var(--card-bg);
            font-weight: bold;
            color: var(--text-primary);
        }
        
        .info-table tr:hover {
            background-color: var(--hover-bg);
        }
        
        .calificacion-badge {
            padding: 4px 12px;
            border-radius: 20px;
            font-weight: bold;
            font-size: 14px;
            display: inline-block;
        }
        
        .calificacion-badge.aprobado {
            background-color: #d4edda;
            color: #155724;
        }
        
        .calificacion-badge.reprobado {
            background-color: #f8d7da;
            color: #721c24;
        }
        
        .empty-state {
            text-align: center;
            padding: 40px;
            color: var(--text-muted);
        }
        
        .resumen-calificaciones {
            margin-top: 30px;
        }
        
        .stat-item {
            text-align: center;
            padding: 15px;
            background-color: var(--card-bg);
            border-radius: 8px;
            border: 1px solid var(--border-color);
        }
        
        .stat-value {
            font-size: 24px;
            font-weight: bold;
            color: var(--primary-color);
            margin-bottom: 5px;
        }
        
        .stat-label {
            font-size: 14px;
            color: var(--text-muted);
        }

        /* Estilos para impresión */
        @media print {
            .sidebar {
                display: none !important;
            }
            
            .main-content {
                margin-left: 0 !important;
            }
            
            .btn-primary, .btn-secondary {
                display: none !important;
            }
            
            .dashboard-container {
                background: white !important;
            }
            
            .card {
                background: white !important;
                box-shadow: none !important;
                border: 1px solid #000 !important;
            }
        }
    </style>

    <script>
        // Función para exportar a Excel
        function exportToExcel() {
            const table = document.getElementById('historial-table');
            const rows = table.querySelectorAll('tr');
            let csv = [];
            
            // Headers
            const headers = Array.from(table.querySelectorAll('th')).map(th => th.textContent.trim());
            csv.push(headers.join(','));
            
            // Data
            rows.forEach(row => {
                const cells = Array.from(row.querySelectorAll('td')).map(td => td.textContent.trim());
                if (cells.length > 0) {
                    csv.push(cells.join(','));
                }
            });
            
            // Download
            const csvContent = csv.join('\n');
            const blob = new Blob([csvContent], { type: 'text/csv' });
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = 'historial_academico_{{ $alumno->Matricula }}.csv';
            a.click();
            window.URL.revokeObjectURL(url);
        }

        document.addEventListener('DOMContentLoaded', function() {
            const toggleButton = document.getElementById('toggleHistorialDetail');
            const detalle = document.getElementById('detalle-historial');

            if (toggleButton && detalle) {
                toggleButton.addEventListener('click', function() {
                    const isVisible = detalle.style.display === 'block';
                    detalle.style.display = isVisible ? 'none' : 'block';
                    toggleButton.textContent = isVisible ? 'Ver detalle del historial' : 'Ocultar detalle';
                });
            }
        });
    </script>
</body>
</html>
