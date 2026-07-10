<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('Gestión de Carreras y Grupos') }} | UniAdmin</title>
    <link rel="stylesheet" href="{{ asset('expedienteG.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="shortcut icon" href="{{ asset('logo-utn.ico') }}" type="image/x-icon">
</head>
<body>

    <div class="dashboard-container">
        @include('partials.sidebar', ['active' => 'carreras'])

        <main class="main-content">
            <header class="student-header">
                <div class="student-profile" style="width: 100%; display: flex; justify-content: space-between; align-items: center;">
                    <div class="student-info">
                        <h1>{{ __('Catálogos: Carreras y Grupos') }}</h1>
                        <p class="student-id">{{ __('Gestión estructura académica') }}</p>
                    </div>
                    <div class="header-actions">
                        <button class="btn-primary" onclick="showModal('carrera')"><i class="fa-solid fa-plus"></i> {{ __('Nueva Carrera') }}</button>
                    </div>
                </div>
            </header>

            @if(session('success'))
                <div class="alert alert-success" style="background-color: #d4edda; color: #155724; padding: 10px; border-radius: 5px; margin-bottom: 20px;">
                    {{ session('success') }}
                </div>
            @endif

            <div class="dashboard-grid">
                <!-- COLUMNA CARRERAS -->
                <div class="card full-width">
                    <h3><i class="fa-solid fa-graduation-cap"></i> {{ __('Gestión de Carreras') }}</h3>
                    
                    <div class="table-responsive">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>{{ __('Nombre de la Carrera') }}</th>
                                    <th>{{ __('Acciones') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($carreras as $carrera)
                                <tr>
                                    <td><strong>{{ $carrera->Nombre }}</strong></td>
                                    <td>
                                        <div class="action-buttons">
                                            <a href="{{ route('materias.show', $carrera->idCarreras) }}" class="btn-icon btn-view" title="{{ __('Plan Educativo') }}" style="background-color: #10504B; color: white; display: inline-flex; align-items: center; justify-content: center; width: 35px; height: 35px; border-radius: 8px; text-decoration: none;">
                                                 <i class="fa-solid fa-book-bookmark"></i>
                                            </a>
                                            <button type="button" class="btn-icon btn-edit" title="{{ __('Editar') }}" 
                                                    onclick="showEditCarreraModal('{{ $carrera->idCarreras }}', '{{ $carrera->Nombre }}')"
                                                    style="display: inline-flex; align-items: center; justify-content: center; width: 35px; height: 35px; border-radius: 8px; text-decoration: none; border: none; cursor: pointer;">
                                                 <i class="fa-solid fa-pen"></i>
                                            </button>
                                            <button type="button" class="btn-icon btn-delete btn-delete-critical" 
                                                    data-url="{{ route('carreras.destroy', $carrera->idCarreras) }}"
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
                        text: '{{ __("Eliminando carrera...") }}',
                        icon: 'success',
                        timer: 1500,
                        showConfirmButton: false,
                        willClose: () => submitWithMethod(url, 'DELETE')
                    });
                }
            });
        });

        function showEditCarreraModal(id, nombre) {
            Swal.fire({
                title: '{{ __("Editar Carrera") }}',
                html: `
                    <div style="text-align: center; padding: 10px;">
                        <div style="background: #fff7ed; padding: 15px; border-radius: 12px; margin-bottom: 20px; border: 1px solid #ffedd5;">
                            <i class="fa-solid fa-pen-to-square" style="font-size: 2.5rem; color: #9a3412; margin-bottom: 10px;"></i>
                            <p style="color: #9a3412; font-weight: 600; margin-bottom: 5px;">{{ __("Modificación de Carrera") }}</p>
                            <p style="color: #374151; font-size: 0.85rem; margin: 0;">{{ __("Actualiza el nombre de la carrera") }} <strong>${nombre}</strong>.</p>
                        </div>
                        <div style="text-align: left;">
                            <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #374151;">{{ __("Nuevo Nombre") }}</label>
                            <input id="swal-edit-carrera-nombre" class="swal2-input" value="${nombre}" style="width: 100%; margin: 0; padding: 12px; border-radius: 8px; border: 1px solid #d1d5db; box-sizing: border-box;">
                        </div>
                    </div>
                `,
                showCancelButton: true,
                confirmButtonText: '{{ __("Guardar Cambios") }}',
                cancelButtonText: '{{ __("Cancelar") }}',
                confirmButtonColor: '#10504B',
                cancelButtonColor: '#6b7280',
                width: '500px',
                padding: '1.5rem',
                focusConfirm: false,
                preConfirm: () => {
                    const nuevoNombre = document.getElementById('swal-edit-carrera-nombre').value.trim();
                    if (!nuevoNombre) {
                        Swal.showValidationMessage('{{ __("El nombre es obligatorio") }}');
                        return false;
                    }
                    return { nombre: nuevoNombre };
                }
            }).then(async (result) => {
                if (result.isConfirmed) {
                    // Primero pedimos el token de seguridad
                    const ok = await promptTokenAndActivate();
                    if (ok) {
                        const form = document.createElement('form');
                        form.method = 'POST';
                        form.action = `/carreras/${id}`;
                        
                        const csrfInput = document.createElement('input');
                        csrfInput.type = 'hidden';
                        csrfInput.name = '_token';
                        csrfInput.value = csrfToken;
                        form.appendChild(csrfInput);

                        const methodInput = document.createElement('input');
                        methodInput.type = 'hidden';
                        methodInput.name = '_method';
                        methodInput.value = 'PATCH';
                        form.appendChild(methodInput);
                        
                        const nameInput = document.createElement('input');
                        nameInput.type = 'hidden';
                        nameInput.name = 'Nombre';
                        nameInput.value = result.value.nombre;
                        form.appendChild(nameInput);
                        
                        document.body.appendChild(form);
                        
                        Swal.fire({
                            title: '{{ __("¡Autorizado!") }}',
                            text: '{{ __("Actualizando carrera...") }}',
                            icon: 'success',
                            timer: 1500,
                            showConfirmButton: false,
                            willClose: () => form.submit()
                        });
                    }
                }
            });
        }

        function showModal(type) {
            if (type === 'carrera') {
                Swal.fire({
                    title: '{{ __("Nueva Carrera") }}',
                    html: `
                        <div style="text-align: center; padding: 10px;">
                            <div style="background: #f0fdf4; padding: 15px; border-radius: 12px; margin-bottom: 20px; border: 1px solid #bbf7d0;">
                                <i class="fa-solid fa-graduation-cap" style="font-size: 2.5rem; color: #10504B; margin-bottom: 10px;"></i>
                                <p style="color: #166534; font-weight: 600; margin-bottom: 5px;">{{ __("Registro de Oferta Académica") }}</p>
                                <p style="color: #374151; font-size: 0.85rem; margin: 0;">{{ __("Ingresa el nombre oficial de la nueva carrera para el sistema.") }}</p>
                            </div>
                            <div style="text-align: left;">
                                <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #374151;">{{ __("Nombre de la Carrera") }}</label>
                                <input id="swal-carrera-nombre" class="swal2-input" placeholder="{{ __("Ej. Ingeniería en Software") }}" style="width: 100%; margin: 0; padding: 12px; border-radius: 8px; border: 1px solid #d1d5db; box-sizing: border-box;">
                            </div>
                        </div>
                    `,
                    showCancelButton: true,
                    confirmButtonText: '{{ __("Guardar Carrera") }}',
                    cancelButtonText: '{{ __("Cancelar") }}',
                    confirmButtonColor: '#10504B',
                    cancelButtonColor: '#6b7280',
                    width: '500px',
                    padding: '1.5rem',
                    focusConfirm: false,
                    preConfirm: () => {
                        const nombre = document.getElementById('swal-carrera-nombre').value.trim();
                        if (!nombre) {
                            Swal.showValidationMessage('{{ __("El nombre es obligatorio") }}');
                            return false;
                        }
                        return { nombre: nombre };
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        const form = document.createElement('form');
                        form.method = 'POST';
                        form.action = "{{ route('carreras.store') }}";
                        
                        const csrfInput = document.createElement('input');
                        csrfInput.type = 'hidden';
                        csrfInput.name = '_token';
                        csrfInput.value = csrfToken;
                        form.appendChild(csrfInput);
                        
                        const nameInput = document.createElement('input');
                        nameInput.type = 'hidden';
                        nameInput.name = 'Nombre';
                        nameInput.value = result.value.nombre;
                        form.appendChild(nameInput);
                        
                        document.body.appendChild(form);
                        form.submit();
                    }
                });
            } else if (type === 'grupo') {
                Swal.fire({
                    title: '{{ __("Nuevo Grupo") }}',
                    html: `
                        <form id="grupo-form" action="{{ route('grupos.store') }}" method="POST">
                            @csrf
                            <div style="text-align: left; margin-bottom: 15px;">
                                <label style="display: block; margin-bottom: 5px; font-weight: bold;">{{ __("Nombre / Código del Grupo") }}</label>
                                <input type="text" name="Grupo" class="swal2-input" placeholder="{{ __("Ej. 1A, 2B, Matutino") }}" required style="width: 100%;">
                            </div>
                            <div style="text-align: left; margin-bottom: 15px;">
                                <label style="display: block; margin-bottom: 5px; font-weight: bold;">{{ __("Capacidad de Alumnos") }}</label>
                                <input type="number" name="Cantidad_Alumnos" class="swal2-input" placeholder="{{ __("Ej. 30") }}" required style="width: 100%;" min="1" max="50">
                            </div>
                            <div style="text-align: left; margin-bottom: 15px;">
                                <label style="display: block; margin-bottom: 5px; font-weight: bold;">{{ __("Carrera") }}</label>
                                <select name="idCarreras" class="swal2-select" style="width: 100%;" required id="carrera-select">
                                    <option value="" disabled selected>{{ __("Selecciona Carrera") }}</option>
                                    @foreach($carreras as $carrera)
                                        <option value="{{ $carrera->idCarreras }}">{{ $carrera->Nombre }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div style="text-align: left; margin-bottom: 15px;">
                                <label style="display: block; margin-bottom: 5px; font-weight: bold;">{{ __("Tutor Asignado (Opcional)") }}</label>
                                <select name="idTutores" class="swal2-select" style="width: 100%;" id="tutor-select">
                                    <option value="">{{ __("Sin Tutor") }}</option>
                                    @foreach($tutores as $tutor)
                                        <option value="{{ $tutor->idTutores }}" data-carrera="{{ $tutor->idCarreras ?? '' }}">{{ $tutor->Nombre }} {{ $tutor->Apellido }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </form>
                    `,
                    confirmButtonText: '{{ __("Guardar Grupo") }}',
                    showCancelButton: true,
                    width: '600px',
                    preConfirm: () => {
                        const form = document.getElementById('grupo-form');
                        if (!form.Grupo.value.trim() || !form.Cantidad_Alumnos.value || !form.idCarreras.value) {
                            Swal.showValidationMessage('{{ __("Todos los campos requeridos deben ser completados") }}');
                            return false;
                        }
                        form.submit();
                    },
                    didOpen: () => {
                        // Filtrar tutores por carrera seleccionada
                        document.getElementById('carrera-select').addEventListener('change', function() {
                            const carreraId = this.value;
                            const tutorSelect = document.getElementById('tutor-select');
                            
                            // Limpiar opciones
                            tutorSelect.innerHTML = '<option value="">{{ __("Sin Tutor") }}</option>';
                            
                            // Agregar tutores filtrados
                            @foreach($tutores as $tutor)
                                if (carreraId == '{{ $tutor->idCarreras }}') {
                                    tutorSelect.innerHTML += '<option value="{{ $tutor->idTutores }}">{{ $tutor->Nombre }} {{ $tutor->Apellido }}</option>';
                                }
                            @endforeach
                        });
                    }
                });
            }
        }
    </script>
</body>
</html>
