<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Tutoría</title>
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
                <a href="/expedienteGeneral" class="nav-item "><i class="fa-solid fa-chart-pie"></i> Dashboard Global</a>
                <a href="#" class="nav-item "><i class="fa-solid fa-users"></i> Mis Alumnos</a>
                <a href="#" class="nav-item"><i class="fa-solid fa-calendar-check"></i> Citas de Tutoría</a>
                <a href="#" class="nav-item"><i class="fa-solid fa-chart-line"></i> Reportes de Desempeño</a>
            </nav>
            <div class="sidebar-footer">
                <a href="{{ asset('/Registro') }}" class="nav-item"><i class="fa-solid fa-arrow-right-from-bracket"></i> Salir</a>
            </div>
        </aside>

        <main class="main-content">
            <header class="student-header">
                <div class="student-profile">
                    <img src="https://ui-avatars.com/api/?name=Roberto+Ruiz&background=10504B&color=fff&size=100" alt="Foto del tutor" class="profile-img">
                    <div class="student-info">
                        <h1>Dr. Roberto Ruiz Sánchez</h1>
                        <p class="student-id"><i class="fa-solid fa-chalkboard-user"></i> Rol: <strong>Tutor Académico</strong></p>
                        <p class="student-career"><i class="fa-solid fa-building"></i> Departamento de Sistemas Computacionales</p>
                    </div>
                </div>
            </header>

            <div class="dashboard-grid">
                <div class="card progress-card">
                    <h3><i class="fa-solid fa-chart-pie"></i> Resumen de Tutorados</h3>
                    <div class="stats-container">
                        <div class="stat-box">
                            <span class="stat-value">24</span>
                            <span class="stat-label">Alumnos Asignados</span>
                        </div>
                        <div class="stat-box">
                            <span class="stat-value" style="color: #991b1b;">3</span>
                            <span class="stat-label">En Riesgo Académico</span>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <h3><i class="fa-solid fa-clock"></i> Próximas Tutorías</h3>
                    <ul class="info-list">
                        <li><strong>Hoy, 12:00 PM</strong> <span>Gilberto Mendoza</span></li>
                        <li><strong>Mañana, 09:00 AM</strong> <span>Ana Gómez</span></li>
                        <li><strong>Jueves, 11:30 AM</strong> <span>Carlos Slim</span></li>
                    </ul>
                </div>

                <div class="card full-width">
                    <h3><i class="fa-solid fa-list-ul"></i> Lista de Alumnos Asignados</h3>
                    <div class="table-responsive">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Matrícula</th>
                                    <th>Nombre del Alumno</th>
                                    <th>Cuatrimestre</th>
                                    <th>Promedio</th>
                                    <th>Estatus</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Tic-310104</td>
                                    <td>Gilberto Alonso Mendoza</td>
                                    <td>8vo</td>
                                    <td>9.4</td>
                                    <td><span class="badge badge-success">Regular</span></td>
                                </tr>
                                <tr>
                                    <td>Tic-310105</td>
                                    <td>María Fernanda López</td>
                                    <td>8vo</td>
                                    <td>6.8</td>
                                    <td><span class="badge badge-danger">En Riesgo</span></td>
                                </tr>
                                <tr>
                                    <td>Tic-310106</td>
                                    <td>Juan Pablo Torres</td>
                                    <td>8vo</td>
                                    <td>8.5</td>
                                    <td><span class="badge badge-success">Regular</span></td>
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