<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Tutor | UniAdmin</title>
    <link rel="stylesheet" href="{{ asset('expedienteG.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="shortcut icon" href="{{ asset('logo-utn.ico') }}" type="image/x-icon">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>

    <div class="dashboard-container">
        @include('partials.sidebar', ['active' => 'tutores'])

        <main class="main-content">
            <header class="student-header">
                <div class="student-profile">
                    <div class="student-info">
                        <h1>Editar Tutor: {{ $tutor->Nombre }} {{ $tutor->Apellido }}</h1>
                        <p class="student-id">Actualización de datos del personal académico</p>
                    </div>
                </div>
            </header>

            <div class="dashboard-grid">
                <div class="card full-width">
                    <h3><i class="fa-solid fa-pen-to-square"></i> Modificar Datos del Tutor</h3>

                    <form id="edit-tutor-form" action="{{ route('tutores.update', $tutor->idTutores) }}" method="POST">
                        @csrf
                        @method('PUT') 

                        <div class="form-grid">
                            <div class="form-group">
                                <label>Clave Trabajador</label>
                                <input type="text" name="Clave_Trabajador" class="form-control" value="{{ $tutor->Clave_Trabajador }}" required>
                            </div>

                            <div class="form-group">
                                <label>Nombre</label>
                                <input type="text" name="Nombre" class="form-control" value="{{ $tutor->Nombre }}" required>
                            </div>

                            <div class="form-group">
                                <label>Apellido</label>
                                <input type="text" name="Apellido" class="form-control" value="{{ $tutor->Apellido }}" required>
                            </div>

                            <div class="form-group">
                                <label>{{ __('Correo Institucional') }}</label>
                                <input type="email" name="Correo_inst" class="form-control" value="{{ $tutor->Correo_inst }}" required>
                            </div>

                            <div class="form-group">
                                <label>Nueva Contraseña (Opcional)</label>
                                <input type="password" name="Password" class="form-control" placeholder="Dejar en blanco para no cambiar">
                            </div>

                            <div class="form-group">
                                <label>Teléfono</label>
                                <input type="text" name="Telefono" class="form-control" value="{{ $tutor->Telefono }}">
                            </div>

                            <div class="form-group">
                                <label>Carrera Asignada</label>
                                <select name="idCarreras" class="form-control" required>
                                    <option value="" disabled>{{ __('Selecciona una Carrera') }}</option>
                                    @foreach($carreras as $carrera)
                                        <option value="{{ $carrera->idCarreras }}" 
                                            {{ $tutor->idCarreras == $carrera->idCarreras ? 'selected' : '' }}>
                                            {{ $carrera->Nombre }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <input type="hidden" name="Rol" value="{{ $tutor->Rol }}">

                            <div class="form-actions">
                                <a href="{{ route('tutores.index') }}" class="btn-secondary" style="text-decoration: none; display: flex; align-items: center;">Cancelar</a>
                                
                                <button type="submit" class="btn-primary">
                                    <i class="fa-solid fa-save"></i> Actualizar Tutor
                                </button>
                            </div>

                        </div>
                    </form>
                </div>
            </div>
        </main>
    </div>

    <script>
        const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

        async function promptTokenAndActivate() {
            const isDark = document.body.classList.contains('dark-mode');
            return Swal.fire({
                title: '{{ __("Confirmación de Seguridad") }}',
                html: `
                    <div style="text-align: center; padding: 10px;">
                        <div style="background: ${isDark ? '#1e293b' : '#f3f4f6'}; padding: 15px; border-radius: 12px; margin-bottom: 20px;">
                            <i class="fa-solid fa-shield-halved" style="font-size: 3rem; color: var(--primary-color); margin-bottom: 15px;"></i>
                            <p style="color: ${isDark ? '#f1f5f9' : '#374151'}; font-weight: 600; margin-bottom: 5px;">{{ __("Acción Crítica Detectada") }}</p>
                            <p style="color: ${isDark ? '#94a3b8' : '#6b7280'}; font-size: 0.9rem; margin: 0;">{{ __("Para proteger la integridad del sistema, por favor ingresa tu token de seguridad.") }}</p>
                        </div>
                        <label style="display: block; text-align: left; margin-bottom: 8px; font-weight: 600; color: ${isDark ? '#f1f5f9' : '#374151'};">{{ __("Token JWT") }}</label>
                        <input id="swal-token" class="swal2-input" placeholder="eyJhbGciOiJIUzI1Ni..." style="width: 100%; margin: 0; padding: 12px; border-radius: 8px; border: 1px solid ${isDark ? '#475569' : '#d1d5db'}; box-sizing: border-box; background: ${isDark ? '#1e293b' : '#fff'}; color: ${isDark ? '#f1f5f9' : '#374151'};">
                    </div>
                `,
                showCancelButton: true,
                confirmButtonText: '{{ __("Autorizar Cambio") }}',
                cancelButtonText: '{{ __("Cancelar") }}',
                confirmButtonColor: '#0d9488',
                cancelButtonColor: '#6b7280',
                width: '500px',
                padding: '1.5rem',
                focusConfirm: false,
                showLoaderOnConfirm: true,
                background: isDark ? '#1e293b' : '#fff',
                color: isDark ? '#f1f5f9' : '#2d3748',
                preConfirm: () => {
                    const token = document.getElementById('swal-token').value;
                    if (!token) {
                        Swal.showValidationMessage('{{ __("El token es obligatorio") }}');
                        return false;
                    }
                    return fetch("{{ route('jwt.verify') }}", {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({ token: token })
                    })
                    .then(response => {
                        if (!response.ok) return response.json().then(json => { throw new Error(json.message) });
                        return response.json();
                    })
                    .catch(error => Swal.showValidationMessage(`Error: ${error.message}`));
                },
                allowOutsideClick: () => !Swal.isLoading()
            }).then(result => result.isConfirmed);
        }

        document.getElementById('edit-tutor-form').addEventListener('submit', async function(e) {
            e.preventDefault();
            const form = this;

            const ok = await promptTokenAndActivate();
            if (ok) {
                Swal.fire({
                    title: '{{ __("¡Autorizado!") }}',
                    text: '{{ __("Procesando actualización...") }}',
                    icon: 'success',
                    timer: 1500,
                    showConfirmButton: false,
                    willClose: () => form.submit()
                });
            }
        });

        @if(session('critical_token_required'))
            document.addEventListener('DOMContentLoaded', async () => {
                const ok = await promptTokenAndActivate();
                if (ok) {
                    Swal.fire({
                        title: '{{ __("¡Autorizado!") }}',
                        text: '{{ __("Completando acción anterior...") }}',
                        icon: 'success',
                        timer: 1500,
                        showConfirmButton: false,
                        willClose: () => document.getElementById('edit-tutor-form').submit()
                    });
                }
            });
        @endif
    </script>
</body>
</html>