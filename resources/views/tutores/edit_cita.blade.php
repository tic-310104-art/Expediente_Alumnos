<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reprogramar Cita | Panel de Tutoría</title>
    <link rel="stylesheet" href="{{ asset('expedienteG.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="shortcut icon" href="{{ asset('logo-utn.ico') }}" type="image/x-icon">
</head>
<body>

    <div class="dashboard-container">
        @include('partials.sidebar', ['active' => 'citas'])

        <main class="main-content">
            <header class="student-header">
                <div class="student-profile">
                    <div class="student-info">
                        <h1>Reprogramar Cita Académica</h1>
                        <p class="student-id"><i class="fa-solid fa-calendar-alt"></i> Gestión de Tutorías</p>
                    </div>
                </div>
            </header>

            <div class="dashboard-grid">
                <div class="card full-width">
                    <h3><i class="fa-solid fa-pen-to-square"></i> Editar Detalles de la Cita</h3>
                    
                    <form action="{{ route('citas-tutoria.update', $cita->idCitas) }}" method="POST">
                        @csrf
                        @method('PATCH')

                        <div class="form-grid">
                            <div class="form-group">
                                <label>Alumno Citado</label>
                                <select name="Alumnos_id" class="form-control" required>
                                    @foreach($tutor->alumnos as $alumno)
                                        <option value="{{ $alumno->idAlumnos }}" 
                                            {{ $cita->Alumnos_id == $alumno->idAlumnos ? 'selected' : '' }}>
                                            {{ $alumno->Matricula }} - {{ $alumno->Nombre }} {{ $alumno->Apellido }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group">
                                <label>Fecha y Hora</label>
                                <input type="datetime-local" name="Fecha" class="form-control" 
                                    value="{{ \Carbon\Carbon::parse($cita->Fecha)->format('Y-m-d\TH:i') }}" required>
                            </div>

                            <div class="form-group" style="grid-column: 1 / -1;">
                                <label>Motivo de la Cita</label>
                                <input type="text" name="Motivo" class="form-control" value="{{ $cita->Motivo }}" required>
                            </div>

                            <div class="form-actions" style="grid-column: 1 / -1;">
                                <a href="{{ route('tutor.citas', $tutor->idTutores) }}" class="btn-secondary">Cancelar</a>
                                <button type="submit" class="btn-primary" style="width: auto;">
                                    <i class="fa-solid fa-save"></i> Guardar Cambios
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </main>
    </div>
</body>
</html>
