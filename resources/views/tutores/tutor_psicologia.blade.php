<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión Citas Psicología | Panel de Tutoría</title>
    <link rel="stylesheet" href="{{ asset('expedienteG.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="shortcut icon" href="{{ asset('logo-utn.ico') }}" type="image/x-icon">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        .badge-status {
            display: inline-flex;
            align-items: center;
            padding: 4px 10px;
            border-radius: 999px;
            font-weight: 700;
            font-size: 12px;
            border: 1px solid rgba(0,0,0,0.06);
        }
        .badge-asistio { background: #d1fae5; color: #065f46; }
        .badge-no { background: #fee2e2; color: #991b1b; }
        .badge-pendiente { background: #fef3c7; color: #92400e; }
    </style>
</head>
<body>

    <div class="dashboard-container">
        @include('partials.sidebar', ['active' => 'none'])

        <main class="main-content">
            <header class="student-header">
                <div class="student-profile">
                    @php
                        $tutorFoto = $tutor->foto_url ?? "https://ui-avatars.com/api/?name=" . urlencode($tutor->Nombre . '+' . $tutor->Apellido) . "&background=10504B&color=fff&size=100";
                    @endphp
                    <img src="{{ $tutorFoto }}" alt="Foto del tutor" class="profile-img">
                    <div class="student-info">
                        <h1>Módulo: Citas de Psicología</h1>
                        <p class="student-id"><i class="fa-solid fa-chalkboard-user"></i> Tutor: <strong>{{ $tutor->Nombre }} {{ $tutor->Apellido }}</strong></p>
                    </div>
                </div>
            </header>

            @if(session('success'))
                <div class="alert alert-success" style="background-color: #d4edda; color: #155724; padding: 10px; border-radius: 5px; margin-bottom: 20px;">
                    {{ session('success') }}
                </div>
            @endif

            <div class="dashboard-grid">
                <!-- Formulario para agendar -->
                <div class="card full-width">
                    <h3><i class="fa-solid fa-calendar-plus"></i> Agendar Cita en Psicología</h3>
                    <form action="{{ route('citas-psicologia.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="Tutores_id" value="{{ $tutor->idTutores }}">
                        <div class="form-grid">
                            <div class="form-group">
                                <label>Alumno a Canalizar</label>
                                <select name="Alumno_id" class="form-control" required>
                                     <option value="" disabled {{ !request('alumno_id') ? 'selected' : '' }}>Selecciona un alumno...</option>
                                     @foreach($tutor->alumnos as $alumno)
                                         <option value="{{ $alumno->idAlumnos }}" {{ (request('alumno_id') == $alumno->idAlumnos || old('Alumno_id') == $alumno->idAlumnos) ? 'selected' : '' }}>{{ $alumno->Matricula }} - {{ $alumno->Nombre }} {{ $alumno->Apellido }}</option>
                                     @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Fecha y Hora</label>
                                <input type="datetime-local" name="Fecha" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label>Estatus / Asistencia Inicial</label>
                                <select name="Asistencia" class="form-control" required>
                                    <option value="Pendiente" selected>Pendiente</option>
                                    <option value="Asistió">Asistió</option>
                                    <option value="No Asistió">No Asistió</option>
                                </select>
                            </div>
                            <div class="form-actions" style="grid-column: 1 / -1;">
                                <button type="submit" class="btn-primary" style="width: auto;"><i class="fa-solid fa-save"></i> Agendar en Psicología</button>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- Lista de citas -->
                <div class="card full-width">
                    <h3><i class="fa-solid fa-list-check"></i> Registro de Canalizaciones</h3>
                    <div class="table-responsive">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Fecha</th>
                                    <th>Alumno</th>
                                    <th>Asistencia</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($citas as $cita)
                                <tr>
                                    <td><strong>{{ \Carbon\Carbon::parse($cita->Fecha)->format('d/m/Y h:i A') }}</strong></td>
                                    <td>
                                        @php 
                                            // Fallback for cases where many-to-many might fail
                                            $first = $cita->alumnos->first(); 
                                            // Maybe it was saved differently? (just in case they have a legacy column)
                                            $alumnoId_direct = $cita->Alumno_id ?? null;
                                        @endphp
                                        @if($first)
                                            {{ $first->Nombre }} {{ $first->Apellido }}
                                        @elseif($alumnoId_direct)
                                            @php $aDir = \App\Models\Alumno::find($alumnoId_direct); @endphp
                                            {{ $aDir ? ($aDir->Nombre . ' ' . $aDir->Apellido) : 'No asignado' }}
                                        @else
                                             No asignado
                                        @endif
                                    </td>
                                    <td>
                                        @php
                                            $badgeClass = $cita->Asistencia == 'Asistió' ? 'badge-asistio' : ($cita->Asistencia == 'No Asistió' ? 'badge-no' : 'badge-pendiente');
                                        @endphp
                                        <span class="badge-status {{ $badgeClass }}">{{ $cita->Asistencia }}</span>
                                    </td>
                                    <td>
                                        <div class="action-buttons" style="display: flex; gap: 5px;">
                                            @php
                                                $first = $cita->alumnos->first();
                                                if (!$first && isset($cita->Alumno_id)) {
                                                    $first = \App\Models\Alumno::find($cita->Alumno_id);
                                                }
                                                $alumnoLabel = $first ? ($first->Matricula . ' - ' . $first->Nombre . ' ' . $first->Apellido) : 'No asignado';
                                                $alumnoId = $first ? $first->idAlumnos : '';
                                                $fechaLocal = \Carbon\Carbon::parse($cita->Fecha)->format('Y-m-d\\TH:i');
                                            @endphp
                                            <button type="button"
                                                class="btn-icon btn-edit js-edit-cita"
                                                data-id="{{ $cita->idCita }}"
                                                data-update-url="{{ route('citas-psicologia.update', $cita->idCita) }}"
                                                data-alumno-id="{{ $alumnoId }}"
                                                data-alumno-label="{{ $alumnoLabel }}"
                                                data-fecha="{{ $fechaLocal }}"
                                                data-asistencia="{{ $cita->Asistencia }}">
                                                <i class="fa-solid fa-pen"></i>
                                            </button>
                                            <form action="{{ route('citas-psicologia.destroy', $cita->idCita) }}" method="POST" onsubmit="return confirm('¿Eliminar cita?');">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="btn-icon btn-delete"><i class="fa-solid fa-trash"></i></button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" style="text-align: center;">No hay citas de psicología registradas.</td>
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
        function openEditCitaModal(payload) {
            const csrf = document.querySelector('meta[name="csrf-token"]').content;
            const asistencia = payload.asistencia || 'Pendiente';
            const html = `
                <div style="text-align:left;">
                    <div style="margin-bottom:10px; padding:10px 12px; border:1px solid #e5e7eb; border-radius:12px; background:#f8fafc;">
                        <div style="font-size:12px; color:#64748b; margin-bottom:4px;">Alumno</div>
                        <div style="font-weight:700; color:#0f172a;">${payload.alumnoLabel || 'No asignado'}</div>
                    </div>
                    <label style="display:block; font-weight:700; margin-bottom:6px;">Fecha y Hora</label>
                    <input id="swalFecha" type="datetime-local" value="${payload.fecha || ''}" style="width:100%; padding:10px 12px; border:1px solid #e5e7eb; border-radius:10px; margin-bottom:12px;">
                    <label style="display:block; font-weight:700; margin-bottom:6px;">Asistencia</label>
                    <select id="swalAsistencia" style="width:100%; padding:10px 12px; border:1px solid #e5e7eb; border-radius:10px;">
                        <option value="Pendiente" ${asistencia === 'Pendiente' ? 'selected' : ''}>Pendiente</option>
                        <option value="Asistió" ${asistencia === 'Asistió' ? 'selected' : ''}>Asistió</option>
                        <option value="No Asistió" ${asistencia === 'No Asistió' ? 'selected' : ''}>No Asistió</option>
                    </select>
                </div>
            `;

            Swal.fire({
                title: 'Editar Cita de Psicología',
                html,
                showCancelButton: true,
                confirmButtonText: 'Guardar cambios',
                cancelButtonText: 'Cancelar',
                showLoaderOnConfirm: true,
                preConfirm: async () => {
                    const fecha = document.getElementById('swalFecha').value;
                    const asistenciaValue = document.getElementById('swalAsistencia').value;
                    if (!fecha) {
                        Swal.showValidationMessage('Selecciona una fecha y hora');
                        return;
                    }

                    const resp = await fetch(payload.updateUrl, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrf,
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({
                            _method: 'PUT',
                            Alumno_id: payload.alumnoId,
                            Fecha: fecha,
                            Asistencia: asistenciaValue
                        })
                    });

                    if (!resp.ok) {
                        let msg = 'No se pudo guardar';
                        try {
                            const json = await resp.json();
                            msg = json.message || msg;
                        } catch (e) {}
                        throw new Error(msg);
                    }
                    return true;
                },
                allowOutsideClick: () => !Swal.isLoading()
            }).then((r) => {
                if (r.isConfirmed) window.location.href = window.location.pathname;
            }).catch((e) => {
                Swal.fire('Error', e.message, 'error');
            });
        }

        document.querySelectorAll('.js-edit-cita').forEach(btn => {
            btn.addEventListener('click', () => {
                openEditCitaModal({
                    id: btn.dataset.id,
                    updateUrl: btn.dataset.updateUrl,
                    alumnoId: btn.dataset.alumnoId,
                    alumnoLabel: btn.dataset.alumnoLabel,
                    fecha: btn.dataset.fecha,
                    asistencia: btn.dataset.asistencia
                });
            });
        });

        const params = new URLSearchParams(window.location.search);
        const toEdit = params.get('edit_cita_psicologia');
        if (toEdit) {
            const target = document.querySelector(`.js-edit-cita[data-id="${toEdit}"]`);
            if (target) target.click();
        }
    </script>
</body>
</html>
