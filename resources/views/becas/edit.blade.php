<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('Editar Beca') }}</title>
    <link rel="stylesheet" href="{{ asset('expedienteG.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="shortcut icon" href="{{ asset('logo-utn.ico') }}" type="image/x-icon">
</head>
<body>
    <div class="dashboard-container">
        @include('partials.sidebar', ['active' => 'becas'])

        <main class="main-content">
            <header class="student-header">
                <h1>{{ __('Editar Beca') }}</h1>
            </header>

            @if(session('success'))
                <div style="background: #d4edda; color: #155724; padding: 15px; border-radius: 8px; margin-bottom: 20px;">
                    {{ session('success') }}
                </div>
            @endif

            <div class="card" style="max-width: 600px; margin: 0 auto;">
                <h3><i class="fa-solid fa-pen-to-square"></i> {{ __('Modificar Beca') }}</h3>
                <form action="{{ route('becas.update', $beca->idBecas) }}" method="POST" id="edit-form">
                    @csrf
                    @method('PUT')
                    <div class="form-group">
                        <label>{{ __('Nombre') }}</label>
                        <input type="text" name="Nombre" class="form-control" value="{{ old('Nombre', $beca->Nombre) }}" required>
                    </div>
                    <div class="form-group">
                        <label>{{ __('Monto') }}</label>
                        <input type="number" name="Monto" class="form-control" step="0.01" value="{{ old('Monto', $beca->Monto) }}">
                    </div>
                    <div class="form-group">
                        <label>{{ __('Descripción') }}</label>
                        <textarea name="Descripcion" class="form-control">{{ old('Descripcion', $beca->Descripcion) }}</textarea>
                    </div>
                    <div style="display: flex; gap: 10px; margin-top: 15px;">
                        <a href="{{ route('becas.index') }}" class="btn-secondary" style="flex: 1; text-align: center; background: #6b7280; color: white; padding: 10px; border-radius: 8px; text-decoration: none;">{{ __('Cancelar') }}</a>
                        <button type="button" class="btn-primary btn-edit-critical" style="flex: 1;">{{ __('Actualizar') }}</button>
                    </div>
                </form>
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

        document.querySelector('.btn-edit-critical').addEventListener('click', async function() {
            const ok = await promptTokenAndActivate();
            if (ok) {
                Swal.fire({
                    title: '{{ __("¡Autorizado!") }}',
                    text: '{{ __("Actualizando beca...") }}',
                    icon: 'success',
                    timer: 1500,
                    showConfirmButton: false,
                    willClose: () => document.getElementById('edit-form').submit()
                });
            }
        });

        @if(session('critical_token_required') && session('critical_intended_url') && session('critical_intended_method'))
            document.addEventListener('DOMContentLoaded', async () => {
                const ok = await promptTokenAndActivate();
                if (!ok) return;
                
                Swal.fire({
                    title: '{{ __("¡Autorizado!") }}',
                    text: '{{ __("Completando acción anterior...") }}',
                    icon: 'success',
                    timer: 1500,
                    showConfirmButton: false,
                    willClose: () => document.getElementById('edit-form').submit()
                });
            });
        @endif
    </script>
</body>
</html>
