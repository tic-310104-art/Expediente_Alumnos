<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Administradores | UniAdmin</title>
    <link rel="stylesheet" href="{{ asset('expedienteG.css') }}">
    <link rel="shortcut icon" href="{{ asset('logo-utn.ico') }}" type="image/x-icon">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body>
    <div class="dashboard-container">
        @include('partials.sidebar', ['active' => 'admins'])

        <main class="main-content">
            <header class="student-header">
                <h1>Gestión de Administradores</h1>
            </header>

            @if(session('success'))
                <div style="background: #10504B; color: white; padding: 15px; border-radius: 8px; margin-bottom: 20px; display: flex; align-items: center; gap: 10px;">
                    <i class="fa-solid fa-circle-check"></i>
                    {{ session('success') }}
                </div>
            @endif

            @if($errors->any())
                <div style="background: #ef4444; color: white; padding: 15px; border-radius: 8px; margin-bottom: 20px;">
                    <p style="margin-bottom: 5px; font-weight: 600;"><i class="fa-solid fa-triangle-exclamation"></i> Error al guardar:</p>
                    <ul style="margin-left: 20px; font-size: 0.9rem;">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="dashboard-grid">
                <div class="card full-width">
                    <h3><i class="fa-solid fa-plus"></i> Nuevo Registro</h3>
                    <form action="{{ route('servicios.store') }}" method="POST">
                        @csrf
                        <div class="form-grid">
                            <div class="form-group">
                                <label>Clave Trabajador</label>
                                <input type="text" name="Clave_Trabajador" class="form-control" placeholder="Ej: SE101" value="{{ old('Clave_Trabajador') }}" required>
                            </div>
                           
                            <div class="form-group">
                                <label>Correo </label>
                                <input type="email" name="Correo" class="form-control" value="{{ old('Correo') }}" required>
                            </div>
                            <div class="form-group">
                                <label>Contraseña</label>
                                <input type="password" name="Password" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label>Telefono</label>
                                <input type="text" name="Telefono" class="form-control" placeholder="Ej: +52 311..." value="{{ old('Telefono') }}" required>
                            </div>
                            <div class="form-group" style="display: none;">
                                <label>Rol</label>
                                <input type="hidden" name="Rol" value="Servicios Escolares">
                            </div>
                            <div class="form-actions">
                                <button type="submit" class="btn-primary">Guardar Administrador</button>
                            </div>
                        </div>
                    </form>
                </div>

                <div class="card full-width">
                    <h3><i class="fa-solid fa-list"></i> Lista de Usuarios</h3>
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Clave</th>
                                <th>Correo</th>
                                <th>Telefono</th>
                                <th>Rol</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($admins as $admin)
                            <tr>
                                <td data-label="{{ __('Clave') }}">{{ $admin->Clave_Trabajador }}</td>
                                <td data-label="{{ __('Correo') }}">{{ $admin->Correo ?? $admin->Correo_inst ?? $admin->Email }}</td>
                                <td data-label="{{ __('Teléfono') }}">{{ $admin->Telefono }}</td>
                                <td data-label="{{ __('Rol') }}"><span class="badge badge-warning">{{ $admin->Rol }}</span></td>
                                <td data-label="{{ __('Acciones') }}">
                                    <div class="action-buttons">
                                        <a href="{{ route('servicios.edit', $admin->idServicios_Escolares) }}" class="btn-icon btn-edit">
                                            <i class="fa-solid fa-pen"></i>
                                        </a>

                                        <button type="button" class="btn-icon btn-delete btn-delete-critical" 
                                                data-url="{{ route('servicios.destroy', $admin->idServicios_Escolares) }}">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
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
                confirmButtonText: '{{ __("Autorizar Acción") }}',
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

        function submitWithMethod(url, method) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = url;

            const csrfInput = document.createElement('input');
            csrfInput.type = 'hidden';
            csrfInput.name = '_token';
            csrfInput.value = csrfToken;
            form.appendChild(csrfInput);

            if (method && method.toUpperCase() !== 'POST') {
                const methodInput = document.createElement('input');
                methodInput.type = 'hidden';
                methodInput.name = '_method';
                methodInput.value = method.toUpperCase();
                form.appendChild(methodInput);
            }

            document.body.appendChild(form);
            form.submit();
        }

        document.querySelectorAll('.btn-delete-critical').forEach(button => {
            button.addEventListener('click', async function() {
                const url = this.getAttribute('data-url');

                const ok = await promptTokenAndActivate();
                if (ok) {
                    Swal.fire({
                        title: '{{ __("¡Autorizado!") }}',
                        text: '{{ __("Eliminando registro...") }}',
                        icon: 'success',
                        timer: 1500,
                        showConfirmButton: false,
                        willClose: () => submitWithMethod(url, 'DELETE')
                    });
                }
            });
        });

        @if(session('critical_token_required') && session('critical_intended_url') && session('critical_intended_method'))
            document.addEventListener('DOMContentLoaded', async () => {
                const ok = await promptTokenAndActivate();
                if (!ok) return;
                const url = @json(session('critical_intended_url'));
                const method = @json(session('critical_intended_method'));
                
                Swal.fire({
                    title: '{{ __("¡Autorizado!") }}',
                    text: '{{ __("Completando acción...") }}',
                    icon: 'success',
                    timer: 1500,
                    showConfirmButton: false,
                    willClose: () => submitWithMethod(url, method)
                });
            });
        @endif
    </script>
</body>
</html>
