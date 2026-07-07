<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Resumen Académico Integral - {{ $alumno->Nombre }} {{ $alumno->Apellido }}</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 30px;
            color: #333;
            background-color: #fff;
            line-height: 1.4;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 3px solid #10504B;
            padding-bottom: 15px;
            margin-bottom: 30px;
        }

        .university-info h1 {
            color: #10504B;
            margin: 0;
            font-size: 22px;
            text-transform: uppercase;
        }

        .university-info p {
            margin: 5px 0 0;
            font-size: 13px;
            color: #666;
        }

        .logo-container img {
            max-height: 70px;
        }

        .document-title {
            text-align: center;
            margin-bottom: 25px;
        }

        .document-title h2 {
            border-bottom: 1px solid #ddd;
            display: inline-block;
            padding: 0 40px 5px;
            font-size: 18px;
            color: #2b7a78;
        }

        .student-box {
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 6px;
            padding: 15px;
            margin-bottom: 25px;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
        }

        .info-item {
            margin-bottom: 5px;
        }

        .info-item label {
            font-weight: bold;
            font-size: 11px;
            color: #10504B;
            text-transform: uppercase;
            display: block;
        }

        .info-item span {
            font-size: 13px;
        }

        section {
            margin-bottom: 25px;
        }

        h3 {
            background-color: #10504B;
            color: white;
            padding: 6px 12px;
            font-size: 14px;
            border-radius: 4px;
            margin-bottom: 10px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 11px;
        }

        th {
            background-color: #f2f2f2;
            text-align: left;
            padding: 8px;
            border: 1px solid #ddd;
            color: #10504B;
        }

        td {
            padding: 8px;
            border: 1px solid #ddd;
        }

        .signature-section {
            margin-top: 60px;
            display: flex;
            justify-content: space-around;
            text-align: center;
        }

        .signature-box {
            width: 200px;
            border-top: 1px solid #333;
            padding-top: 8px;
        }

        .signature-box p {
            margin: 3px 0;
            font-size: 11px;
            font-weight: bold;
        }

        .footer {
            margin-top: 40px;
            text-align: center;
            font-size: 9px;
            color: #999;
            border-top: 1px solid #eee;
            padding-top: 15px;
        }

        @media print {
            body { padding: 0; }
            .no-print { display: none; }
            @page { size: portrait; margin: 1.5cm; }
        }

        .btn-print {
            position: fixed;
            bottom: 20px;
            right: 20px;
            background-color: #10504B;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 30px;
            cursor: pointer;
            font-size: 14px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            z-index: 1000;
        }
    </style>
</head>
<body onload="setupPrint()">

    <button class="btn-print no-print" onclick="window.print()">
        <i class="fa-solid fa-print"></i> {{ __('Imprimir / Guardar como PDF') }}
    </button>

    <script>
        function setupPrint() {
            // Un pequeño buffer de tiempo asegura que los estilos y fuentes se apliquen
            setTimeout(function() {
                window.print();
            }, 800);
        }

        // Si el usuario cierra o termina el diálogo de impresión, no cerramos la ventana
        // automáticamente para permitir reintentos manuales si es necesario.
    </script>

    <div class="header">
        <div class="university-info">
            <h1>Universidad Tecnológica de Nayarit</h1>
            <p>Dirección de Servicios Escolares y Tutorías</p>
        </div>
        <div class="logo-container">
            <img src="{{ asset('imgs/utn.png') }}" alt="Logo UT">
        </div>
    </div>

    <div class="document-title">
        <h2>RESUMEN ACADÉMICO INTEGRAL</h2>
    </div>

    <div class="student-box">
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
            <label>Cuatrimestre</label>
            <span>{{ $alumno->Cuatrimestre }}°</span>
        </div>
        <div class="info-item">
            <label>Tutor</label>
            <span>{{ optional($alumno->tutor)->Nombre ?? 'N/A' }} {{ optional($alumno->tutor)->Apellido ?? '' }}</span>
        </div>
    </div>

    <section>
        <h3>CALIFICACIONES Y DESEMPEÑO</h3>
        <table>
            <thead>
                <tr>
                    <th>Materia</th>
                    <th>Ciclo</th>
                    <th>Calificación</th>
                    <th>Estatus</th>
                </tr>
            </thead>
            <tbody>
                @forelse($alumno->historialAcademico as $historial)
                <tr>
                    <td>{{ $historial->Materia }}</td>
                    <td>{{ $historial->Ciclo }}</td>
                    <td style="font-weight: bold; text-align: center;">{{ $historial->Calificacion }}</td>
                    <td>{{ $historial->Calificacion >= 8 ? 'Aprobado' : ($historial->Calificacion >= 7 ? 'Regular' : 'No Aprobado') }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" style="text-align: center;">Sin registros de calificaciones.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
        @if($alumno->promedio > 0)
            <p style="text-align: right; font-weight: bold; margin-top: 5px;">Promedio General: {{ $alumno->promedio }}</p>
        @endif
    </section>

    @if(!$alumno->citasTutoria->isEmpty())
    <section>
        <h3>SEGUIMIENTO DE TUTORÍA</h3>
        <table>
            <thead>
                <tr>
                    <th>Fecha</th>
                    <th>Motivo / Asunto</th>
                    <th>Área</th>
                </tr>
            </thead>
            <tbody>
                @foreach($alumno->citasTutoria as $cita)
                <tr>
                    <td>{{ \Carbon\Carbon::parse($cita->Fecha)->format('d/m/Y') }}</td>
                    <td>{{ $cita->Motivo }}</td>
                    <td>Tutoría Individual</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </section>
    @endif

    @if(!$alumno->citasPsicologia->isEmpty())
    <section>
        <h3>ATENCIÓN PSICOLÓGICA</h3>
        <table>
            <thead>
                <tr>
                    <th>Fecha</th>
                    <th>Referencia / Tema</th>
                    <th>Estatus</th>
                </tr>
            </thead>
            <tbody>
                @foreach($alumno->citasPsicologia as $cita)
                <tr>
                    <td>{{ \Carbon\Carbon::parse($cita->Fecha)->format('d/m/Y') }}</td>
                    <td>Canalización de Tutoría</td>
                    <td>{{ $cita->Asistencia ?: 'Registrada' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </section>
    @endif

    @if(!$alumno->asesorias->isEmpty())
    <section>
        <h3>ASESORÍAS ACADÉMICAS</h3>
        <table>
            <thead>
                <tr>
                    <th>Fecha</th>
                    <th>Materia / Tema</th>
                    <th>Motivo</th>
                </tr>
            </thead>
            <tbody>
                @foreach($alumno->asesorias as $asesoria)
                <tr>
                    <td>{{ \Carbon\Carbon::parse($asesoria->Fecha)->format('d/m/Y') }}</td>
                    <td>{{ $asesoria->Materia ?: 'N/A' }}</td>
                    <td>{{ $asesoria->Motivo }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </section>
    @endif

    <div class="signature-section">
        <div class="signature-box">
            <p>___________________________</p>
            <p>{{ optional($alumno->tutor)->Nombre ?? 'TUTOR' }} {{ optional($alumno->tutor)->Apellido ?? '' }}</p>
            <p>FIRMA DEL TUTOR</p>
        </div>
        <div class="signature-box">
            <p>___________________________</p>
            <p>{{ $alumno->Nombre }} {{ $alumno->Apellido }}</p>
            <p>FIRMA DEL ALUMNO</p>
        </div>
    </div>

    <div class="footer">
        <p>Documento generado digitalmente por el Sistema de Expediente de Alumnos UTN.</p>
        <p>Fecha de emisión: {{ date('d/m/Y H:i') }}</p>
    </div>

</body>
</html>
