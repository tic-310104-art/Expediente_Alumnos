<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('Mi Reporte de Desempeño') }} | {{ __('Expediente del Alumno') }}</title>
    <link rel="stylesheet" href="{{ asset('expedienteG.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="shortcut icon" href="{{ asset('logo-utn.ico') }}" type="image/x-icon">
    <style>
        .btn-download-premium {
            display: inline-flex;
            align-items: center;
            gap: 12px;
            padding: 12px 24px;
            border-radius: 14px;
            text-decoration: none;
            background: #10504B;
            color: #ffffff;
            font-weight: 700;
            font-size: 14px;
            letter-spacing: 0.03em;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            border: 1px solid rgba(255, 255, 255, 0.1);
            box-shadow: 0 4px 15px rgba(16, 80, 75, 0.2);
            position: relative;
            overflow: hidden;
        }

        .btn-download-premium::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(
                120deg,
                transparent,
                rgba(255, 255, 255, 0.2),
                transparent
            );
            transition: all 0.6s;
        }

        .btn-download-premium:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(16, 80, 75, 0.3);
            background: #14635d;
            color: #fff;
        }

        .btn-download-premium:hover::before {
            left: 100%;
        }

        .btn-download-premium i {
            font-size: 18px;
            filter: drop-shadow(0 2px 4px rgba(0,0,0,0.1));
        }

        .btn-download-premium:active {
            transform: translateY(0);
        }

        /* Dark Mode Adjustments */
        body.dark-mode .stat-value {
            color: #4ade80 !important;
        }
        body.dark-mode .stat-label {
            color: #94a3b8 !important;
        }
        body.dark-mode strong, body.dark-mode b {
            color: #f1f5f9 !important;
        }
        body.dark-mode td {
            color: #cbd5e1 !important;
        }
        body.dark-mode .data-table thead th {
            background: #0f172a !important;
            color: #f1f5f9 !important;
        }
        body.dark-mode .card h3 {
            color: #f1f5f9 !important;
        }
        body.dark-mode .student-info h1 {
            color: #f1f5f9 !important;
        }
        body.dark-mode .period-title {
            color: #f1f5f9 !important;
        }
    </style>
</head>
<body>

    <div class="dashboard-container">
        @include('partials.sidebar', ['active' => 'desempeno'])

        <main class="main-content">
            <header class="student-header">
                <div class="student-profile">
                    @php
                        $alumnoFoto = $alumno->foto_url ?? "https://ui-avatars.com/api/?name=" . urlencode($alumno->Nombre . '+' . $alumno->Apellido) . "&background=10504B&color=fff&size=100";
                    @endphp
                    <img src="{{ $alumnoFoto }}" alt="Foto del alumno" class="profile-img">
                    <div class="student-info">
                        <h1>{{ __('Mi Bitácora de Desempeño') }}</h1>
                        <p class="student-id"><i class="fa-solid fa-id-card"></i> {{ __('Matrícula') }}: <strong>{{ $alumno->Matricula }}</strong></p>
                    </div>
                </div>
                <div style="display: flex; flex-direction: column; align-items: flex-end; gap: 12px;">
                    <div class="student-status" style="background: #10504B; color: white; padding: 10px 18px; border-radius: 12px; font-weight: 700; box-shadow: 0 4px 12px rgba(0,0,0,0.05);">
                        <span>{{ __('Promedio General') }}: <strong>{{ $alumno->historialAcademico->avg('Calificacion') ? number_format($alumno->historialAcademico->avg('Calificacion'), 1) : 'N/A' }}</strong></span>
                    </div>
                </div>
            </header>

            <div class="dashboard-grid" style="margin-bottom: 25px;">
                
                <div class="card progress-card">
                    <h3><i class="fa-solid fa-chart-pie"></i> {{ __('Resumen General de Desempeño') }}</h3>
                    @php
                        $promedioGeneral = $alumno->historialAcademico->avg('Calificacion');
                        $materiasTotales = $alumno->historialAcademico->count();
                        $riesgoGeneral = \App\Models\Alumno::getRiesgoStatus($promedioGeneral);
                        $colorRiesgo = \App\Models\Alumno::getRiesgoColor($promedioGeneral);
                    @endphp
                    <div class="stats-container" style="display: flex; gap: 20px; justify-content: space-around; margin-top:15px;">
                        <div class="stat-box" style="text-align: center;">
                            <span class="stat-value" style="font-size: 2em; display: block; font-weight: 800; color: #10504B;">{{ $promedioGeneral ? number_format($promedioGeneral, 1) : 'N/A' }}</span>
                            <span class="stat-label" style="color: #64748b; font-size: 13px; font-weight: 600;">{{ __('Promedio General') }}</span>
                        </div>
                        <div class="stat-box" style="text-align: center;">
                            <span class="stat-value" style="font-size: 2em; display: block; font-weight: 800; color: #10504B;">{{ $materiasTotales }}</span>
                            <span class="stat-label" style="color: #64748b; font-size: 13px; font-weight: 600;">{{ __('Materias Cursadas') }}</span>
                        </div>
                        <div class="stat-box" style="text-align: center;">
                            <span class="stat-value" style="font-size: 1.5em; display: block; font-weight: 800; color: {{ $colorRiesgo }};">
                                {{ $riesgoGeneral }}
                            </span>
                            <span class="stat-label" style="color: #64748b; font-size: 13px; font-weight: 600;">{{ __('Nivel de Riesgo Global') }}</span>
                        </div>
                    </div>
                </div>

            </div>

            <div class="card full-width">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                        <h3 style="margin: 0;"><i class="fa-solid fa-calendar-check"></i> {{ __('Desempeño por Periodo Académico') }}</h3>
                    </div>
                    
                    <div class="table-responsive">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>{{ __('Periodo Escolar') }}</th>
                                    <th style="text-align: center;">{{ __('Materias') }}</th>
                                    <th style="text-align: center;">{{ __('Promedio del Periodo') }}</th>
                                    <th style="text-align: center;">{{ __('Estatus de Riesgo') }}</th>
                                    <th style="text-align: center;">{{ __('Acciones') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($periodos as $ciclo => $data)
                                <tr>
                                    <td>
                                        <div style="display: flex; align-items: center; gap: 10px;">
                                            <div style="background: #10504B10; color: #10504B; width: 35px; height: 35px; border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                                                <i class="fa-solid fa-calendar-day"></i>
                                            </div>
                                            <strong class="period-title" style="color: #1e293b;">{{ $ciclo }}</strong>
                                        </div>
                                    </td>
                                    <td style="text-align: center; color: #64748b;">{{ $data['materias_count'] }} {{ __('Materias') }}</td>
                                    <td style="text-align: center;">
                                        <span style="font-weight: 800; color: #10504B; font-size: 15px;">{{ $data['promedio'] }}</span>
                                    </td>
                                    <td style="text-align: center;">
                                        @php 
                                            $riesgoLabel = $data['riesgo'];
                                            $riesgoColor = \App\Models\Alumno::getRiesgoColor($data['promedio']);
                                        @endphp
                                        <span style="background: {{ $riesgoColor }}15; color: {{ $riesgoColor }}; padding: 6px 12px; border-radius: 8px; font-size: 12px; font-weight: 700; border: 1px solid {{ $riesgoColor }}30;">
                                            @if($riesgoLabel == 'Riesgo Extremo' || $riesgoLabel == 'Riesgo Medio')
                                                <i class="fa-solid fa-triangle-exclamation"></i>
                                            @else
                                                <i class="fa-solid fa-circle-check"></i>
                                            @endif
                                            {{ $riesgoLabel }}
                                        </span>
                                    </td>
                                    <td style="text-align: center;">
                                        <a href="{{ route('alumno.pdf.desempeno.periodo', ['id' => $alumno->idAlumnos, 'periodo' => $ciclo]) }}" target="_blank" class="btn-download-premium" style="padding: 8px 15px; font-size: 12px; border-radius: 10px; gap: 6px;">
                                            <i class="fa-solid fa-file-arrow-down" style="font-size: 14px;"></i> {{ __('Descargar PDF') }}
                                        </a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" style="text-align: center; padding: 40px; color: #94a3b8;">
                                        <i class="fa-solid fa-inbox" style="font-size: 30px; margin-bottom: 10px; display: block; opacity: 0.5;"></i>
                                        {{ __('No hay registros académicos suficientes para generar la bitácora.') }}
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
        </main>
    </div>
</body>
</html>
