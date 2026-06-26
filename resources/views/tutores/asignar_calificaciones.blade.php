<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('Asignar Calificaciones') }} | UniAdmin</title>
    <link rel="stylesheet" href="{{ asset('expedienteG.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="shortcut icon" href="{{ asset('logo-utn.ico') }}" type="image/x-icon">
</head>
<body>
    <div class="dashboard-container">
        @include('partials.sidebar', ['active' => 'alumnos'])

        <main class="main-content">
            <header class="student-header">
                <div class="student-profile">
                    <img src="https://ui-avatars.com/api/?name={{ urlencode($alumno->Nombre . '+' . $alumno->Apellido) }}&background=10504B&color=fff&size=100" alt="Foto del alumno" class="profile-img">
                    <div class="student-info">
                        <h1>{{ __('Asignar Calificaciones') }}</h1>
                        <p class="student-id"><i class="fa-solid fa-user-graduate"></i> {{ $alumno->Nombre }} {{ $alumno->Apellido }}</p>
                        <p style="font-size: 0.9em; opacity: 0.8;"><i class="fa-solid fa-id-card"></i> {{ __('Matrícula') }}: {{ $alumno->Matricula }}</p>
                    </div>
                </div>
            </header>

            @if(session('success'))
                <div class="alert alert-success" style="background: #dcfce7; color: #166534; padding: 15px; border-radius: 8px; margin-bottom: 20px; border: 1px solid #bbf7d0;">
                    <i class="fa-solid fa-circle-check"></i> {{ session('success') }}
                </div>
            @endif

            <div class="dashboard-grid">
                <div class="card full-width">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                        <h3 style="margin: 0;"><i class="fa-solid fa-list-check"></i> {{ __('Carga Académica del Grupo') }}: {{ $alumno->grupo->Grupo ?? __('Sin Grupo') }}</h3>
                        <a href="{{ route('tutor.alumnos', Auth::user()->tutor->idTutores) }}" class="btn-secondary" style="text-decoration: none; font-size: 14px;">
                            <i class="fa-solid fa-arrow-left"></i> {{ __('Volver') }}
                        </a>
                    </div>

                    <form action="{{ route('tutor.alumnos.calificaciones.guardar', ['id' => Auth::user()->tutor->idTutores, 'alumnoId' => $alumno->idAlumnos]) }}" method="POST">
                        @csrf
                        <div style="background: #f8fafc; padding: 20px; border-radius: 12px; margin-bottom: 20px; border: 1px solid #e2e8f0;">
                            <div style="display: flex; align-items: center; gap: 15px;">
                                <div style="background: #10504B; color: white; width: 40px; height: 40px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 18px;">
                                    <i class="fa-solid fa-calendar-day"></i>
                                </div>
                                <div style="flex: 1;">
                                    <label style="display: block; font-size: 13px; font-weight: 700; color: #64748b; margin-bottom: 5px;">{{ __('Periodo Escolar') }}</label>
                                    <input type="text" name="Periodo" class="form-control" placeholder="Ej. SEP-DIC 2026" required style="max-width: 300px;">
                                </div>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="data-table">
                                <thead>
                                    <tr>
                                        <th>{{ __('Materia') }}</th>
                                        <th>{{ __('Docente') }}</th>
                                        <th style="text-align: center; width: 150px;">{{ __('Calificación') }}</th>
                                        <th>{{ __('Horario') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($cargaAcademica as $carga)
                                        <tr>
                                            <td>
                                                <div style="font-weight: 700; color: #1e293b;">{{ $carga->materia->Nombre }}</div>
                                            </td>
                                            <td style="color: #64748b;">{{ $carga->Maestro ?? __('Sin Asignar') }}</td>
                                            <td>
                                                <input type="number" step="0.1" min="0" max="10" 
                                                       name="calificaciones[{{ $carga->id }}]" 
                                                       class="form-control" 
                                                       style="text-align: center; font-weight: 700; color: #10504B;"
                                                       placeholder="0.0">
                                            </td>
                                            <td style="font-size: 12px; color: #94a3b8;">{{ $carga->Horario ?? __('Sin Horario') }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" style="text-align: center; padding: 40px; color: #94a3b8;">
                                                <i class="fa-solid fa-triangle-exclamation" style="font-size: 30px; margin-bottom: 10px; display: block; opacity: 0.5;"></i>
                                                {{ __('No hay carga académica asignada a este grupo.') }}
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        @if($cargaAcademica->count() > 0)
                            <div style="margin-top: 30px; text-align: right;">
                                <button type="submit" class="btn-primary" style="padding: 12px 30px; font-size: 16px; border-radius: 10px; background: #10504B;">
                                    <i class="fa-solid fa-floppy-disk"></i> {{ __('Guardar Calificaciones') }}
                                </button>
                            </div>
                        @endif
                    </form>
                </div>
            </div>
        </main>
    </div>
</body>
</html>
