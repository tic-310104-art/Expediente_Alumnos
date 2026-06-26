<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('Mis Citas de Tutoría') }} | {{ __('Expediente del Alumno') }}</title>
    <link rel="stylesheet" href="{{ asset('expedienteG.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="shortcut icon" href="{{ asset('logo-utn.ico') }}" type="image/x-icon">
    <style>
        .btn-row-pdf{
            display:inline-flex;align-items:center;gap:8px;
            padding:7px 10px;border-radius:10px;text-decoration:none;
            background: linear-gradient(135deg,#0ea5e9,#0369a1);
            color:#fff;border:1px solid rgba(255,255,255,0.2);
            box-shadow:0 4px 12px rgba(3,105,161,.18);
            font-weight:800;font-size:12px;
        }
        .btn-row-pdf:hover{ transform: translateY(-1px); box-shadow:0 6px 14px rgba(3,105,161,.28); }
    </style>
</head>
<body>

    <div class="dashboard-container">
        @include('partials.sidebar', ['active' => 'citas'])

        <main class="main-content">
            <header class="student-header">
                <div class="student-profile">
                    @php
                        $alumnoFoto = $alumno->foto_url ?? "https://ui-avatars.com/api/?name=" . urlencode($alumno->Nombre . '+' . $alumno->Apellido) . "&background=10504B&color=fff&size=100";
                    @endphp
                    <img src="{{ $alumnoFoto }}" alt="Foto del alumno" class="profile-img">
                    <div class="student-info">
                        <h1>{{ __('Mis Citas de Tutoría') }}</h1>
                        <p class="student-id"><i class="fa-solid fa-id-card"></i> {{ __('Matrícula') }}: <strong>{{ $alumno->Matricula }}</strong></p>
                        <p style="margin-top:5px; font-size:0.9em; color:#ddd;"><i class="fa-solid fa-chalkboard-user"></i> {{ __('Tutor') }}: {{ $alumno->tutor ? $alumno->tutor->Nombre . ' ' . $alumno->tutor->Apellido : __('Sin Asignar') }}</p>
                    </div>
                </div>
            </header>

            <div class="dashboard-grid">
                <div class="card full-width">
                    <h3><i class="fa-solid fa-calendar-days"></i> {{ __('Próximas Citas y Seguimiento') }}</h3>
                    <div class="table-responsive">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>{{ __('Fecha y Hora') }}</th>
                                    <th>{{ __('Tutor') }}</th>
                                    <th>{{ __('Motivo de la Cita') }}</th>
                                    <th>{{ __('Estatus') }}</th>
                                    <th>{{ __('PDF') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($alumno->citasTutoria as $cita)
                                <tr>
                                    <td><strong>{{ \Carbon\Carbon::parse($cita->Fecha)->format('d/m/Y h:i A') }}</strong></td>
                                    <td>{{ $cita->tutor ? $cita->tutor->Nombre . ' ' . $cita->tutor->Apellido : __('No asignado') }}</td>
                                    <td>{{ $cita->Motivo }}</td>
                                    <td>
                                        @if(\Carbon\Carbon::parse($cita->Fecha)->isPast())
                                            <span class="badge" style="background-color: #6b7280; color: white; padding: 4px 8px; border-radius: 4px; font-size: 0.8em;">{{ __('Realizada') }}</span>
                                        @else
                                            <span class="badge" style="background-color: #059669; color: white; padding: 4px 8px; border-radius: 4px; font-size: 0.8em;">{{ __('Programada') }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a class="btn-row-pdf" href="{{ route('alumno.pdf.citas_tutoria.item', [$alumno->idAlumnos, $cita->idCitas]) }}" target="_blank">
                                            <i class="fa-solid fa-file-arrow-down"></i> PDF
                                        </a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" style="text-align: center;">{{ __('No tienes citas de tutorías agendadas por el momento.') }}</td>
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
