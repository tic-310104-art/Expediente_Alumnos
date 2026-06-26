<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Citas de Tutoría | Panel de Tutoría</title>
    <link rel="stylesheet" href="{{ asset('expedienteG.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="shortcut icon" href="{{ asset('logo-utn.ico') }}" type="image/x-icon">
</head>
<body>

    <div class="dashboard-container">
        @include('partials.sidebar', ['active' => 'none'])

        <main class="main-content">
            <header class="student-header">
                <div class="student-profile">
                    @php
                        $tutorFoto = $tutor->foto_url ?? "https://ui-avatars.com/api/?name=" . urlencode($tutor->Nombre . '+' . $tutor->Apellido) . "&background=10504B&color=fff&size=100";
                    @endphp
                    <img src="{{ $tutorFoto }}" alt="Foto del tutor" class="profile-img">
                    <div class="student-info">
                        <h1>Módulo: Citas de Tutoría</h1>
                        <p class="student-id"><i class="fa-solid fa-chalkboard-user"></i> Tutor: <strong>{{ $tutor->Nombre }} {{ $tutor->Apellido }}</strong></p>
                    </div>
                </div>
            </header>

            @if(session('success'))
                <div class="alert alert-success" style="background-color: #d4edda; color: #155724; padding: 10px; border-radius: 5px; margin-bottom: 20px;">
                    {{ session('success') }}
                </div>
            @endif

            <div class="dashboard-grid">
                
                <div class="card full-width">
                    <h3><i class="fa-solid fa-calendar-plus"></i> Agendar Nueva Cita</h3>
                    <form action="{{ route('citas-tutoria.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="Tutores_id" value="{{ $tutor->idTutores }}">
                        <div class="form-grid">
                            <div class="form-group">
                                <label>Alumno a Citar</label>
                                <select name="Alumnos_id" class="form-control" required>
                                    @if(isset($alumno_id))
                                        @foreach($tutor->alumnos as $alumno)
                                            @if($alumno->idAlumnos == $alumno_id)
                                                <option value="{{ $alumno->idAlumnos }}" selected>{{ $alumno->Matricula }} - {{ $alumno->Nombre }} {{ $alumno->Apellido }}</option>
                                            @endif
                                        @endforeach
                                    @else
                                        <option value="" disabled selected>Selecciona un alumno...</option>
                                        @forelse($tutor->alumnos as $alumno)
                                            <option value="{{ $alumno->idAlumnos }}">{{ $alumno->Matricula }} - {{ $alumno->Nombre }} {{ $alumno->Apellido }}</option>
                                        @empty
                                            <option value="" disabled>No tienes alumnos asignados</option>
                                        @endforelse
                                    @endif
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Fecha y Hora</label>
                                <input type="datetime-local" name="Fecha" class="form-control" required>
                            </div>
                            <div class="form-group" style="grid-column: 1 / -1;">
                                <label>Motivo de la Cita</label>
                                <input type="text" name="Motivo" class="form-control" placeholder="Ej. Revisión de desempeño académico..." required>
                            </div>
                            <div class="form-actions" style="grid-column: 1 / -1;">
                                <button type="submit" class="btn-primary" style="width: auto;"><i class="fa-solid fa-calendar-check"></i> Agendar Cita</button>
                            </div>
                        </div>
                    </form>
                </div>

                <div class="card full-width">
                    <h3><i class="fa-solid fa-calendar-days"></i> Historial y Próximas Citas Programadas</h3>
                    <div class="table-responsive">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Fecha y Hora</th>
                                    <th>Alumno</th>
                                    <th>Motivo de la Cita</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($citas as $cita)
                                <tr>
                                    <td><strong>{{ \Carbon\Carbon::parse($cita->Fecha)->format('d/m/Y h:i A') }}</strong></td>
                                    <td>{{ $cita->alumno ? $cita->alumno->Nombre . ' ' . $cita->alumno->Apellido : 'General' }}</td>
                                    <td>{{ $cita->Motivo }}</td>
                                    <td>
                                        <div class="action-buttons" style="display: flex; gap: 10px;">
                                            <a href="{{ route('citas-tutoria.edit', $cita->idCitas) }}" class="btn-primary" 
                                               style="padding:5px 12px; font-size:12px; text-decoration: none; border-radius: 4px; display: inline-flex; align-items: center; gap: 5px;">
                                               <i class="fa-solid fa-pen"></i> Reprogramar
                                            </a>
                                            
                                            <form action="{{ route('citas-tutoria.destroy', $cita->idCitas) }}" method="POST" 
                                                  onsubmit="return confirm('¿Estás seguro de que deseas eliminar esta cita?');" style="display:inline;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn-secondary" 
                                                        style="padding:5px 12px; font-size:12px; background-color: #991b1b; color: white; border-radius: 4px; display: inline-flex; align-items: center; gap: 5px; border: none; cursor: pointer;">
                                                   <i class="fa-solid fa-trash-can"></i> Eliminar
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="3" style="text-align: center;">No se encontraron citas de tutorías agendadas.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </main>
    </div>
</body>
</html>
