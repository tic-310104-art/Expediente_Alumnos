<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('Gestión de Tutores') }}</title>
    <link rel="stylesheet" href="{{ asset('expedienteG.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="shortcut icon" href="{{ asset('logo-utn.ico') }}" type="image/x-icon">
</head>
<body>

    <div class="dashboard-container">
        @include('partials.sidebar', ['active' => 'tutores'])

        <main class="main-content">
            <header class="student-header">
                <div class="student-profile">
                    <div class="student-info">
                        <h1>{{ __('Gestión de Tutores') }}</h1>
                        <p class="student-id">{{ __('Control del personal académico') }}</p>
                    </div>
                </div>
            </header>

            @if(session('success'))
                <div class="alert alert-success" style="background-color: #d4edda; color: #155724; padding: 15px; border-radius: 8px; margin-bottom: 20px; border: 1px solid #c3e6cb;">
                    <i class="fa-solid fa-check-circle"></i> {{ session('success') }}
                </div>
            @endif

            <div class="dashboard-grid">

                <!-- FORMULARIO -->
                <div class="card full-width">
                    <h3><i class="fa-solid fa-chalkboard-user"></i> {{ __('Formulario de Tutor') }}</h3>
                    <form action="{{ route('tutores.store') }}" method="POST">
                        @csrf

                        <div class="form-grid">

                            <div class="form-group">
                                <label>{{ __('Clave Trabajador') }}</label>
                                <input type="text" name="Clave_Trabajador" class="form-control" placeholder="Ej. 123456" required>
                            </div>

                            <div class="form-group">
                                <label>{{ __('Nombre') }}</label>
                                <input type="text" name="Nombre" class="form-control" placeholder="{{ __('Nombre') }}" required>
                            </div>

                            <div class="form-group">
                                <label>{{ __('Apellido') }}</label>
                                <input type="text" name="Apellido" class="form-control" placeholder="{{ __('Apellido') }}" required>
                            </div>

                            <div class="form-group">
                                <label>{{ __('Correo Institucional') }}</label>
                                <input type="email" name="Correo_inst" class="form-control" placeholder="correo@utnay.edu.mx" required>
                            </div>

                            <div class="form-group">
                                <label>{{ __('Contraseña') }}</label>
                                <input type="password" name="Password" class="form-control" placeholder="{{ __('Contraseña del tutor') }}" required>
                            </div>

                            <div class="form-group">
                                <label>{{ __('Teléfono') }}</label>
                                <input type="text" name="Telefono" class="form-control" placeholder="+52 311...">
                            </div>

                            <div class="form-group">
                                <label>{{ __('Carrera Asignada') }}</label>
                                <select name="idCarreras" class="form-control" required>
                                    <option value="" disabled selected>{{ __('Selecciona una Carrera') }}</option>
                                    @foreach($carreras as $carrera)
                                        <option value="{{ $carrera->idCarreras }}">{{ $carrera->Nombre }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <input type="hidden" name="Rol" value="Tutor">

                            <div class="form-actions">
                                <button type="reset" class="btn-secondary">{{ __('Limpiar') }}</button>
                                <button type="submit" class="btn-primary">
                                    <i class="fa-solid fa-save"></i> {{ __('Guardar Tutor') }}
                                </button>
                            </div>

                        </div>
                    </form>
                </div>

                <!-- TABLA -->
                <div class="card full-width">
                    <h3><i class="fa-solid fa-list"></i> {{ __('Lista de Tutores') }}</h3>
                    
                    {{-- FILTROS --}}
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
                        <div class="filter-group" style="display: flex; align-items: flex-end;">
                            <button id="clear-filters" class="btn-secondary" style="width: 100%;">{{ __('Limpiar Filtros') }}</button>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="data-table" id="tutores-table">
                            <thead>
                                <tr>
                                    <th>{{ __('Clave') }}</th>
                                    <th>{{ __('Nombre') }}</th>
                                    <th>{{ __('Apellido') }}</th>
                                    <th>{{ __('Carrera') }}</th>
                                    <th>{{ __('Grupos') }}</th>
                                    <th>{{ __('Tutorados') }}</th>
                                    <th>{{ __('Correo') }}</th>
                                    <th>{{ __('Teléfono') }}</th>
                                    <th>{{ __('Acciones') }}</th>
                                </tr>
                            </thead>

                            <tbody>
                                @foreach($tutores as $tutor)
                                <tr class="tutor-row" 
                                    data-carrera="{{ $tutor->carrera ? $tutor->carrera->Nombre : '' }}">
                                    <td><a href="{{ route('tutor.dashboard', $tutor->idTutores) }}" style="color:#2b7a78; font-weight:bold; text-decoration:none;">{{ $tutor->Clave_Trabajador }}</a></td>
                                    <td>{{ $tutor->Nombre }}</td>
                                    <td> {{ $tutor->Apellido }}</td>
                                    <td>{{ $tutor->carrera ? $tutor->carrera->Nombre : __('Sin Carrera') }}</td>
                                    <td>
                                        @forelse($tutor->grupos as $grupo)
                                            <span class="badge" style="background: #2b7a78; color: white; padding: 4px 8px; border-radius: 6px; font-size: 11px; margin: 2px; display: inline-block;">
                                                {{ $grupo->Grupo }}
                                            </span>
                                        @empty
                                            <span style="font-size: 11px; color: #999;">{{ __('Sin Grupos') }}</span>
                                        @endforelse
                                    </td>
                                    <td>
                                        <span class="badge" style="background: #10504B; color: white; padding: 4px 8px; border-radius: 12px; font-size: 11px;">
                                            {{ $tutor->alumnos->count() }} {{ __('Alumnos') }}
                                        </span>
                                    </td>
                                    <td>{{ $tutor->Correo_inst }}</td>
                                    <td>{{ $tutor->Telefono }}</td>

                                    <td>
                                        <div class="action-buttons">
                                            <!-- ASIGNAR GRUPO -->
                                            <button class="btn-icon btn-view" title="{{ __('Asignar Grupo') }}" 
                                                    style="background-color: #2b7a78; color: white;"
                                                    onclick="showAssignModal({{ $tutor->idTutores }}, '{{ $tutor->Nombre }} {{ $tutor->Apellido }}', {{ $tutor->idCarreras ?? 'null' }})">
                                                <i class="fa-solid fa-users-rectangle"></i>
                                            </button>

                                            <!-- EDITAR -->
                                            <a href="{{ route('tutores.edit', $tutor->idTutores) }}" class="btn-icon btn-edit" title="{{ __('Editar') }}">
                                                <i class="fa-solid fa-pen"></i>
                                            </a>

                                            <!-- ELIMINAR -->
                                            <button type="button" class="btn-icon btn-delete btn-delete-critical" 
                                                    data-url="{{ route('tutores.destroy', $tutor->idTutores) }}" 
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
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script>
        const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
        const allGrupos = @json($grupos);

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

        function showAssignModal(tutorId, tutorName, carreraId) {
            let optionsHtml = '<option value="" disabled selected>{{ __("Buscar grupo...") }}</option>';
            
            // Filtrar grupos que pertenezcan a la carrera del tutor
            const filteredGrupos = allGrupos.filter(g => g.idCarreras == carreraId);

            if (filteredGrupos.length > 0) {
                filteredGrupos.forEach(grupo => {
                    const carreraNombre = grupo.carrera ? grupo.carrera.Nombre : '{{ __("Sin Carrera") }}';
                    optionsHtml += `<option value="${grupo.idGrupos}">${grupo.Grupo} — ${carreraNombre}</option>`;
                });
            } else {
                optionsHtml = '<option value="" disabled selected>{{ __("No hay grupos disponibles para esta carrera") }}</option>';
            }

            Swal.fire({
                title: '{{ __("Asignar Grupo a") }} ' + tutorName,
                html: `
                    <div style="text-align: left; padding: 10px;">
                        <label style="display: block; margin-bottom: 10px; font-weight: 600; color: #374151;">{{ __("Selecciona el Grupo") }}</label>
                        <select id="swal-idGrupos" class="swal2-select" style="width: 100%; margin: 0; display: block; padding: 12px; border-radius: 8px; border: 1px solid #d1d5db; box-sizing: border-box;">
                            ${optionsHtml}
                        </select>
                        <p style="margin-top: 10px; font-size: 12px; color: #6b7280; line-height: 1.4;">
                            <i class="fa-solid fa-circle-info" style="margin-right: 4px;"></i>
                            {{ __('Al asignar el grupo, todos sus alumnos serán vinculados automáticamente a este tutor.') }}
                        </p>
                    </div>
                `,
                showCancelButton: true,
                confirmButtonText: '{{ __("Asignar") }}',
                cancelButtonText: '{{ __("Cancelar") }}',
                confirmButtonColor: '#10504B',
                cancelButtonColor: '#6b7280',
                width: '500px',
                padding: '1.5rem',
                focusConfirm: false,
                preConfirm: () => {
                    const idGrupos = document.getElementById('swal-idGrupos').value;
                    if (!idGrupos) {
                        Swal.showValidationMessage('{{ __("Debes seleccionar un grupo") }}');
                        return false;
                    }
                    return { idGrupos: idGrupos };
                }
            }).then(async (result) => {
                if (result.isConfirmed) {
                    const ok = await promptTokenAndActivate();
                    if (!ok) return;

                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = "{{ route('tutores.assign') }}";
                    
                    const csrfInput = document.createElement('input');
                    csrfInput.type = 'hidden';
                    csrfInput.name = '_token';
                    csrfInput.value = "{{ csrf_token() }}";
                    form.appendChild(csrfInput);
                    
                    const tutorInput = document.createElement('input');
                    tutorInput.type = 'hidden';
                    tutorInput.name = 'idTutores';
                    tutorInput.value = tutorId;
                    form.appendChild(tutorInput);
                    
                    const grupoInput = document.createElement('input');
                    grupoInput.type = 'hidden';
                    grupoInput.name = 'idGrupos';
                    grupoInput.value = result.value.idGrupos;
                    form.appendChild(grupoInput);
                    
                    document.body.appendChild(form);
                    
                    Swal.fire({
                        title: '{{ __("¡Autorizado!") }}',
                        text: '{{ __("Asignando grupo...") }}',
                        icon: 'success',
                        timer: 1500,
                        showConfirmButton: false,
                        willClose: () => form.submit()
                    });
                }
            });
        }

        // FILTROS PARA TABLA DE TUTORES
        const filterCarrera = document.getElementById('filter-carrera');
        const clearFilters = document.getElementById('clear-filters');
        const rows = document.querySelectorAll('.tutor-row');

        function applyFilters() {
            const carrera = filterCarrera.value.toLowerCase();

            rows.forEach(row => {
                const rowCarrera = row.getAttribute('data-carrera').toLowerCase();
                let show = true;
                
                if (carrera && rowCarrera !== carrera) show = false;

                row.style.display = show ? '' : 'none';
            });
        }

        filterCarrera.addEventListener('change', applyFilters);

        clearFilters.addEventListener('click', () => {
            filterCarrera.value = '';
            applyFilters();
        });

        // ACCIÓN CRÍTICA: ELIMINACIÓN CON TOKEN
        document.querySelectorAll('.btn-delete-critical').forEach(button => {
            button.addEventListener('click', async function() {
                const url = this.getAttribute('data-url');

                const ok = await promptTokenAndActivate();
                if (ok) {
                    Swal.fire({
                        title: '{{ __("¡Autorizado!") }}',
                        text: '{{ __("Eliminando tutor...") }}',
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
