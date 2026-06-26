<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte de Desempeño - {{ $periodo }}</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; padding: 18px; color: #111827; background: #f8fafc; }
        .no-print { display: block; }
        @media print { .no-print { display: none; } @page { size: portrait; margin: 1cm; } body { padding: 0; background: #fff; } }

        .page { background: #ffffff; border: 1px solid #e5e7eb; border-radius: 14px; padding: 20px; min-height: 88vh; display: flex; flex-direction: column; }
        @media print { .page { border: none; border-radius: 0; padding: 0; min-height: 0; } }

        .header { display:flex; justify-content: space-between; align-items: center; border-bottom: 3px solid #10504B; padding-bottom: 10px; margin-bottom: 14px; }
        .university-info h1 { margin:0; font-size: 18px; color:#10504B; }
        .university-info p { margin:2px 0 0 0; font-size: 12px; color:#374151; }
        .logo-container img { height: 48px; }

        .document-title { text-align:center; margin: 15px 0; }
        .document-title h2 { margin:0; font-size: 16px; letter-spacing: 0.08em; color: #10504B; }
        .document-title p { margin: 5px 0 0 0; font-size: 14px; font-weight: bold; color: #374151; }

        .info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 20px; }
        .info-item { border: 1px solid #e5e7eb; border-radius: 10px; padding: 10px; background: #f9fafb; }
        .info-item label { display: block; font-size: 10px; color: #6b7280; text-transform: uppercase; font-weight: 600; margin-bottom: 3px; }
        .info-item span { font-weight: 700; font-size: 13px; color: #111827; }

        table { width: 100%; border-collapse: collapse; margin-top: 10px; border-radius: 8px; overflow: hidden; }
        th, td { border: 1px solid #e5e7eb; padding: 10px; font-size: 12px; }
        th { background: #10504B; color: #ffffff; text-align: left; font-weight: 600; }
        tbody tr:nth-child(even) { background: #f9fafb; }

        .status-badge { padding: 4px 8px; border-radius: 6px; font-weight: 700; font-size: 11px; }
        .status-excelente { background: #ecfdf5; color: #059669; border: 1px solid #05966930; }
        .status-bien { background: #f0fdf4; color: #166534; border: 1px solid #16653430; }
        .status-riesgo-medio { background: #fef3c7; color: #92400e; border: 1px solid #92400e30; }
        .status-riesgo-extremo { background: #fee2e2; color: #991b1b; border: 1px solid #991b1b30; }

        .footer-sign { margin-top: auto; padding-top: 50px; display: flex; justify-content: space-around; }
        .sign-box { text-align: center; }
        .sign-line { border-top: 1px solid #111827; margin-top: 50px; padding-top: 6px; font-size: 11px; width: 220px; }

        .btn-print { position: fixed; right: 20px; bottom: 20px; background: #10504B; color: #fff; border: none; border-radius: 50px; padding: 12px 24px; cursor: pointer; font-weight: 700; box-shadow: 0 4px 12px rgba(0,0,0,0.2); }
    </style>
</head>
<body onload="setTimeout(() => window.print(), 500)">

    <button class="btn-print no-print" onclick="window.print()">Imprimir Reporte</button>

    <div class="page">
        <div class="header">
            <div class="university-info">
                <h1>Universidad Tecnológica de Nayarit</h1>
                <p>Bitácora de Seguimiento Académico</p>
            </div>
            <div class="logo-container">
                <img src="{{ asset('imgs/utn.png') }}" alt="Logo UT">
            </div>
        </div>

        <div class="document-title">
            <h2>REPORTE DE DESEMPEÑO POR PERIODO</h2>
            <p>{{ $periodo }}</p>
        </div>

        @php
            $promedioPeriodo = $historialFiltrado->avg('Calificacion');
            $riesgo = \App\Models\Alumno::getRiesgoStatus($promedioPeriodo);
            $slugRiesgo = str_replace(' ', '-', strtolower($riesgo));
        @endphp

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
                <label>Carrera</label>
                <span>{{ $alumno->carreras->first()->Nombre ?? 'N/A' }}</span>
            </div>
            <div class="info-item">
                <label>Grupo</label>
                <span>{{ $alumno->grupo->Grupo ?? 'N/A' }}</span>
            </div>
            <div class="info-item">
                <label>Promedio del Periodo</label>
                <span style="color: #10504B; font-size: 16px;">{{ number_format($promedioPeriodo, 1) }}</span>
            </div>
            <div class="info-item">
                <label>Nivel de Riesgo</label>
                <span class="status-badge status-{{ $slugRiesgo }}">{{ strtoupper($riesgo) }}</span>
            </div>
        </div>

        <section>
            <h3 style="margin: 0 0 10px 0; font-size: 14px; color: #10504B; border-left: 4px solid #10504B; padding-left: 8px;">Detalle de Materias</h3>
            <table>
                <thead>
                    <tr>
                        <th>Asignatura</th>
                        <th>Docente</th>
                        <th style="text-align: center;">Calificación</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($historialFiltrado as $h)
                        <tr>
                            <td><strong>{{ $h->Materia }}</strong></td>
                            <td>{{ $h->Profesor }}</td>
                            <td style="text-align: center; font-weight: 700;">{{ $h->Calificacion }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </section>

        <div class="footer-sign">
            <div class="sign-box">
                <div class="sign-line">Firma del Alumno</div>
            </div>
            <div class="sign-box">
                <div class="sign-line">Firma del Tutor Académico</div>
            </div>
        </div>
        
        <div style="margin-top: 30px; font-size: 10px; color: #94a3b8; text-align: center;">
            Este documento es un reporte informativo generado el {{ date('d/m/Y H:i') }}
        </div>
    </div>
</body>
</html>
