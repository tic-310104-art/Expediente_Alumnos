<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('Plan Educativo') }} | {{ $carrera->Nombre }}</title>
    <link rel="stylesheet" href="{{ asset('expedienteG.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="shortcut icon" href="{{ asset('logo-utn.ico') }}" type="image/x-icon">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body>

    <div class="dashboard-container">
        @include('partials.sidebar', ['active' => 'carreras'])

        <main class="main-content">
            <header class="student-header">
                <div class="student-profile" style="width: 100%; display: flex; justify-content: space-between; align-items: center;">
                    <div class="student-info">
                        <h1>{{ __('Plan Educativo') }}: {{ $carrera->Nombre }}</h1>
                        <p class="student-id"><i class="fa-solid fa-graduation-cap"></i> {{ __('Configuración de Materias y Grupos') }}</p>
                    </div>
                    <div class="header-actions">
                        <button class="btn-primary" onclick="showModal('grupo')" style="background-color: #a17171;">
                            <i class="fa-solid fa-plus"></i> {{ __('Nuevo Grupo') }}
                        </button>
                        <a href="{{ route('carreras.index') }}" class="btn-secondary" style="text-decoration: none; padding: 10px 20px; border-radius: 8px; display: inline-flex; align-items: center; gap: 8px;">
                            <i class="fa-solid fa-arrow-left"></i> {{ __('Volver') }}
                        </a>
                    </div>
                </div>
            </header>

            @if(session('success'))
                <div class="alert alert-success" style="background-color: #d4edda; color: #155724; padding: 15px; border-radius: 8px; margin-bottom: 20px; border: 1px solid #c3e6cb;">
                    <i class="fa-solid fa-check-circle"></i> {{ session('success') }}
                </div>
            @endif

            <div class="dashboard-grid">
                <div class="card full-width">
                    <h3 style="border-bottom: 1px solid var(--border-color); padding-bottom: 10px; margin-bottom: 20px;">
                        <i class="fa-solid fa-users-rectangle"></i> {{ __('Grupos de esta Carrera') }}
                    </h3>
                    
                    <div class="table-responsive">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>{{ __('Nombre / Código de Grupo') }}</th>
                                    <th>{{ __('Tutor') }}</th>
                                    <th>{{ __('Capacidad') }}</th>
                                    <th>{{ __('Carga Académica') }}</th>
                                    <th>{{ __('Acciones') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($carrera->grupos as $grupo)
                                <tr>
                                    <td><strong>{{ $grupo->Grupo }}</strong></td>
                                    <td>{{ $grupo->tutor ? $grupo->tutor->Nombre . ' ' . $grupo->tutor->Apellido : __('Sin Tutor') }}</td>
                                    <td>{{ $grupo->Cantidad_Alumnos }} {{ __('Alumnos') }}</td>
                                    <td style="min-width: 300px;">
                                        @if($grupo->materias->count() > 0)
                                            <button class="btn-secondary" style="padding: 5px 10px; font-size: 11px; margin-bottom: 8px; display: inline-flex; align-items: center; gap: 5px; cursor: pointer; border-radius: 6px;" onclick="toggleIndividualCarga('carga-{{ $grupo->idGrupos }}', this)">
                                                <i class="fa-solid fa-eye-slash"></i> <span>{{ __('Ocultar Carga') }}</span>
                                            </button>
                                            
                                            <div id="carga-{{ $grupo->idGrupos }}" style="font-size: 13px; color: var(--text-main); display: flex; flex-direction: column; gap: 8px; transition: all 0.3s ease;">
                                                @foreach($grupo->materias as $m)
                                                    <div style="padding: 8px 12px; border: 1px solid var(--border-color); border-radius: 8px; background: rgba(var(--primary-rgb, 16, 80, 75), 0.03); border-left: 4px solid var(--primary-color);">
                                                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 4px;">
                                                            <strong style="color: var(--primary-color);">{{ $m->Nombre }}</strong>
                                                            <span style="font-size: 11px; background: var(--primary-color); color: white; padding: 2px 6px; border-radius: 4px;">{{ $m->Cuatrimestre }}°</span>
                                                        </div>
                                                        <div style="font-size: 12px; color: var(--text-muted); display: flex; gap: 15px;">
                                                            <span><i class="fa-solid fa-user-tie" style="margin-right: 5px;"></i> {{ $m->pivot->Maestro ?: __('Sin Maestro') }}</span>
                                                            <span><i class="fa-solid fa-clock" style="margin-right: 5px;"></i> {{ $m->pivot->Horario ?: __('Sin Horario') }}</span>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @else
                                            <div style="text-align: center; padding: 10px; border: 1px dashed #ef4444; border-radius: 8px; color: #ef4444; font-size: 12px;">
                                                <i class="fa-solid fa-triangle-exclamation"></i> {{ __('Sin Carga Académica') }}
                                            </div>
                                        @endif
                                    </td>
                                        <td>
                                            <div class="action-buttons">
                                                <a href="{{ route('grupos.carga', $grupo->idGrupos) }}" class="btn-icon btn-view" title="{{ __('Gestionar Carga') }}" style="background-color: #f59e0b; color: white; display: inline-flex; align-items: center; justify-content: center; width: 35px; height: 35px; border-radius: 8px; text-decoration: none;">
                                                     <i class="fa-solid fa-list-check"></i>
                                                </a>
                                                <a href="{{ route('grupos.edit', $grupo->idGrupos) }}" class="btn-icon btn-edit" title="{{ __('Editar') }}" style="display: inline-flex; align-items: center; justify-content: center; width: 35px; height: 35px; border-radius: 8px; text-decoration: none;">
                                                     <i class="fa-solid fa-pen"></i>
                                                </a>
                                                <button type="button" class="btn-icon btn-delete btn-delete-critical" 
                                                        data-url="{{ route('grupos.destroy', $grupo->idGrupos) }}"
                                                        title="{{ __('Eliminar') }}" 
                                                        style="display: inline-flex; align-items: center; justify-content: center; width: 35px; height: 35px; border-radius: 8px; text-decoration: none;">
                                                    <i class="fa-solid fa-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="5" style="text-align: center; padding: 20px; color: var(--text-muted);">{{ __('No hay grupos registrados para esta carrera.') }}</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Formulario Nueva Materia -->
                <div class="card full-width">
                    <h3><i class="fa-solid fa-plus-circle"></i> {{ __('Agregar Materia al Plan') }}</h3>
                    <form action="{{ route('materias.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="idCarreras" value="{{ $carrera->idCarreras }}">
                        
                        <div class="form-grid" style="grid-template-columns: 2fr 1fr auto;">
                            <div class="form-group">
                                <label>{{ __('Nombre de la Materia') }}</label>
                                <input type="text" name="Nombre" class="form-control" placeholder="{{ __('Ej. Base de Datos Avanzada') }}" required>
                            </div>
                            <div class="form-group">
                                <label>{{ __('Cuatrimestre Sugerido') }}</label>
                                <select name="Cuatrimestre" class="form-control" required>
                                    @for($i=1; $i<=12; $i++)
                                        <option value="{{ $i }}">{{ $i }}° {{ __('Cuatrimestre') }}</option>
                                    @endfor
                                </select>
                            </div>
                            <div class="form-actions" style="margin-top:25px;">
                                <button type="submit" class="btn-primary" style="width: auto;">
                                    <i class="fa-solid fa-save"></i> {{ __('Guardar Materia') }}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- Listado de Materias por Cuatrimestre -->
                <div class="card full-width">
                    <h3><i class="fa-solid fa-list-check"></i> {{ __('Materias Registradas en el Plan') }}</h3>
                    
                    @php 
                        $materiasGrouped = $carrera->materias->groupBy('Cuatrimestre')->sortKeys();
                    @endphp

                    @forelse($materiasGrouped as $cuatri => $materias)
                        <div class="cuatrimestre-group" style="margin-bottom: 30px;">
                            <h4 style="background: #2b7a78; color: white; padding: 8px 15px; border-radius: 5px; display: inline-block;">
                                {{ $cuatri }}° {{ __('Cuatrimestre') }}
                            </h4>
                            <div class="table-responsive" style="margin-top: 10px;">
                                <table class="data-table">
                                    <thead>
                                        <tr>
                                            <th>{{ __('Materia') }}</th>
                                            <th style="width: 100px;">{{ __('Acciones') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($materias as $materia)
                                        <tr>
                                            <td><strong>{{ $materia->Nombre }}</strong></td>
                                            <td>
                                                <button type="button" class="btn-delete-critical" 
                                                        data-url="{{ route('materias.destroy', $materia->idMateria) }}"
                                                        style="background:none; border:none; color: #991b1b; cursor:pointer; font-size: 1.2rem;" 
                                                        title="{{ __('Eliminar') }}">
                                                    <i class="fa-solid fa-circle-xmark"></i>
                                                </button>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @empty
                        <p style="text-align: center; padding: 20px; color: #666;">{{ __('Aún no se han registrado materias para este plan de estudios.') }}</p>
                    @endforelse
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
                        text: '{{ __("Eliminando grupo...") }}',
                        icon: 'success',
                        timer: 1500,
                        showConfirmButton: false,
                        willClose: () => submitWithMethod(url, 'DELETE')
                    });
                }
            });
        });

        function toggleIndividualCarga(id, btn) {
            const content = document.getElementById(id);
            const span = btn.querySelector('span');
            const icon = btn.querySelector('i');
            
            if (content.style.display === 'none') {
                content.style.display = 'flex';
                span.innerText = '{{ __("Ocultar Carga") }}';
                icon.className = 'fa-solid fa-eye-slash';
            } else {
                content.style.display = 'none';
                span.innerText = '{{ __("Ver Carga") }}';
                icon.className = 'fa-solid fa-eye';
            }
        }

        function showModal(type) {
            if (type === 'grupo') {
                Swal.fire({
                    title: '{{ __("Nuevo Grupo") }}',
                    html: `
                        <div style="text-align: center; padding: 10px;">
                            <div style="background: #eff6ff; padding: 15px; border-radius: 12px; margin-bottom: 20px; border: 1px solid #bfdbfe;">
                                <i class="fa-solid fa-users-rectangle" style="font-size: 2.5rem; color: #1e40af; margin-bottom: 10px;"></i>
                                <p style="color: #1e40af; font-weight: 600; margin-bottom: 5px;">{{ __("Apertura de Nuevo Grupo") }}</p>
                                <p style="color: #374151; font-size: 0.85rem; margin: 0;">{{ __("Define los detalles del grupo para") }} <strong>{{ $carrera->Nombre }}</strong>.</p>
                            </div>
                            <div style="text-align: left; margin-bottom: 15px;">
                                <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #374151;">{{ __("Nombre / Código del Grupo") }}</label>
                                <input id="swal-grupo-nombre" class="swal2-input" placeholder="{{ __("Ej. 1A, 2B") }}" style="width: 100%; margin: 0; padding: 12px; border-radius: 8px; border: 1px solid #d1d5db; box-sizing: border-box;">
                            </div>
                            <div style="text-align: left; margin-bottom: 15px;">
                                <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #374151;">{{ __("Capacidad Máxima") }}</label>
                                <input id="swal-grupo-capacidad" type="number" class="swal2-input" placeholder="{{ __("Ej. 30") }}" min="1" max="50" style="width: 100%; margin: 0; padding: 12px; border-radius: 8px; border: 1px solid #d1d5db; box-sizing: border-box;">
                            </div>
                            <div style="text-align: left; margin-bottom: 15px;">
                                <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #374151;">{{ __("Tutor Asignado (Opcional)") }}</label>
                                <select id="swal-grupo-tutor" class="swal2-select" style="width: 100%; margin: 0; display: block; padding: 12px; border-radius: 8px; border: 1px solid #d1d5db; box-sizing: border-box;">
                                    <option value="">{{ __("Sin Tutor") }}</option>
                                    @foreach($tutores as $tutor)
                                        <option value="{{ $tutor->idTutores }}">{{ $tutor->Nombre }} {{ $tutor->Apellido }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    `,
                    showCancelButton: true,
                    confirmButtonText: '{{ __("Guardar Grupo") }}',
                    cancelButtonText: '{{ __("Cancelar") }}',
                    confirmButtonColor: '#10504B',
                    cancelButtonColor: '#6b7280',
                    width: '500px',
                    padding: '1.5rem',
                    focusConfirm: false,
                    preConfirm: () => {
                        const nombre = document.getElementById('swal-grupo-nombre').value.trim();
                        const capacidad = document.getElementById('swal-grupo-capacidad').value;
                        const tutorId = document.getElementById('swal-grupo-tutor').value;
                        
                        if (!nombre || !capacidad) {
                            Swal.showValidationMessage('{{ __("Nombre y capacidad son obligatorios") }}');
                            return false;
                        }
                        return { nombre, capacidad, tutorId };
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        const form = document.createElement('form');
                        form.method = 'POST';
                        form.action = "{{ route('grupos.store') }}";
                        
                        const csrfInput = document.createElement('input');
                        csrfInput.type = 'hidden';
                        csrfInput.name = '_token';
                        csrfInput.value = csrfToken;
                        form.appendChild(csrfInput);
                        
                        const carreraInput = document.createElement('input');
                        carreraInput.type = 'hidden';
                        carreraInput.name = 'idCarreras';
                        carreraInput.value = "{{ $carrera->idCarreras }}";
                        form.appendChild(carreraInput);
                        
                        const nameInput = document.createElement('input');
                        nameInput.type = 'hidden';
                        nameInput.name = 'Grupo';
                        nameInput.value = result.value.nombre;
                        form.appendChild(nameInput);
                        
                        const capInput = document.createElement('input');
                        capInput.type = 'hidden';
                        capInput.name = 'Cantidad_Alumnos';
                        capInput.value = result.value.capacidad;
                        form.appendChild(capInput);
                        
                        const tutorInput = document.createElement('input');
                        tutorInput.type = 'hidden';
                        tutorInput.name = 'idTutores';
                        tutorInput.value = result.value.tutorId;
                        form.appendChild(tutorInput);
                        
                        document.body.appendChild(form);
                        form.submit();
                    }
                });
            }
        }
    </script>
</body>
</html>
