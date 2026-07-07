<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Asesorías Académicas (PDF)</title>
    <link rel="shortcut icon" href="{{ asset('logo-utn.ico') }}" type="image/x-icon">
    <style>
        body { font-family: Arial, sans-serif; margin: 0; padding: 18px; color: #111827; background: #f8fafc; }
        .no-print { display: block; }
        @media print { .no-print { display: none; } @page { size: portrait; margin: 1cm; } body { padding: 0; background: #fff; } }

        .page { background: #ffffff; border: 1px solid #e5e7eb; border-radius: 14px; padding: 16px; min-height: 88vh; display: flex; flex-direction: column; }
        @media print { .page { border: none; border-radius: 0; padding: 0; min-height: 0; } }

        .header { display:flex; justify-content: space-between; align-items: center; border-bottom: 3px solid #10504B; padding-bottom: 10px; margin-bottom: 14px; }
        .university-info h1 { margin:0; font-size: 18px; color:#10504B; }
        .university-info p { margin:2px 0 0 0; font-size: 12px; color:#374151; }
        .logo-container img { height: 48px; }

        .document-title { text-align:center; margin: 10px 0 14px 0; }
        .document-title h2 { margin:0; font-size: 16px; letter-spacing: 0.08em; }

        .info-grid { display:grid; grid-template-columns: 1fr 1fr; gap: 10px; margin-bottom: 14px; }
        .info-item { border: 1px solid #e5e7eb; border-radius: 10px; padding: 10px; }
        .info-item label { display:block; font-size: 11px; color:#6b7280; margin-bottom: 4px; }
        .info-item span { font-weight: 700; font-size: 13px; }

        table { width: 100%; border-collapse: collapse; margin-top: 8px; }
        th, td { border: 1px solid #e5e7eb; padding: 8px; font-size: 12px; }
        th { background:#f8fafc; color:#111827; text-align: left; }
        tbody tr:nth-child(even) { background: #fcfcfd; }

        .footer-sign { margin-top: auto; padding-top: 60px; display:flex; justify-content: center; gap: 120px; }
        .sign-box { flex: 1; text-align: center; }
        .sign-line { border-top: 1px solid #111827; margin-top: 60px; padding-top: 6px; font-size: 12px; width: 260px; margin-left: auto; margin-right: auto; }
        .btn-print { position: fixed; right: 18px; bottom: 18px; background:#10504B; color:#fff; border:none; border-radius: 999px; padding: 12px 18px; cursor:pointer; font-weight:700; }
    </style>
</head>
<body onload="setTimeout(() => window.print(), 50)">

    <button class="btn-print no-print" onclick="window.print()">Imprimir / Guardar PDF</button>

    <div class="page">
        <div class="header">
            <div class="university-info">
                <h1>Universidad Tecnológica de Nayarit</h1>
                <p>Dirección de Servicios Escolares</p>
            </div>
            <div class="logo-container">
                <img src="{{ asset('imgs/utn.png') }}" alt="Logo UT">
            </div>
        </div>

        <div class="document-title">
            <h2>ASESORÍAS ACADÉMICAS</h2>
        </div>

    <div class="info-grid">
        <div class="info-item">
            <label>Alumno</label>
            <span>{{ $alumno->Nombre }} {{ $alumno->Apellido }}</span>
        </div>
        <div class="info-item">
            <label>Matrícula</label>
            <span>{{ $alumno->Matricula }}</span>
        </div>
        <div class="info-item">
            <label>Tutor</label>
            <span>{{ $alumno->tutor ? ($alumno->tutor->Nombre . ' ' . $alumno->tutor->Apellido) : 'Sin asignar' }}</span>
        </div>
        <div class="info-item">
            <label>Carrera</label>
            <span>{{ $alumno->carrera->Nombre ?? 'No asignada' }}</span>
        </div>
    </div>

    <section>
        <h3 style="margin:0; font-size: 13px; color:#10504B;">Historial</h3>
        <table>
            <thead>
                <tr>
                    <th>Fecha y hora</th>
                    <th>Motivo / Tema</th>
                    <th>Estatus</th>
                </tr>
            </thead>
            <tbody>
                @forelse($alumno->asesorias as $asesoria)
                    <tr>
                        <td>{{ \Carbon\Carbon::parse($asesoria->Fecha)->format('d/m/Y h:i A') }}</td>
                        <td>{{ $asesoria->Motivo }}</td>
                        <td>{{ \Carbon\Carbon::parse($asesoria->Fecha)->isPast() ? 'Finalizada' : 'Programada' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" style="text-align:center;">Sin registros</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </section>

        <div class="footer-sign">
            <div class="sign-box">
                <div class="sign-line">Firma del Tutor</div>
            </div>
            <div class="sign-box">
                <div class="sign-line">Firma del Alumno</div>
            </div>
        </div>
    </div>

    <script>
        window.addEventListener('afterprint', () => {
            try { window.close(); } catch (e) {}
        });
    </script>

</body>
</html>
