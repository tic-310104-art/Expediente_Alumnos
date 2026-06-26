<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('Gestión de Becas') }}</title>
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
                <h1>{{ __('Gestión de Becas') }}</h1>
            </header>

            @if(session('success'))
                <div style="background: #d4edda; color: #155724; padding: 15px; border-radius: 8px; margin-bottom: 20px;">
                    {{ session('success') }}
                </div>
            @endif

            <div class="dashboard-grid" style="margin-bottom: 25px;">
                <!-- Crear Beca -->
                <div class="card">
                    <h3><i class="fa-solid fa-plus"></i> {{ __('Nueva Beca') }}</h3>
                    <form action="{{ route('becas.store') }}" method="POST">
                        @csrf
                        <div class="form-group">
                            <label>{{ __('Nombre') }}</label>
                            <input type="text" name="Nombre" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>{{ __('Monto') }}</label>
                            <input type="number" name="Monto" class="form-control" step="0.01">
                        </div>
                        <div class="form-group">
                            <label>{{ __('Descripción') }}</label>
                            <textarea name="Descripcion" class="form-control"></textarea>
                        </div>
                        <button type="submit" class="btn-primary" style="width: 100%; margin-top: 10px;">{{ __('Guardar') }}</button>
                    </form>
                </div>

                <!-- Asignar Beca -->
                <div class="card">
                    <h3><i class="fa-solid fa-user-tag"></i> {{ __('Asignar Beca') }}</h3>
                    <form action="{{ route('becas.assign') }}" method="POST">
                        @csrf
                        <div class="form-group">
                            <label>{{ __('Alumno') }}</label>
                            <select name="Alumno_id" class="form-control" required>
                                @foreach($alumnos as $alumno)
                                    <option value="{{ $alumno->idAlumnos }}">{{ $alumno->Nombre }} {{ $alumno->Apellido }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label>{{ __('Beca') }}</label>
                            <select name="Beca_id" class="form-control" required>
                                @foreach($becas as $beca)
                                    <option value="{{ $beca->idBecas }}">{{ $beca->Nombre }} (${{ $beca->Monto }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label>{{ __('Fecha de Asignación') }}</label>
                            <input type="date" name="Fecha_Asignacion" class="form-control" value="{{ date('Y-m-d') }}" required>
                        </div>
                        <button type="submit" class="btn-primary" style="width: 100%; margin-top: 10px;">{{ __('Asignar') }}</button>
                    </form>
                </div>
            </div>

            <!-- Lista de Becas -->
            <div class="card full-width">
                <h3><i class="fa-solid fa-list"></i> {{ __('Lista de Becas') }}</h3>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>{{ __('Nombre') }}</th>
                            <th>{{ __('Monto') }}</th>
                            <th>{{ __('Alumnos Asignados') }}</th>
                            <th>{{ __('Acciones') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($becas as $beca)
                        <tr>
                            <td>{{ $beca->Nombre }}</td>
                            <td>${{ number_format($beca->Monto, 2) }}</td>
                            <td>
                                <a href="{{ route('becas.show', $beca->idBecas) }}" style="text-decoration: none; font-weight: 500; color: #10504B;">
                                    {{ $beca->alumnos_count }} {{ __('Ver') }} <i class="fa-solid fa-arrow-right-to-bracket" style="font-size: 0.8rem;"></i>
                                </a>
                            </td>
                            <td>
                                <div style="display: flex; gap: 8px;">
                                    <a href="{{ route('becas.edit', $beca->idBecas) }}" class="btn-icon" style="color: #4b5563; background: #e5e7eb; display: inline-flex; align-items: center; justify-content: center; width: 35px; height: 35px; border-radius: 8px; text-decoration: none;" title="{{ __('Editar beca') }}">
                                        <i class="fa-solid fa-pen"></i>
                                    </a>
                                    <a href="{{ route('becas.show', $beca->idBecas) }}" class="btn-icon" style="display: inline-flex; align-items: center; justify-content: center; width: 35px; height: 35px; border-radius: 8px; text-decoration: none; background: #e0f2fe; color: #0284c7;" title="{{ __('Gestionar asignaciones') }}">
                                        <i class="fa-solid fa-users-gear"></i>
                                    </a>
                                    <button type="button" class="btn-icon btn-delete btn-delete-critical" 
                                            data-url="{{ route('becas.destroy', $beca->idBecas) }}" title="{{ __('Eliminar beca') }}">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
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
                confirmButtonText: '{{ __("Autorizar Acción") }}',
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
                        text: '{{ __("Eliminando beca...") }}',
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
