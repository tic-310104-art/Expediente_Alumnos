<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Expediente Académico - {{ $alumno->Nombre }} {{ $alumno->Apellido }}</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 30px;
            color: #333;
            background-color: #fff;
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
            font-size: 24px;
            text-transform: uppercase;
        }

        .university-info p {
            margin: 5px 0 0;
            font-size: 14px;
            color: #666;
        }

        .logo-container img {
            max-height: 80px;
        }

        .document-title {
            text-align: center;
            margin-bottom: 30px;
        }

        .document-title h2 {
            border-bottom: 1px solid #ddd;
            display: inline-block;
            padding: 0 50px 5px;
            font-size: 20px;
            color: #2b7a78;
        }

        .info-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 15px;
            margin-bottom: 40px;
            padding: 20px;
            background-color: #f9f9f9;
            border-radius: 8px;
            border: 1px solid #eee;
        }

        .info-item label {
            font-weight: bold;
            display: block;
            font-size: 11px;
            text-transform: uppercase;
            color: #10504B;
            margin-bottom: 3px;
        }

        .info-item span {
            font-size: 14px;
            color: #000;
        }

        section {
            margin-bottom: 35px;
        }

        h3 {
            background-color: #10504B;
            color: white;
            padding: 8px 15px;
            font-size: 16px;
            border-radius: 4px;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 12px;
        }

        th {
            background-color: #f2f2f2;
            text-align: left;
            padding: 10px;
            border: 1px solid #ddd;
            color: #10504B;
        }

        td {
            padding: 10px;
            border: 1px solid #ddd;
        }

        .badge {
            padding: 3px 8px;
            border-radius: 12px;
            font-size: 10px;
            font-weight: bold;
        }

        .footer {
            margin-top: 50px;
            text-align: center;
            font-size: 10px;
            color: #999;
            border-top: 1px solid #eee;
            padding-top: 20px;
        }

        @media print {
            body { padding: 0; }
            .no-print { display: none; }
            .page-break { page-break-before: always; }
            @page { size: portrait; margin: 1cm; }
        }

        .btn-print {
            position: fixed;
            bottom: 20px;
            right: 20px;
            background-color: #10504B;
            color: white;
            padding: 12px 24px;
            border: none;
            border-radius: 50px;
            cursor: pointer;
            font-size: 16px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            transition: all 0.3s;
        }

        .btn-print:hover {
            transform: scale(1.05);
            box-shadow: 0 6px 12px rgba(0,0,0,0.15);
        }
    </style>
</head>
<body onload="setupPrint()">

    <button class="btn-print no-print" onclick="window.print()">
        <i class="fa-solid fa-print"></i> Imprimir / Guardar PDF
    </button>

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
        <h2>EXPEDIENTE ACADÉMICO INTEGRAL</h2>
    </div>

    <div class="info-grid">
        <div class="info-item">
            <label>Nombre del Alumno</label>
            <span>{{ $alumno->Nombre }} {{ $alumno->Apellido }}</span>
        </div>
        <div class="info-item">
            <label>Matrícula / ID</label>
            <span>{{ $alumno->Matricula }}</span>
        </div>
        <div class="info-item">
            <label>Carrera / Programa Académico</label>
            <span>{{ $alumno->carreras->first()->Nombre ?? 'No asignada' }}</span>
        </div>
        <div class="info-item">
            <label>Grupo / Ciclo</label>
            <span>{{ $alumno->grupo->Grupo ?? 'Sin grupo' }} ({{ $alumno->grupo->Periodo ?? 'N/A' }})</span>
        </div>
        <div class="info-item">
            <label>Correo Institucional</label>
            <span>{{ $alumno->Correo_inst }}</span>
        </div>
        <div class="info-item">
            <label>Estatus Académico</label>
            <span>{{ $alumno->Rol }}</span>
        </div>
        <div class="info-item">
            <label>Tutor Asignado</label>
            <span>{{ optional($alumno->tutor)->Nombre ?? 'No asignado' }} {{ optional($alumno->tutor)->Apellido ?? '' }}</span>
        </div>
    </div>

    <section>
        <h3>CARGA ACADÉMICA Y CALIFICACIONES</h3>
        <table>
            <thead>
                <tr>
                    <th>Materia</th>
                    <th>Profesor</th>
                    <th>Horario</th>
                    <th>Ciclo</th>
                    <th>Calificación</th>
                </tr>
            </thead>
            <tbody>
                @forelse($alumno->historialAcademico as $historial)
                <tr>
                    <td>{{ $historial->Materia }}</td>
                    <td>{{ $historial->Profesor }}</td>
                    <td>{{ $historialDocumento = $historial->Horario }}</td>
                    <td>{{ $historial->Ciclo }}</td>
                    <td style="text-align: center; font-weight: bold;">{{ $historial->Calificacion }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" style="text-align: center;">Sin historial académico registrado.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </section>

    <section>
        <h3>HISTORIAL DE APOYO Y SEGUIMIENTO</h3>
        <p style="font-size: 11px; margin-bottom: 10px; color: #666;">Resumen de atenciones brindadas por tutoría, psicología y asesorías académicas.</p>
        <table>
            <thead>
                <tr>
                    <th>Área / Servicio</th>
                    <th>Estatus / Tema</th>
                    <th>Fecha de Registro</th>
                </tr>
            </thead>
            <tbody>
                @foreach($alumno->citasTutoria as $cita)
                <tr>
                    <td><strong>Tutoría Individual</strong></td>
                    <td>{{ $cita->Motivo }}</td>
                    <td>{{ \Carbon\Carbon::parse($cita->Fecha)->format('d/m/Y') }}</td>
                </tr>
                @endforeach
                
                @foreach($alumno->citasPsicologia as $cita)
                <tr>
                    <td><strong>Psicología</strong></td>
                    <td>Canalización / {{ $cita->Asistencia }}</td>
                    <td>{{ \Carbon\Carbon::parse($cita->Fecha)->format('d/m/Y') }}</td>
                </tr>
                @endforeach

                @foreach($alumno->asesorias as $asesoria)
                <tr>
                    <td><strong>Asesoría Académica</strong></td>
                    <td>{{ $asesoria->Motivo }}</td>
                    <td>{{ \Carbon\Carbon::parse($asesoria->Fecha)->format('d/m/Y') }}</td>
                </tr>
                @endforeach

                @if($alumno->citasTutoria->isEmpty() && $alumno->citasPsicologia->isEmpty() && $alumno->asesorias->isEmpty())
                <tr>
                    <td colspan="3" style="text-align: center;">No se registran intervenciones de apoyo.</td>
                </tr>
                @endif
            </tbody>
        </table>
    </section>

    <section>
        <h3>BECAS Y APOYOS ECONÓMICOS</h3>
        <table>
            <thead>
                <tr>
                    <th>Nombre de la Beca</th>
                    <th>Monto</th>
                    <th>Fecha de Asignación</th>
                    <th>Descripción</th>
                </tr>
            </thead>
            <tbody>
                @forelse($alumno->becas as $beca)
                <tr>
                    <td><strong>{{ $beca->Nombre }}</strong></td>
                    <td>${{ number_format($beca->Monto, 2) }}</td>
                    <td>{{ \Carbon\Carbon::parse($beca->pivot->Fecha_Asignacion)->format('d/m/Y') }}</td>
                    <td>{{ $beca->Descripcion }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" style="text-align: center;">No se registran becas asignadas a este alumno.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </section>

    <div class="footer">
        <p>Este documento es para fines informativos y de seguimiento interno. Emitido el {{ date('d/m/Y H:i') }}</p>
        <p>Expediente de Alumnos | UniAdmin Sistema de Seguimiento</p>
    </div>

    <script>
        function setupPrint() {
            // Buffer de tiempo asegurando carga de recursos
            setTimeout(function() {
                window.print();
            }, 1000);
        }
    </script>
</body>
</html>
