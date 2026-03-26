<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Administradores | UniAdmin</title>
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
                <a href="/gestion-tutores" class="nav-item "><i class="fa-solid fa-chalkboard-user"></i> Gestión de Tutores</a>
            </nav>
            <div class="sidebar-footer">
                <a href="{{ asset('/Registro') }}" class="nav-item"><i class="fa-solid fa-arrow-right-from-bracket"></i> Salir</a>
            </div>
        </aside>

        <main class="main-content">
            <header class="student-header">
                <div class="student-profile">
                    <div class="student-info">
                        <h1>Gestión de Administradores</h1>
                        <p class="student-id">Control de usuarios del sistema y Servicios Escolares</p>
                    </div>
                </div>
            </header>

            <div class="dashboard-grid">
                <div class="card full-width">
                    <h3><i class="fa-solid fa-shield-halved"></i> Configuración de Acceso</h3>
                    <form action="#" method="POST">
                        <div class="form-grid">
                            <div class="form-group">
                                <label>Nombre del Administrador</label>
                                <input type="text" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label>Correo Electrónico (Usuario)</label>
                                <input type="email" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label>Contraseña Provisional</label>
                                <input type="password" class="form-control" placeholder="Dejar en blanco para no modificar">
                            </div>
                            <div class="form-group">
                                <label>Nivel de Privilegios</label>
                                <select class="form-control" required>
                                    
                                     <option value="superadmin">Servicios Escolares</option>
                                    
                                </select>
                            </div>
                            <div class="form-actions">
                                <button type="reset" class="btn-secondary">Cancelar</button>
                                <button type="submit" class="btn-primary"><i class="fa-solid fa-save"></i> Guardar Usuario</button>
                            </div>
                        </div>
                    </form>
                </div>

                <div class="card full-width">
                    <h3><i class="fa-solid fa-server"></i> Usuarios del Sistema</h3>
                    <div class="table-responsive">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Nombre</th>
                                    <th>Correo (Usuario)</th>
                                    <th>Rol</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Admin General</td>
                                    <td>admin@utnay.edu.mx</td>
                                    <td><span class="badge badge-warning">Servicios Escolares</span></td>
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