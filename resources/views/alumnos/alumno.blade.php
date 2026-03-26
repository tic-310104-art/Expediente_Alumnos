

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Expediente del Alumno</title>
    <link rel="stylesheet" href="expedienteG.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="shortcut icon" href="logo-utn.ico" type="image/x-icon">
</head>
<body>

    <div class="dashboard-container">
        
        <aside class="sidebar">
            <div class="sidebar-logo">
                <img src="{{ asset('imgs/utn.png') }}" alt="Logo Institucional" class="custom-sidebar-logo">
                <span>UniAdmin</span>
            </div>
            
            <nav class="sidebar-nav">
                <a href="/expedienteGeneral" class="nav-item "><i class="fa-solid fa-calendar-days"></i> Resumen General</a>
               <!-- <a href="#" class="nav-item"><i class="fa-solid fa-book"></i> Historial Académico</a> -->
                <a href="/tutor" class="nav-item"><i class="fa-solid fa-user"></i> Tutor</a>
                <a href="/alumno" class="nav-item"><i class="fa-solid fa-user"></i> Alumno</a>
                
            </nav>
            <div class="sidebar-footer">
                <a href="{{ asset('/Registro') }}" class="nav-item"><i class="fa-solid fa-arrow-right-from-bracket"></i> Salir</a>
            </div>
        </aside>

       

        <main class="main-content">
            <header class="student-header">
                <div class="student-profile">
                    <img src="https://ui-avatars.com/api/?name=Ana+Gomez&background=10504B&color=fff&size=100" alt="Foto del alumno" class="profile-img">
                    <div class="student-info">
                        <h1>Gilberto Alonso Mendoza</h1>
                        <p class="student-id"><i class="fa-solid fa-id-card"></i> Matrícula: <strong>Tic-310104</strong></p>
                        <p class="student-career"><i class="fa-solid fa-laptop-code"></i> Ingeniería en Desarrollo de Software</p>
                    </div>
                </div>
                <div class="student-status active-status">
                    Estatus: <span>Inscrito (Regular)</span>
                </div>
            </header>

            <div class="dashboard-grid">
                
                <div class="card">
                    <h3><i class="fa-solid fa-address-card"></i> Información Personal</h3>
                    <ul class="info-list">
                        <li><strong>CURP:</strong> MESG0S0FTES456945</li>
                        <li><strong>Correo Inst:</strong> TIC-310104@UTNAY.EDU.MX</li>
                        <li><strong>Teléfono:</strong> +52 311 123 4567</li>
                        <li><strong>Cuatrimestre Actual:</strong> 8vo Cuatrimestre</li>
                    </ul>
                </div>

                <div class="card progress-card">
                    <h3><i class="fa-solid fa-chart-pie"></i> Progreso Académico</h3>
                    <div class="stats-container">
                        <div class="stat-box">
                            <span class="stat-value">9.4</span>
                            <span class="stat-label">Promedio General</span>
                        </div>
                    </div>
                </div>

                <div class="card full-width">
                    <h3><i class="fa-solid fa-list-check"></i> Carga Académica Actual (Ciclo 2026-1)</h3>
                    <div class="table-responsive">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Clave</th>
                                    <th>Materia</th>
                                    <th>Profesor</th>
                                    <th>Calificacion</th>
                                    <th>Horario</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>ISC-501</td>
                                    <td>Seguridad de Apps</td>
                                    <td>ing.Oscar Arenas</td>
                                    <td>8</td>
                                    <td>Lu/Mi 10:00 - 12:00</td>
                                </tr>
                                <tr>
                                    <td>ISC-502</td>
                                    <td>Bases de Datos</td>
                                    <td>ing.Tovar</td>
                                    <td>8</td>
                                    <td>Ma/Ju 08:00 - 10:00</td>
                                </tr>
                                <tr>
                                    <td>MAT-304</td>
                                    <td>Desarrollo Web Profesional </td>
                                    <td>ing.Fanny</td>
                                    <td>10</td>
                                    <td>Lu/Mi/Vi 07:00 - 08:00</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
            
        </main>
    </div>

</body>
</html>