<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Asesorías | Panel de Tutoría</title>
    <link rel="stylesheet" href="{{ asset('expedienteG.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="shortcut icon" href="{{ asset('logo-utn.ico') }}" type="image/x-icon">
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
                        <h1>Módulo: Asesorías Académicas</h1>
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
                <!-- Formulario para agendar asesoría -->
                <div class="card full-width">
                    <h3><i class="fa-solid fa-book-open-reader"></i> Programar Nueva Asesoría</h3>
                    <form action="{{ route('asesorias.store') }}" method="POST">
                        @csrf
                        <div class="form-grid">
                            <div class="form-group">
                                <label>Alumnos Invitados</label>
                                <div style="display:flex; gap:10px; margin-bottom:10px;">
                                    <input id="alumnos-search" type="text" class="form-control" placeholder="Buscar alumno..." style="flex:1;">
                                    <button type="button" id="toggle-all" class="btn-secondary" style="white-space:nowrap;">Seleccionar todo</button>
                                </div>
                                <div id="alumnos-list" style="border:1px solid var(--border-color); border-radius:10px; padding:10px; max-height:180px; overflow:auto; background: var(--bg-color);">
                                    @foreach($tutor->alumnos as $alumno)
                                        <label class="alumno-item" style="display:flex; align-items:center; gap:10px; padding:6px 8px; border-radius:8px; cursor:pointer;">
                                            <input type="checkbox" name="Alumno_id[]" value="{{ $alumno->idAlumnos }}">
                                            <span style="font-weight:600; color: var(--text-color);">{{ $alumno->Matricula }}</span>
                                            <span style="color: var(--text-muted);">{{ $alumno->Nombre }} {{ $alumno->Apellido }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                            <div class="form-group">
                                <label>Fecha y Hora</label>
                                <input type="datetime-local" name="Fecha" class="form-control" required>
                            </div>
                            <div class="form-group" style="grid-column: 1 / -1;">
                                <label>Motivo / Tema de la Asesoría</label>
                                <textarea name="Motivo" class="form-control" placeholder="Ej. Refuerzo en programación orientada a objetos..." rows="2" required></textarea>
                            </div>
                            <div class="form-actions" style="grid-column: 1 / -1;">
                                <button type="submit" class="btn-primary" style="width: auto;"><i class="fa-solid fa-save"></i> Registrar Asesoría</button>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- Lista de asesorías -->
                <div class="card full-width">
                    <h3><i class="fa-solid fa-history"></i> Historial de Asesorías</h3>
                    <div class="table-responsive">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Fecha</th>
                                    <th>Tema / Motivo</th>
                                    <th>Alumnos Participantes</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($asesorias as $asesoria)
                                <tr>
                                    <td><strong>{{ \Carbon\Carbon::parse($asesoria->Fecha)->format('d/m/Y h:i A') }}</strong></td>
                                    <td>{{ $asesoria->Motivo }}</td>
                                    <td>
                                        @foreach($asesoria->alumnos as $alumno)
                                            <span class="badge" style="background-color: #e0f2fe; color: #0369a1; padding: 2px 6px; border-radius: 4px; font-size: 0.75em; margin-right: 2px;">{{ $alumno->Nombre }}</span>
                                        @endforeach
                                    </td>
                                    <td>
                                        <div class="action-buttons" style="display: flex; gap: 5px;">
                                            <form action="{{ route('asesorias.destroy', $asesoria->idAsesoria) }}" method="POST" onsubmit="return confirm('¿Eliminar registro de asesoría?');">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="btn-icon btn-delete"><i class="fa-solid fa-trash"></i></button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" style="text-align: center;">No hay registros de asesorías académicas.</td>
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
        const searchInput = document.getElementById('alumnos-search');
        const list = document.getElementById('alumnos-list');
        const toggleAll = document.getElementById('toggle-all');

        function getItems() {
            return Array.from(list.querySelectorAll('.alumno-item'));
        }

        function isAllChecked(visibleItems) {
            return visibleItems.length > 0 && visibleItems.every(item => item.querySelector('input[type="checkbox"]').checked);
        }

        searchInput.addEventListener('input', () => {
            const q = searchInput.value.toLowerCase().trim();
            getItems().forEach(item => {
                const text = item.textContent.toLowerCase();
                item.style.display = text.includes(q) ? '' : 'none';
            });
            const visible = getItems().filter(i => i.style.display !== 'none');
            toggleAll.textContent = isAllChecked(visible) ? 'Quitar selección' : 'Seleccionar todo';
        });

        toggleAll.addEventListener('click', () => {
            const visible = getItems().filter(i => i.style.display !== 'none');
            const shouldCheck = !isAllChecked(visible);
            visible.forEach(item => {
                item.querySelector('input[type="checkbox"]').checked = shouldCheck;
            });
            toggleAll.textContent = shouldCheck ? 'Quitar selección' : 'Seleccionar todo';
        });

        list.addEventListener('change', () => {
            const visible = getItems().filter(i => i.style.display !== 'none');
            toggleAll.textContent = isAllChecked(visible) ? 'Quitar selección' : 'Seleccionar todo';
        });
    </script>
</body>
</html>
