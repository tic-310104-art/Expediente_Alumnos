<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('Detalles de Beca') }}</title>
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
                <div style="display: flex; align-items: center; gap: 15px;">
                    <a href="{{ route('becas.index') }}" class="btn-icon" style="color: #10504B; font-size: 1.5rem;"><i class="fa-solid fa-arrow-left"></i></a>
                    <h1>{{ __('Detalles de Beca:') }} {{ $beca->Nombre }}</h1>
                </div>
            </header>

            @if(session('success'))
                <div style="background: #d4edda; color: #155724; padding: 15px; border-radius: 8px; margin-bottom: 20px;">
                    {{ session('success') }}
                </div>
            @endif

            <div class="card full-width">
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <h3><i class="fa-solid fa-circle-info"></i> {{ __('Información General') }}</h3>
                    <a href="{{ route('becas.edit', $beca->idBecas) }}" class="btn-primary" style="text-decoration: none;"><i class="fa-solid fa-pen-to-square"></i> {{ __('Editar Beca') }}</a>
                </div>
                <div style="margin-top: 15px; display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                    <div>
                        <strong>{{ __('Nombre:') }}</strong> {{ $beca->Nombre }}
                    </div>
                    <div>
                        <strong>{{ __('Monto:') }}</strong> ${{ number_format($beca->Monto, 2) }}
                    </div>
                </div>
                <div style="margin-top: 15px;">
                    <strong>{{ __('Descripción:') }}</strong>
                    <p>{{ $beca->Descripcion ?: __('Sin descripción') }}</p>
                </div>
            </div>

            <div class="card full-width" style="margin-top: 25px;">
                <h3><i class="fa-solid fa-users"></i> {{ __('Alumnos Asignados') }} ({{ $beca->alumnos->count() }})</h3>
                
                @if($beca->alumnos->isEmpty())
                    <p style="text-align: center; color: #6b7280; padding: 20px;">{{ __('No hay alumnos asignados a esta beca.') }}</p>
                @else
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>{{ __('Matrícula / ID') }}</th>
                                <th>{{ __('Nombre Completo') }}</th>
                                <th>{{ __('Fecha Asignación') }}</th>
                                <th>{{ __('Acciones') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($beca->alumnos as $alumno)
                            <tr>
                                <td>{{ $alumno->idAlumnos }}</td>
                                <td>{{ $alumno->Nombre }} {{ $alumno->Apellido }}</td>
                                <td>{{ $alumno->pivot->Fecha_Asignacion }}</td>
                                <td>
                                    <button type="button" class="btn-icon btn-delete btn-delete-unassign-critical" 
                                            data-url="{{ route('becas.unassign', ['beca' => $beca->idBecas, 'alumno' => $alumno->idAlumnos]) }}"
                                            title="{{ __('Eliminar asignación') }}">
                                        <i class="fa-solid fa-user-xmark"></i>
                                    </button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
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

        document.querySelectorAll('.btn-delete-unassign-critical').forEach(button => {
            button.addEventListener('click', async function() {
                const url = this.getAttribute('data-url');

                const ok = await promptTokenAndActivate();
                if (ok) {
                    Swal.fire({
                        title: '{{ __("¡Autorizado!") }}',
                        text: '{{ __("Eliminando asignación...") }}',
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
                    text: '{{ __("Completando acción anterior...") }}',
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
