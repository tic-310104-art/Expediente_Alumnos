<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mis Alumnos | Panel de Tutoría</title>
    <link rel="stylesheet" href="{{ asset('expedienteG.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="shortcut icon" href="{{ asset('logo-utn.ico') }}" type="image/x-icon">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>

    <div class="dashboard-container">
        @include('partials.sidebar', ['active' => 'alumnos'])

        <main class="main-content">
            <header class="student-header">
                <div class="student-profile">
                    @php
                        $tutorFoto = $tutor->foto_url ?? "https://ui-avatars.com/api/?name=" . urlencode($tutor->Nombre . '+' . $tutor->Apellido) . "&background=10504B&color=fff&size=100";
                    @endphp
                    <img src="{{ $tutorFoto }}" alt="Foto del tutor" class="profile-img">
                    <div class="student-info">
                        <h1>Módulo: Mis Alumnos</h1>
                        <p class="student-id"><i class="fa-solid fa-chalkboard-user"></i> Tutor: <strong>{{ $tutor->Nombre }} {{ $tutor->Apellido }}</strong></p>
                    </div>
                </div>
                <div class="student-status active-status" style="background:#2b7a78; color:#fff;">
                    Total: <span>{{ $tutor->alumnos->count() }} alumnos</span>
                </div>
            </header>

            <div class="dashboard-grid">
                <div class="card full-width">
                    <h3><i class="fa-solid fa-users"></i> {{ __('Directorio de Tutorados') }}</h3>
                    
                    {{-- FILTROS --}}
                    <div class="filters-container" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px; margin-bottom: 20px; padding: 15px; background: var(--bg-color); border-radius: 8px;">
                        <div class="filter-group">
                            <label style="display: block; font-size: 13px; margin-bottom: 5px;">{{ __('Carrera') }}</label>
                            <select id="filter-carrera" class="form-control">
                                <option value="">{{ __('Todas') }}</option>
                                @php
                                    $carreras = $tutor->alumnos->pluck('carreras')->flatten()->unique('idCarreras');
                                @endphp
                                @foreach($carreras as $carrera)
                                    <option value="{{ $carrera->Nombre }}">{{ $carrera->Nombre }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="filter-group">
                            <label style="display: block; font-size: 13px; margin-bottom: 5px;">{{ __('Cuatrimestre') }}</label>
                            <select id="filter-cuatrimestre" class="form-control">
                                <option value="">{{ __('Todos') }}</option>
                                @for($i=1; $i<=11; $i++)
                                    <option value="{{ $i }}">{{ $i }}</option>
                                @endfor
                            </select>
                        </div>
                        <div class="filter-group" style="display: flex; align-items: flex-end;">
                            <button id="clear-filters" class="btn-secondary" style="width: 100%;">{{ __('Limpiar Filtros') }}</button>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="data-table" id="tutorados-table">
                            <thead>
                                <tr>
                                    <th>{{ __('Matrícula') }}</th>
                                    <th>{{ __('Nombre del Alumno') }}</th>
                                    <th>{{ __('Carrera') }}</th>
                                    <th>{{ __('Cuatrimestre') }}</th>
                                    <th>{{ __('Promedio') }}</th>
                                    <th>{{ __('Grupo') }}</th>
                                    <th>{{ __('Correo') }}</th>
                                    <th>{{ __('Estatus') }}</th>
                                    <th>{{ __('Acciones') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($tutor->alumnos as $alumno)
                                <tr class="alumno-row" 
                                    data-carrera="{{ $alumno->carreras->first()->Nombre ?? '' }}" 
                                    data-cuatrimestre="{{ $alumno->Cuatrimestre }}">
                                    <td><a href="{{ route('alumno.dashboard', $alumno->idAlumnos) }}" style="color:#2b7a78;font-weight:bold;">{{ $alumno->Matricula }}</a></td>
                                    <td>{{ $alumno->Nombre }} {{ $alumno->Apellido }}</td>
                                    <td>{{ $alumno->carreras->first()->Nombre ?? __('Sin Carrera') }}</td>
                                    <td>{{ $alumno->Cuatrimestre }}°</td>
                                    <td style="font-weight: bold; color: {{ \App\Models\Alumno::getRiesgoColor($alumno->promedio) }};">
                                        {{ $alumno->promedio > 0 ? $alumno->promedio : __('N/A') }}
                                    </td>
                                    <td>{{ $alumno->grupo->Grupo ?? __('Sin Asignar') }}</td>
                                    <td>{{ $alumno->Correo_inst }}</td>
                                    <td style="min-width: 160px;">
                                        <form class="estatus-form" action="{{ route('alumnos.estatus', $alumno->idAlumnos) }}" method="POST">
                                            @csrf
                                            @php $current = strtolower($alumno->Estatus ?? 'activo'); @endphp
                                            <select name="estatus" class="form-control" onchange="handleEstatusChange(this)">
                                                <option value="activo" {{ $current === 'activo' ? 'selected' : '' }}>{{ __('Activo') }}</option>
                                                <option value="baja" {{ $current === 'baja' ? 'selected' : '' }}>{{ __('Baja') }}</option>
                                                <option value="riesgo" {{ $current === 'riesgo' ? 'selected' : '' }}>{{ __('En riesgo') }}</option>
                                            </select>
                                        </form>
                                    </td>
                                    <td style="display: flex; gap: 4px; flex-wrap: wrap;">
                                        <a href="{{ route('tutor.alumnos.calificaciones', ['id' => $tutor->idTutores, 'alumnoId' => $alumno->idAlumnos]) }}" class="btn-primary" 
                                           style="padding: 4px 8px; font-size: 10px; text-decoration: none; border-radius: 4px; display: inline-flex; align-items: center; gap: 3px; background-color: #dc2626;" title="{{ __('Asignar Calificaciones') }}">
                                           <i class="fa-solid fa-grade"></i> {{ __('Asign.') }}
                                        </a>
                                        <a href="{{ route('historial.show', $alumno->idAlumnos) }}" class="btn-primary" 
                                           style="padding: 4px 8px; font-size: 10px; text-decoration: none; border-radius: 4px; display: inline-flex; align-items: center; gap: 3px; background-color: #10504B;" title="{{ __('Ver Calificaciones') }}">
                                           <i class="fa-solid fa-graduation-cap"></i> {{ __('Calif.') }}
                                        </a>
                                        <a href="{{ route('tutor.citas', ['id' => $tutor->idTutores, 'alumno_id' => $alumno->idAlumnos]) }}" class="btn-primary" 
                                           style="padding: 4px 8px; font-size: 10px; text-decoration: none; border-radius: 4px; display: inline-flex; align-items: center; gap: 3px; background-color: #2b7a78;" title="{{ __('Agendar Tutoría') }}">
                                           <i class="fa-solid fa-calendar-plus"></i> {{ __('Citar') }}
                                        </a>
                                        <a href="{{ route('tutor.psicologia', ['id' => $tutor->idTutores, 'alumno_id' => $alumno->idAlumnos]) }}" class="btn-primary" 
                                           style="padding: 4px 8px; font-size: 10px; text-decoration: none; border-radius: 4px; display: inline-flex; align-items: center; gap: 3px; background-color: #6366f1;" title="{{ __('Cita Psicología') }}">
                                           <i class="fa-solid fa-brain"></i> {{ __('Psicol.') }}
                                        </a>
                                        <a href="{{ route('tutor.asesorias', $tutor->idTutores) }}" class="btn-primary" 
                                           style="padding: 4px 8px; font-size: 10px; text-decoration: none; border-radius: 4px; display: inline-flex; align-items: center; gap: 3px; background-color: #f59e0b;" title="{{ __('Agendar Asesoría') }}">
                                           <i class="fa-solid fa-chalkboard-user"></i> {{ __('Ases.') }}
                                        </a>
                                        <a href="{{ route('alumno.pdf.resumen', $alumno->idAlumnos) }}" target="_blank" class="btn-primary" 
                                           style="padding: 4px 8px; font-size: 10px; text-decoration: none; border-radius: 4px; display: inline-flex; align-items: center; gap: 3px; background-color: #0d9488;" title="{{ __('Descargar Resumen PDF') }}">
                                           <i class="fa-solid fa-file-pdf"></i> {{ __('PDF') }}
                                        </a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="9" style="text-align: center;">{{ __('No tiene alumnos asignados actualmente.') }}</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script>
        const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

        async function promptTokenAndActivate() {
            @if(session('critical_token'))
                return true;
            @endif

            const { value: token } = await Swal.fire({
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
            });

            return !!token;
        }

        async function handleEstatusChange(select) {
            const form = select.form;
            const ok = await promptTokenAndActivate();
            if (ok) {
                Swal.fire({
                    title: '{{ __("¡Autorizado!") }}',
                    text: '{{ __("Actualizando estatus...") }}',
                    icon: 'success',
                    timer: 1500,
                    showConfirmButton: false,
                    willClose: () => form.submit()
                });
            } else {
                location.reload(); 
            }
        }

        document.querySelectorAll('form.estatus-form').forEach(f => {
            f.addEventListener('submit', async (e) => {
                e.preventDefault();
                const form = e.target;
                const ok = await promptTokenAndActivate();
                if (ok) {
                    Swal.fire({
                        title: '{{ __("¡Autorizado!") }}',
                        text: '{{ __("Actualizando estatus...") }}',
                        icon: 'success',
                        timer: 1500,
                        showConfirmButton: false,
                        willClose: () => form.submit()
                    });
                }
            });
        });
        const filterCarrera = document.getElementById('filter-carrera');
        const filterCuatrimestre = document.getElementById('filter-cuatrimestre');
        const clearFilters = document.getElementById('clear-filters');
        const rows = document.querySelectorAll('.alumno-row');

        function applyFilters() {
            const carrera = filterCarrera.value.toLowerCase();
            const cuatri = filterCuatrimestre.value;

            rows.forEach(row => {
                const rowCarrera = row.getAttribute('data-carrera').toLowerCase();
                const rowCuatri = row.getAttribute('data-cuatrimestre');

                let show = true;
                if (carrera && rowCarrera !== carrera) show = false;
                if (cuatri && rowCuatri !== cuatri) show = false;

                row.style.display = show ? '' : 'none';
            });
        }

        [filterCarrera, filterCuatrimestre].forEach(f => {
            f.addEventListener('change', applyFilters);
        });

        clearFilters.addEventListener('click', () => {
            filterCarrera.value = '';
            filterCuatrimestre.value = '';
            applyFilters();
        });
    </script>
</body>
</html>
