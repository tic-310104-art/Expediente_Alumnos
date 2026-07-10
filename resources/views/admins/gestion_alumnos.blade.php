<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('Gestión de Alumnos') }} | UniAdmin</title>
    <link rel="stylesheet" href="{{ asset('expedienteG.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="shortcut icon" href="{{ asset('logo-utn.ico') }}" type="image/x-icon">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body>

    <div class="dashboard-container">
        @include('partials.sidebar', ['active' => 'alumnos'])

        <main class="main-content">
            @if(session('success'))
                <div class="alert alert-success" style="background-color: #d4edda; color: #155724; padding: 10px; border-radius: 5px; margin-bottom: 20px;">
                    {{ session('success') }}
                </div>
            @endif

            <div class="dashboard-grid">
                <div class="card full-width">
                    <h3><i class="fa-solid fa-user-plus"></i> {{ __('Formulario de Alumno') }}</h3>
                    <form action="{{ route('alumnos.store') }}" method="POST">
                        @csrf <div class="form-grid">
                            <div class="form-group">
                                <label>{{ __('Nombre(s)') }}</label>
                                <input type="text" name="Nombre" class="form-control" placeholder="{{ __('Nombre(s)') }}" required>
                            </div>
                            <div class="form-group">
                                <label>{{ __('Apellidos') }}</label>
                                <input type="text" name="Apellido" class="form-control" placeholder="{{ __('Apellidos') }}" required>
                            </div>
                            
                            <div class="form-group">
                                <label>{{ __('Matrícula') }}</label>
                                <input type="text" name="Matricula" class="form-control" placeholder="Ej. TIC-310104" required>
                            </div>
                            
                            <div class="form-group">
                                <label>{{ __('Correo Institucional') }}</label>
                                <input type="email" name="Correo_inst" class="form-control" placeholder="correo@utnay.edu.mx" required>
                            </div>
                            
                            <div class="form-group">
                                <label>{{ __('Contraseña') }}</label>
                                <input type="password" name="Password" class="form-control" placeholder="{{ __('Contraseña') }}" required>
                            </div>
                             
                               <div class="form-group">
                                <label>{{ __('Número de Teléfono') }}</label>
                                <input type="text" name="Telefono" class="form-control" placeholder="+52 311..." required>
                            </div>
                            
                            <div class="form-group">
                                <label>{{ __('Cuatrimestre') }}</label>
                                <input type="number" name="Cuatrimestre" class="form-control" min="1" max="11" placeholder="Ej. 8">
                            </div>

                            <div class="form-group">
                                <label>{{ __('Carrera') }}</label>
                                <select name="Carreras_id" class="form-control" id="carrera-select" required>
                                    <option value="" disabled selected>{{ __('Selecciona una Carrera') }}</option>
                                    @forelse($carreras as $carrera)
                                        <option value="{{ $carrera->idCarreras }}">{{ $carrera->Nombre }}</option>
                                    @empty
                                        <option value="" disabled>{{ __('No hay carreras registradas') }}</option>
                                    @endforelse
                                </select>
                            </div>

                            <div class="form-group">
                                <label>{{ __('Grupo') }}</label>
                                <select name="Grupos_id" class="form-control" id="grupo-select" required>
                                    <option value="" disabled selected>{{ __('Selecciona primero una Carrera') }}</option>
                                </select>
                            </div>

                            <div class="form-actions">
                                <button type="reset" class="btn-secondary">{{ __('Limpiar') }}</button>
                                <button type="submit" class="btn-primary"><i class="fa-solid fa-save"></i> {{ __('Guardar Alumno') }}</button>
                            </div>
                        </div>
                    </form>
                </div>

                <div class="card full-width">
                    <h3><i class="fa-solid fa-users"></i> {{ __('Directorio de Alumnos') }}</h3>
                    
                    {{-- FILTROS AVANZADOS --}}
                    <div class="filters-container" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px; margin-bottom: 20px; padding: 15px; background: var(--bg-color); border-radius: 8px;">
                        <div class="filter-group">
                            <label style="display: block; font-size: 13px; margin-bottom: 5px;">{{ __('Carrera') }}</label>
                            <select id="filter-carrera" class="form-control">
                                <option value="">{{ __('Todas') }}</option>
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
                        <div class="filter-group">
                            <label style="display: block; font-size: 13px; margin-bottom: 5px;">{{ __('Estatus') }}</label>
                            <select id="filter-estatus" class="form-control">
                                <option value="">{{ __('Todos') }}</option>
                                <option value="activo">{{ __('Activo') }}</option>
                                <option value="baja">{{ __('Baja') }}</option>
                                <option value="riesgo">{{ __('En riesgo') }}</option>
                            </select>
                        </div>
                        <div class="filter-group" style="display: flex; align-items: flex-end;">
                            <button id="clear-filters" class="btn-secondary" style="width: 100%;">{{ __('Limpiar Filtros') }}</button>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="data-table" id="alumnos-table">
                            <thead>
                                <tr>
                                    <th>{{ __('Matrícula') }}</th>
                                    <th>{{ __('Nombre Completo') }}</th>
                                    <th>{{ __('Carrera') }}</th>
                                    <th>{{ __('Cuatrimestre') }}</th>
                                    <th>{{ __('Correo') }}</th>
                                    <th>{{ __('Telefono') }}</th>
                                    <th>{{ __('Estatus') }}</th>
                                    
                                    <th>{{ __('Acciones') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($alumnos as $alumno)
                                <tr class="alumno-row" 
                                    data-carrera="{{ $alumno->carrera->Nombre ?? '' }}" 
                                    data-cuatrimestre="{{ $alumno->Cuatrimestre }}" 
                                    data-estatus="{{ strtolower($alumno->Estatus ?? 'activo') }}">
                                    <td data-label="{{ __('Matrícula') }}"><a href="{{ route('alumno.dashboard', $alumno->idAlumnos) }}" class="link-perfil">{{ $alumno->Matricula }}</a></td>
                                    <td data-label="{{ __('Nombre') }}">{{ $alumno->Nombre }} {{ $alumno->Apellido }}</td>
                                    <td data-label="{{ __('Carrera') }}">{{ $alumno->carrera->Nombre ?? __('Sin Carrera') }}</td>
                                    <td data-label="{{ __('Cuatrimestre') }}">{{ $alumno->Cuatrimestre }}</td>
                                    <td data-label="{{ __('Correo') }}">{{ $alumno->Correo_inst}}</td>
                                    <td data-label="{{ __('Teléfono') }}">{{ $alumno->Telefono}}</td>
<td data-label="{{ __('Estatus') }}">
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
                                  
                                    <td data-label="{{ __('Acciones') }}">
                                        <div class="action-buttons">
                                            <a href="{{ route('alumno.pdf.resumen', $alumno->idAlumnos) }}" target="_blank" class="btn-icon btn-pdf" title="{{ __('Descargar Resumen PDF') }}" style="background-color: #0d9488; color: white;">
                                                <i class="fa-solid fa-file-pdf"></i>
                                            </a>
                                            <a href="{{ route('alumnos.edit', $alumno->idAlumnos) }}" class="btn-icon btn-edit" title="{{ __('Editar') }}">
                                                 <i class="fa-solid fa-pen"></i>
                                            </a>
                                            <button type="button" class="btn-icon btn-delete btn-delete-critical" 
                                                    data-url="{{ route('alumnos.destroy', $alumno->idAlumnos) }}" 
                                                    title="{{ __('Eliminar') }}">
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
            </div>
        </main>
    </div>
        <script>
            const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

            console.log('Carreras disponibles:', @json($carreras->count()), @json($carreras->pluck('Nombre')));




        // LÓGICA DE FILTRADO DINÁMICO DE GRUPOS
        const carreraSelect = document.getElementById('carrera-select');
        const grupoSelect = document.getElementById('grupo-select');

        if (carreraSelect) {
            carreraSelect.addEventListener('change', async function() {
                const carreraId = this.value;
                grupoSelect.innerHTML = '<option value="" disabled selected>{{ __("Cargando grupos...") }}</option>';
                
                try {
                    const response = await fetch(`/api/carreras/${carreraId}/grupos`);
                    const grupos = await response.json();
                    
                    grupoSelect.innerHTML = '<option value="" disabled selected>{{ __("Selecciona un Grupo") }}</option>';
                    grupos.forEach(grupo => {
                        grupoSelect.innerHTML += `<option value="${grupo.idGrupos}">${grupo.Grupo}</option>`;
                    });
                } catch (error) {
                    console.error('Error cargando grupos:', error);
                    grupoSelect.innerHTML = '<option value="" disabled selected>{{ __("Error al cargar") }}</option>';
                }
            });
        }

        // FILTROS AJAX PARA TABLA
        const filterCarrera = document.getElementById('filter-carrera');
        const filterCuatrimestre = document.getElementById('filter-cuatrimestre');
        const filterEstatus = document.getElementById('filter-estatus');
        const clearFilters = document.getElementById('clear-filters');
        const rows = document.querySelectorAll('.alumno-row');

        function applyFilters() {
            const carrera = filterCarrera.value.toLowerCase();
            const cuatri = filterCuatrimestre.value;
            const estatus = filterEstatus.value.toLowerCase();

            rows.forEach(row => {
                const rowCarrera = row.getAttribute('data-carrera').toLowerCase();
                const rowCuatri = row.getAttribute('data-cuatrimestre');
                const rowEstatus = row.getAttribute('data-estatus').toLowerCase();

                let show = true;
                if (carrera && rowCarrera !== carrera) show = false;
                if (cuatri && rowCuatri !== cuatri) show = false;
                if (estatus && rowEstatus !== estatus) show = false;

                row.style.display = show ? '' : 'none';
            });
        }

        if (filterCarrera) {
            [filterCarrera, filterCuatrimestre, filterEstatus].forEach(f => {
                if (f) f.addEventListener('change', applyFilters);
            });
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
                // Si cancela, regresamos el select al valor anterior (si fuera necesario, pero como es SPA-ish o recarga, el refresh lo arregla)
                location.reload(); 
            }
        }

        if (clearFilters) {
            clearFilters.addEventListener('click', () => {
                filterCarrera.value = '';
                filterCuatrimestre.value = '';
                filterEstatus.value = '';
                applyFilters();
            });
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
