<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ __('Editar Carrera') }} | {{ $carrera->Nombre }}</title>
    <link rel="stylesheet" href="{{ asset('expedienteG.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="shortcut icon" href="{{ asset('logo-utn.ico') }}" type="image/x-icon">
</head>
<body>

    <div class="dashboard-container">
        @include('partials.sidebar', ['active' => 'carreras'])

        <main class="main-content">
            <header class="student-header">
                <div class="student-profile">
                    <div class="profile-img" style="display:flex;align-items:center;justify-content:center;background:#10504B;color:#fff;">
                        <i class="fa-solid fa-graduation-cap"></i>
                    </div>
                    <div class="student-info">
                        <h1>{{ __('Editar Carrera') }}: {{ $carrera->Nombre }}</h1>
                        <p class="student-id">{{ __('Actualización del catálogo académico') }}</p>
                    </div>
                </div>
            </header>

            <div class="dashboard-grid">
                <div class="card full-width">
                    <h3><i class="fa-solid fa-pen-to-square"></i> {{ __('Modificar Datos de la Carrera') }}</h3>

                    <form id="edit-carrera-form" action="{{ route('carreras.update', $carrera->idCarreras) }}" method="POST">
                        @csrf
                        @method('PATCH') 

                        <div class="form-grid">
                            <div class="form-group">
                                <label>{{ __('Nombre de la Carrera') }}</label>
                                <input type="text" name="Nombre" class="form-control" value="{{ $carrera->Nombre }}" required>
                            </div>

                            <div class="form-actions">
                                <a href="{{ route('carreras.index') }}" class="btn-secondary" style="text-decoration: none; display: flex; align-items: center; justify-content: center;">{{ __('Cancelar') }}</a>
                                <button type="submit" class="btn-primary">
                                    <i class="fa-solid fa-rotate"></i> {{ __('Actualizar Carrera') }}
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
            return Swal.fire({
                title: '{{ __("Confirmación de Seguridad") }}',
                html: `
                    <div style="text-align: center; padding: 10px;">
                        <div style="background: #f3f4f6; padding: 15px; border-radius: 12px; margin-bottom: 20px;">
                            <i class="fa-solid fa-shield-halved" style="font-size: 3rem; color: #10504B; margin-bottom: 15px;"></i>
                            <p style="color: #374151; font-weight: 600; margin-bottom: 5px;">{{ __("Acción Crítica Detectada") }}</p>
                            <p style="color: #6b7280; font-size: 0.9rem; margin: 0;">{{ __("Para proteger la integridad del sistema, por favor ingresa tu token de seguridad.") }}</p>
                        </div>
                        <label style="display: block; text-align: left; margin-bottom: 8px; font-weight: 600; color: #374151;">{{ __("Token JWT") }}</label>
                        <input id="swal-token" class="swal2-input" placeholder="eyJhbGciOiJIUzI1Ni..." style="width: 100%; margin: 0; padding: 12px; border-radius: 8px; border: 1px solid #d1d5db; box-sizing: border-box;">
                    </div>
                `,
                showCancelButton: true,
                confirmButtonText: '{{ __("Autorizar Cambio") }}',
                cancelButtonText: '{{ __("Cancelar") }}',
                confirmButtonColor: '#10504B',
                cancelButtonColor: '#6b7280',
                width: '500px',
                padding: '1.5rem',
                focusConfirm: false,
                showLoaderOnConfirm: true,
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

        document.getElementById('edit-carrera-form').addEventListener('submit', async function(e) {
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
                        willClose: () => document.getElementById('edit-carrera-form').submit()
                    });
                }
            });
        @endif
    </script>
</body>
</html>
