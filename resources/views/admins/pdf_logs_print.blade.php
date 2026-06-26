<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte de Logs de Actividad - UTN</title>
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

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 10px;
        }

        th {
            background-color: #f2f2f2;
            text-align: left;
            padding: 8px;
            border: 1px solid #ddd;
            color: #10504B;
            text-transform: uppercase;
        }

        td {
            padding: 8px;
            border: 1px solid #ddd;
            word-wrap: break-word;
            max-width: 200px;
        }

        .signature-section {
            margin-top: 80px;
            display: flex;
            justify-content: space-around;
            text-align: center;
        }

        .signature-box {
            width: 250px;
            border-top: 1px solid #333;
            padding-top: 10px;
        }

        .signature-box p {
            margin: 5px 0;
            font-size: 12px;
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

        .badge {
            padding: 2px 6px;
            border-radius: 4px;
            font-size: 8px;
            font-weight: bold;
            color: white;
            text-transform: uppercase;
        }
        .badge-alta { background-color: #15803d; }
        .badge-edit { background-color: #1d4ed8; }
        .badge-baja { background-color: #b91c1c; }
        .badge-other { background-color: #6b7280; }

        @media print {
            body { padding: 0; }
            .no-print { display: none; }
            @page { size: landscape; margin: 1cm; }
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
            z-index: 9999;
        }
    </style>
</head>
<body onload="setupPrint()">
    <script>
        function setupPrint() {
            setTimeout(function() {
                window.print();
            }, 1000);
        }
    </script>

    <button class="btn-print no-print" onclick="window.print()">
        <i class="fa-solid fa-download"></i> Descargar Reporte (PDF)
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
        <h2>REPORTE DE ACTIVIDAD DEL SISTEMA (LOGS)</h2>
        <p style="font-size: 12px; color: #666;">Generado el: {{ date('d/m/Y H:i') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Fecha/Hora</th>
                <th>Usuario</th>
                <th>Rol</th>
                <th>Acción</th>
                <th>IP</th>
                <th>URL / Método</th>
                <th>Detalles</th>
            </tr>
        </thead>
        <tbody>
            @foreach($logs as $log)
                @php
                    $method = strtoupper((string)$log->method);
                    $crud = $method === 'POST' ? 'alta' : (($method === 'PUT' || $method === 'PATCH') ? 'edit' : ($method === 'DELETE' ? 'baja' : 'other'));
                    $subjectLower = strtolower((string)$log->subject);
                    if (str_contains($subjectLower, 'baja') || str_contains($subjectLower, 'elimin')) $crud = 'baja';
                    if (str_contains($subjectLower, 'edición') || str_contains($subjectLower, 'edicion')) $crud = 'edit';
                    if (str_contains($subjectLower, 'alta') || str_contains($subjectLower, 'creación') || str_contains($subjectLower, 'creacion')) $crud = ($crud == 'other') ? 'alta' : $crud;
                @endphp
                <tr>
                    <td>{{ $log->created_at->format('d/m/Y H:i:s') }}</td>
                    <td>{{ $log->user ? $log->user->name : 'Sistema' }}</td>
                    <td>{{ $log->user ? strtoupper($log->user->role) : 'N/A' }}</td>
                    <td>
                        <span class="badge badge-{{ $crud }}">{{ $log->subject }}</span>
                    </td>
                    <td>{{ $log->ip }}</td>
                    <td>{{ $method }} {{ Str::limit($log->url, 50) }}</td>
                    <td>{{ preg_replace('/(password|Password|Contraseña|Password_confirmation|swal-token): [^|]+/i', '$1: ********', (string)$log->details) ?: 'N/A' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="signature-section">
        <div class="signature-box">
            <p>___________________________________</p>
            <p>FIRMA Y SELLO</p>
            <p>DIRECCIÓN DE SERVICIOS ESCOLARES</p>
        </div>
        <div class="signature-box">
            <p>___________________________________</p>
            <p>FIRMA Y SELLO</p>
            <p>RECTORÍA</p>
        </div>
    </div>

    <div class="footer">
        <p>Este reporte ha sido generado automáticamente por el sistema de control escolar de la UTN.</p>
        <p>Universidad Tecnológica de Nayarit | @ {{ date('Y') }}</p>
    </div>

</body>
</html>
