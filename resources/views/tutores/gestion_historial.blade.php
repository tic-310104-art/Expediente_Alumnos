<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Historial Académico | {{ $alumno->Nombre }}</title>
    <link rel="stylesheet" href="{{ asset('expedienteG.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="shortcut icon" href="{{ asset('logo-utn.ico') }}" type="image/x-icon">
    <style>
        .day-selector { display: flex; gap: 8px; margin-top: 5px; }
        .day-btn { 
            width: 35px; height: 35px; border-radius: 50%; border: 1px solid #ccc; 
            background: white; cursor: pointer; display: flex; align-items: center; 
            justify-content: center; font-weight: bold; transition: all 0.3s;
        }
        .day-btn.active { background: #2b7a78; color: white; border-color: #2b7a78; }
        .time-range { display: flex; align-items: center; gap: 10px; margin-top: 5px; }
        .period-selector { display: flex; gap: 10px; }
    </style>
</head>
<body>

    <div class="dashboard-container">
        @include('partials.sidebar', ['active' => 'alumnos'])

        <main class="main-content">
            <header class="student-header">
                <div class="student-profile">
                    <img src="https://ui-avatars.com/api/?name={{ urlencode($alumno->Nombre . '+' . $alumno->Apellido) }}&background=10504B&color=fff&size=100" alt="Foto del alumno" class="profile-img">
                    <div class="student-info">
                        <h1>Calificaciones: {{ $alumno->Nombre }} {{ $alumno->Apellido }}</h1>
                        <p class="student-id"><i class="fa-solid fa-graduation-cap"></i> Carrera: <strong>{{ $alumno->carreras->first()->Nombre ?? 'Sin Carrera' }}</strong></p>
                    </div>
                </div>
                <div class="student-status" style="background: #10504B; color: white;">
                    Promedio General: <span>{{ number_format($alumno->historialAcademico->avg('Calificacion'), 1) }}</span>
                </div>
            </header>

            @if(session('success'))
                <div class="alert alert-success" style="background-color: #d4edda; color: #155724; padding: 15px; border-radius: 8px; margin-bottom: 20px; border: 1px solid #c3e6cb;">
                    <i class="fa-solid fa-check-circle"></i> {{ session('success') }}
                </div>
            @endif

            <div class="dashboard-grid">
                <!-- Historial Agrupado -->
                <div class="card full-width">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                        <h3 style="margin: 0;"><i class="fa-solid fa-file-invoice"></i> Historial Académico Detallado</h3>
                        <a href="{{ route('tutor.alumnos.calificaciones', ['id' => $tutor->idTutores, 'alumnoId' => $alumno->idAlumnos]) }}" class="btn-primary" style="background: #10504B; text-decoration: none; font-size: 14px; padding: 8px 15px; border-radius: 8px;">
                            <i class="fa-solid fa-plus"></i> Asignar Calificaciones por Carga
                        </a>
                    </div>
                    
                    @php
                        // Agrupamos el historial por cuatrimestre usando la relación con materia
                        $historialGrouped = $alumno->historialAcademico->groupBy(function($item) {
                            return $item->materia ? $item->materia->Cuatrimestre : 'Sin Cuatrimestre';
                        })->sortKeys();
                    @endphp

                    @forelse($historialGrouped as $cuatri => $registros)
                        <div class="cuatrimestre-section" style="margin-bottom: 25px;">
                            <h4 style="border-bottom: 2px solid #2b7a78; padding-bottom: 5px; color: #10504B;">
                                {{ is_numeric($cuatri) ? $cuatri . '° Cuatrimestre' : $cuatri }}
                            </h4>
                            <div class="table-responsive">
                                <table class="data-table">
                                    <thead>
                                        <tr>
                                            <th>Materia</th>
                                            <th>Profesor</th>
                                            <th>Horario</th>
                                            <th>Ciclo</th>
                                            <th>Calif.</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($registros as $reg)
                                        <tr>
                                            <td><strong>{{ $reg->materia->Nombre ?? 'Materia Eliminada' }}</strong></td>
                                            <td>{{ $reg->Profesor }}</td>
                                            <td><small>{{ $reg->Horario }}</small></td>
                                            <td>{{ $reg->Ciclo }}</td>
                                            <td>
                                                <span class="badge {{ $reg->Calificacion >= 8 ? 'badge-success' : ($reg->Calificacion >= 7 ? 'badge-warning' : 'badge-danger') }}">
                                                    {{ number_format($reg->Calificacion, 1) }}
                                                </span>
                                            </td>
                                            <td>
                                                <form action="{{ route('historial.destroy', $reg->idHistorial) }}" method="POST" onsubmit="return confirm('¿Seguro que deseas eliminar esta calificación del historial?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" style="background:none; border:none; color:#991b1b; cursor:pointer;" title="Eliminar">
                                                        <i class="fa-solid fa-trash-can"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @empty
                        <div style="text-align: center; padding: 40px; color: #666;">
                            <i class="fa-solid fa-folder-open" style="font-size: 3rem; margin-bottom: 10px; display: block;"></i>
                            <p>El alumno no tiene calificaciones registradas todavía.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </main>
    </div>
</body>
</html>
