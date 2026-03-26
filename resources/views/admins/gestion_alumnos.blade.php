<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Alumnos | UniAdmin</title>
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
                <a href="/gestion-tutores" class="nav-item "><i class="fa-solid fa-chalkboard-user"></i> Gestión de Tutores</a>
                <a href="/gestion-admins" class="nav-item"><i class="fa-solid fa-shield-halved"></i> Administradores</a>
            <div class="sidebar-footer">
                <a href="{{ asset('/Registro') }}" class="nav-item"><i class="fa-solid fa-arrow-right-from-bracket"></i> Salir</a>
            </div>
        </aside>

        <main class="main-content">
            <header class="student-header">
                <div class="student-profile">
                    <div class="student-info">
                        <h1>Gestión de Alumnos</h1>
                        <p class="student-id">Módulo de Altas, Bajas y Modificaciones</p>
                    </div>
                </div>
            </header>

            <div class="dashboard-grid">
                <div class="card full-width">
                    <h3><i class="fa-solid fa-user-plus"></i> Formulario de Alumno</h3>
                    <form action="#" method="POST">
                        <div class="form-grid">
                            <div class="form-group">
                                <label>Matrícula</label>
                                <input type="text" class="form-control" placeholder="Ej. TIC-310104" required>
                            </div>
                            <div class="form-group">
                                <label>Nombre Completo</label>
                                <input type="text" class="form-control" placeholder="Nombre(s) y Apellidos" required>
                            </div>
                            <div class="form-group">
                                <label>Correo Electrónico</label>
                                <input type="email" class="form-control" placeholder="correo@utnay.edu.mx" required>
                            </div>
                            <div class="form-group">
                                <label>Carrera</label>
                                <select class="form-control" required>
                                    <option value="">Seleccione una carrera...</option>
                                    <option value="1">Desarrollo de Software</option>
                                    <option value="2">Mecatrónica</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Cuatrimestre</label>
                                <input type="number" class="form-control" min="1" max="12" placeholder="Ej. 8">
                            </div>
                            <div class="form-group">
                                <label>Estatus</label>
                                <select class="form-control">
                                    <option value="activo">Activo (Regular)</option>
                                    <option value="riesgo">En Riesgo</option>
                                    <option value="baja_temp">Baja Temporal</option>
                                </select>
                            </div>
                            <div class="form-actions">
                                <button type="reset" class="btn-secondary">Limpiar</button>
                                <button type="submit" class="btn-primary"><i class="fa-solid fa-save"></i> Guardar Alumno</button>
                            </div>
                        </div>
                    </form>
                </div>

                <div class="card full-width">
                    <h3><i class="fa-solid fa-users"></i> Directorio de Alumnos</h3>
                    <div class="table-responsive">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Matrícula</th>
                                    <th>Nombre</th>
                                    <th>Carrera</th>
                                    <th>Estatus</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td> <a href="{{ asset('/alumno') }}" class="link-perfil">TIC-310104</a></td>
                                    <td>Gilberto Alonso Mendoza</td>
                                    <td>Software</td>
                                    <td><span class="badge badge-success">Activo</span></td>
                                    <td>
                                        <div class="action-buttons">
                                            <button class="btn-icon btn-edit" title="Editar"><i class="fa-solid fa-pen"></i></button>
                                            <button class="btn-icon btn-delete" title="Dar de Baja"><i class="fa-solid fa-trash"></i></button>
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