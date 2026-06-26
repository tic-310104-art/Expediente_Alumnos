<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('Gestionar Carga Académica') }} | UniAdmin</title>
    <link rel="stylesheet" href="{{ asset('expedienteG.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .selected-row {
            background-color: #f0fdf4 !important;
        }
        body.dark-mode .selected-row {
            background-color: #064e3b !important;
            color: #ecfdf5 !important;
        }
        body.dark-mode .materia-input:disabled {
            background-color: #1e293b;
            opacity: 0.5;
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        @include('partials.sidebar', ['active' => 'carreras'])

        <main class="main-content">
            <header class="student-header">
                <div class="student-info">
                    <h1>{{ __('Carga Académica') }}: {{ $grupo->Grupo }}</h1>
                    <p class="student-id">{{ __('Carrera') }}: {{ $grupo->carrera->Nombre ?? __('N/A') }}</p>
                </div>
            </header>

            <div class="dashboard-grid">
                <div class="card full-width">
                    <h3><i class="fa-solid fa-table-list"></i> {{ __('Matriz de Carga Académica') }}</h3>
                    <p style="margin-bottom: 20px; color: var(--text-muted);">{{ __('Selecciona las materias de la carrera y asigna un maestro y horario para este grupo.') }}</p>

                    @if ($errors->any())
                        <div class="alert alert-danger" style="background:#fee2e2; color:#b91c1c; padding:15px; border-radius:8px; margin-bottom: 20px;">
                            <ul style="margin:0; padding-left:20px;">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form id="carga-form" action="{{ route('grupos.carga.store', $grupo->idGrupos) }}" method="POST">
                        @csrf
                        <div class="table-responsive">
                            <table class="data-table">
                                <thead>
                                    <tr>
                                        <th style="width: 50px;">{{ __('Sel.') }}</th>
                                        <th>{{ __('Materia') }}</th>
                                        <th>{{ __('Cuatrimestre') }}</th>
                                        <th>{{ __('Maestro / Docente') }}</th>
                                        <th>{{ __('Horario') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($materiasCarrera as $index => $materia)
                                        @php
                                            $cargaActual = $grupo->materias->where('idMateria', $materia->idMateria)->first();
                                        @endphp
                                        <tr class="{{ $cargaActual ? 'selected-row' : '' }}">
                                            <td style="text-align: center;">
                                                <input type="hidden" name="materias[{{ $index }}][idMateria]" value="{{ $materia->idMateria }}">
                                                <input type="checkbox" name="materias[{{ $index }}][selected]" value="1" {{ $cargaActual ? 'checked' : '' }} class="materia-checkbox" onchange="toggleRow(this)">
                                            </td>
                                            <td>
                                                <strong>{{ $materia->Nombre }}</strong>
                                                @if($cargaActual)
                                                    <span style="display:block; font-size: 10px; color: #166534; font-weight: bold;">{{ __('ASIGNADA') }}</span>
                                                @endif
                                            </td>
                                            <td style="text-align: center;">{{ $materia->Cuatrimestre }}°</td>
                                            <td>
                                                <input type="text" name="materias[{{ $index }}][Maestro]" class="form-control materia-input" 
                                                       placeholder="{{ __('Nombre del Maestro') }}" 
                                                       value="{{ $cargaActual ? $cargaActual->pivot->Maestro : '' }}"
                                                       {{ $cargaActual ? '' : 'disabled' }}>
                                            </td>
                                            <td>
                                                <input type="text" name="materias[{{ $index }}][Horario]" class="form-control materia-input" 
                                                       placeholder="{{ __('Ej. Lun-Vie 7:00-9:00') }}" 
                                                       value="{{ $cargaActual ? $cargaActual->pivot->Horario : '' }}"
                                                       {{ $cargaActual ? '' : 'disabled' }}>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" style="text-align: center;">{{ __('No hay materias definidas para esta carrera.') }}</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <div class="form-actions" style="margin-top: 20px; display: flex; gap: 10px;">
                            <button type="submit" class="btn-primary"><i class="fa-solid fa-save"></i> {{ __('Guardar Carga Académica') }}</button>
                            <a href="{{ route('materias.show', $grupo->idCarreras) }}" class="btn-secondary">{{ __('Cancelar') }}</a>
                        </div>
                    </form>
                </div>
            </div>
        </main>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
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

        document.getElementById('carga-form').addEventListener('submit', async function(e) {
            e.preventDefault();
            const form = this;

            const ok = await promptTokenAndActivate();
            if (ok) {
                Swal.fire({
                    title: '{{ __("¡Autorizado!") }}',
                    text: '{{ __("Guardando carga académica...") }}',
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
                        willClose: () => document.getElementById('carga-form').submit()
                    });
                }
            });
        @endif

        function toggleRow(checkbox) {
            const row = checkbox.closest('tr');
            const inputs = row.querySelectorAll('.materia-input');
            
            if (checkbox.checked) {
                row.classList.add('selected-row');
                inputs.forEach(input => input.disabled = false);
            } else {
                row.classList.remove('selected-row');
                inputs.forEach(input => input.disabled = true);
            }
        }
    </script>
</body>
</html>
