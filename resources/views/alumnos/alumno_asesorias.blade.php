<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('Mis Asesorías Académicas') }} | {{ __('Expediente del Alumno') }}</title>
    <link rel="stylesheet" href="{{ asset('expedienteG.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="shortcut icon" href="{{ asset('logo-utn.ico') }}" type="image/x-icon">
    <style>
        .btn-download{
            display:inline-flex;align-items:center;gap:10px;
            padding:10px 16px;border-radius:12px;text-decoration:none;
            background: linear-gradient(135deg,#0ea5e9,#0369a1);
            color:#fff;border:1px solid rgba(255,255,255,0.2);
            box-shadow:0 6px 16px rgba(3,105,161,.25);
            font-weight:700; letter-spacing:.02em;
            transition:transform .12s ease, box-shadow .12s ease, background .2s ease;
        }
        .btn-download:hover{ transform: translateY(-1px); box-shadow:0 8px 18px rgba(3,105,161,.35); }
        .btn-download i{ font-size:14px; }
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
        @include('partials.sidebar', ['active' => 'asesorias'])

        <main class="main-content">
            <header class="student-header">
                <div class="student-profile">
                    @php
                        $alumnoFoto = $alumno->foto_url ?? "https://ui-avatars.com/api/?name=" . urlencode($alumno->Nombre . '+' . $alumno->Apellido) . "&background=10504B&color=fff&size=100";
                    @endphp
                    <img src="{{ $alumnoFoto }}" alt="Foto del alumno" class="profile-img">
                    <div class="student-info">
                        <h1>{{ __('Mis Asesorías Académicas') }}</h1>
                        <p class="student-id"><i class="fa-solid fa-id-card"></i> {{ __('Matrícula') }}: <strong>{{ $alumno->Matricula }}</strong></p>
                    </div>
                </div>
            </header>

            <div class="dashboard-grid">
                <div class="card full-width">
                    <h3><i class="fa-solid fa-book-open-reader"></i> {{ __('Historial de Asesorías Solicitadas') }}</h3>
                    <div class="table-responsive">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>{{ __('Fecha y Hora') }}</th>
                                    <th>{{ __('Motivo / Tema de Asesoría') }}</th>
                                    <th>{{ __('Estatus') }}</th>
                                    <th>{{ __('PDF') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($alumno->asesorias as $asesoria)
                                <tr>
                                    <td><strong>{{ \Carbon\Carbon::parse($asesoria->Fecha)->format('d/m/Y h:i A') }}</strong></td>
                                    <td>{{ $asesoria->Motivo }}</td>
                                    <td>
                                        @if(\Carbon\Carbon::parse($asesoria->Fecha)->isPast())
                                            <span class="badge" style="background-color: #e5e7eb; color: #374151; padding: 4px 8px; border-radius: 4px; font-size: 0.8em; border: 1px solid #d1d5db;">{{ __('Finalizada') }}</span>
                                        @else
                                            <span class="badge" style="background-color: #dcfce7; color: #166534; padding: 4px 8px; border-radius: 4px; font-size: 0.8em; border: 1px solid #22c55e;">{{ __('Programada') }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a class="btn-row-pdf" href="{{ route('alumno.pdf.asesorias.item', [$alumno->idAlumnos, $asesoria->idAsesoria]) }}" target="_blank">
                                            <i class="fa-solid fa-file-arrow-down"></i> PDF
                                        </a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" style="text-align: center;">{{ __('No tienes registros de asesorías académicas programadas.') }}</td>
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
