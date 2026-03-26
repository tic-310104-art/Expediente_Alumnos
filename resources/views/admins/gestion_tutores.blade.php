<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Tutores | UniAdmin</title>
    <link rel="stylesheet" href="{{ asset('expedienteG.css') }}">
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
                <a href="/expedienteGeneral" class="nav-item"><i class="fa-solid fa-chart-pie"></i> Dashboard Global</a>
                <a href="/gestion-alumnos" class="nav-item"><i class="fa-solid fa-user-graduate"></i> Gestión de Alumnos</a>
                <a href="/gestion-admins" class="nav-item"><i class="fa-solid fa-shield-halved"></i> Administradores</a>
            </nav>
            <div class="sidebar-footer">
                <a href="{{ asset('/Registro') }}" class="nav-item"><i class="fa-solid fa-arrow-right-from-bracket"></i> Salir</a>
            </div>
        </aside>

        <main class="main-content">
            <header class="student-header">
                <div class="student-profile">
                    <div class="student-info">
                        <h1>Gestión de Tutores</h1>
                        <p class="student-id">Control del personal académico</p>
                    </div>
                </div>
            </header>

            <div class="dashboard-grid">
                <div class="card full-width">
                    <h3><i class="fa-solid fa-chalkboard-user"></i> Datos del Tutor</h3>
                    <form action="#" method="POST">
                        <div class="form-grid">
                            <div class="form-group">
                                <label>Número de Empleado</label>
                                <input type="text" class="form-control" placeholder="Ej. EMP-045" required>
                            </div>
                            <div class="form-group">
                                <label>Nombre y Grado</label>
                                <input type="text" class="form-control" placeholder="Ej. Dr. Roberto Ruiz" required>
                            </div>
                            <div class="form-group">
                                <label>Correo Institucional</label>
                                <input type="email" class="form-control" placeholder="profesor@utnay.edu.mx" required>
                            </div>
                            <div class="form-group">
                                <label>Departamento / Academia</label>
                                <select class="form-control" required>
                                    <option value="">Seleccione departamento...</option>
                                    <option value="sistemas">Sistemas y Computación</option>
                                    <option value="ciencias">Ciencias Básicas</option>
                                </select>
                            </div>
                            <div class="form-actions">
                                <button type="reset" class="btn-secondary">Cancelar</button>
                                <button type="submit" class="btn-primary"><i class="fa-solid fa-save"></i> Guardar Tutor</button>
                            </div>
                        </div>
                    </form>
                </div>

                <div class="card full-width">
                    <h3><i class="fa-solid fa-list"></i> Lista de Tutores</h3>
                    <div class="table-responsive">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>N° Empleado</th>
                                    <th>Nombre</th>
                                    <th>Departamento</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>EMP-045</td>
                                    <td>Dr. Roberto Ruiz</td>
                                    <td>Sistemas y Computación</td>
                                    <td>
                                        <div class="action-buttons">
                                            <button class="btn-icon btn-edit"><i class="fa-solid fa-pen"></i></button>
                                            <button class="btn-icon btn-delete"><i class="fa-solid fa-trash"></i></button>
                                        </div>
                                    </td>
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