

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('Expediente del Alumno') }}</title>
    <link rel="stylesheet" href="{{ asset('expedienteG.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="shortcut icon" href="{{ asset('logo-utn.ico') }}" type="image/x-icon">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body>

    <div class="dashboard-container">
        
        @include('partials.sidebar', ['active' => 'perfil'])

       

        <main class="main-content">
            <header class="student-header">
                <div class="student-profile">
                    <div class="profile-img-container" onclick="document.getElementById('profile-upload').click()">
                        @php
                            $fotoUrl = $alumno->foto_url ?? "https://ui-avatars.com/api/?name=" . urlencode($alumno->Nombre . '+' . $alumno->Apellido) . "&background=10504B&color=fff&size=100";
                        @endphp
                        <img src="{{ $fotoUrl }}" alt="Foto del alumno" class="profile-img" id="profile-display">
                        <div class="change-photo-overlay">
                            <i class="fa-solid fa-camera"></i>
                        </div>
                        <input type="file" id="profile-upload" style="display: none;" accept="image/*">
                    </div>
                    <div class="student-info">
                        <h1> {{ $alumno->Nombre }}</h1>
                        <p class="student-id"><i class="fa-solid fa-id-card"></i> {{ __('Matrícula') }}: <strong>{{ $alumno->Matricula }}</strong></p>
                        <p class="student-career"><i class="fa-solid fa-graduation-cap"></i> {{ $alumno->carreras->first()->Nombre ?? 'Carrera no asignada' }}</p>
                        <p style="margin-top:4px;font-size:13px;color:var(--text-muted);"><i class="fa-solid fa-chalkboard-user"></i> {{ __('Tutor') }}: {{ $alumno->tutor ? $alumno->tutor->Nombre . ' ' . $alumno->tutor->Apellido : __('Sin Asignar') }}</p>
                    </div>
                </div>
                @php
                    $estatus = strtolower($alumno->Estatus ?? 'activo');
                    $estatusLabel = $estatus === 'baja' ? 'Baja' : ($estatus === 'riesgo' ? 'En riesgo' : 'Activo');
                    $estatusStyle = $estatus === 'baja'
                        ? 'background-color:#fee2e2;color:#991b1b;'
                        : ($estatus === 'riesgo' ? 'background-color:#ffedd5;color:#9a3412;' : '');

                    $califs = $alumno->historialAcademico
                        ->map(fn($h) => is_numeric($h->Calificacion) ? (float) $h->Calificacion : null)
                        ->filter(fn($v) => $v !== null);
                    $avg = $califs->count() ? round($califs->avg(), 1) : null;
                    $statusByAvg = $avg !== null ? \App\Models\Alumno::getRiesgoStatus($avg) : 'N/A';
                    $statusByAvgColor = $avg !== null ? \App\Models\Alumno::getRiesgoColor($avg) : '#64748b';
                    $statusByAvgStyle = "background-color:{$statusByAvgColor}15;color:{$statusByAvgColor};border:1px solid {$statusByAvgColor}30;";
                @endphp
                <div class="student-status" style="{{ $estatusStyle }}">
                    {{ __('Estatus') }}: <span>{{ __($estatusLabel) }}</span>
                </div>
            </header>

              

            <div class="dashboard-grid" style="margin-bottom: 25px;">
                
                <div class="card">
                    <h3><i class="fa-solid fa-address-card"></i> {{ __('Información Personal') }}</h3>
                    <ul class="info-list">
                        <li><strong>{{ __('Correo Inst') }}:</strong> {{ $alumno->Correo_inst }}</li>
                        <li><strong>{{ __('Teléfono') }}:</strong> {{ $alumno->Telefono }}</li>
                        <li><strong>{{ __('Cuatrimestre Actual') }}:</strong> {{ $alumno->Cuatrimestre }}</li>
                        <li><strong>{{ __('Grupo') }}:</strong> {{ $alumno->grupo->Grupo ?? __('Sin Asignar') }}</li>
                    </ul>
                </div>



                <div class="card">
                    <h3><i class="fa-solid fa-square-poll-vertical"></i> {{ __('Promedio Final') }}</h3>
                    <div style="display:flex;align-items:center;justify-content:center;flex-direction:column;gap:12px;padding:var(--space-sm) 0;">
                        <div style="font-size:54px;font-weight:900;line-height:1;color:var(--primary-color);">
                            {{ $avg !== null ? $avg : 'N/A' }}
                        </div>
                        <div style="display:inline-flex;align-items:center;padding:8px 14px;border-radius:999px;font-weight:900;font-size:12px;border:1px solid rgba(0,0,0,0.06);{{ $statusByAvgStyle }}">
                            {{ __('Estatus') }}: {{ __($statusByAvg) }}
                        </div>
                        <div style="font-size:12px;color:var(--text-muted);text-align:center;">
                            {{ __('Calculado con todas las calificaciones registradas') }}
                        </div>
                    </div>
                </div>

            </div>

            <div class="card full-width">
                    <h3><i class="fa-solid fa-hand-holding-dollar"></i> {{ __('Mis Becas') }}</h3>
                    <div class="table-responsive">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>{{ __('Nombre') }}</th>
                                    <th>{{ __('Monto') }}</th>
                                    <th>{{ __('Fecha de Asignación') }}</th>
                                    <th>{{ __('Descripción') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($alumno->becas as $beca)
                                <tr>
                                    <td><strong>{{ $beca->Nombre }}</strong></td>
                                    <td>${{ number_format($beca->Monto, 2) }}</td>
                                    <td>{{ \Carbon\Carbon::parse($beca->pivot->Fecha_Asignacion)->format('d/m/Y') }}</td>
                                    <td>{{ $beca->Descripcion }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" style="text-align: center;">{{ __('No tienes becas asignadas.') }}</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

            
        </main>
    </div>

    <script>
        const fileInput = document.getElementById('profile-upload');
        const profileDisplay = document.getElementById('profile-display');

        fileInput.addEventListener('change', async (e) => {
            const file = e.target.files[0];
            if (!file) return;

            if (file.size > 2 * 1024 * 1024) {
                Swal.fire(@json(__('Error')), @json(__('La imagen supera 2MB. Elige una más ligera.')), 'error');
                fileInput.value = '';
                return;
            }

            Swal.fire({
                title: @json(__('Subiendo imagen...')),
                text: @json(__('Por favor espera')),
                allowOutsideClick: false,
                didOpen: () => Swal.showLoading()
            });

            try {
                const formData = new FormData();
                formData.append('photo', file);

                const response = await fetch(@json(route('perfil.foto.update')), {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    },
                    body: formData
                });

                const raw = await response.text();
                let result = null;
                try { result = JSON.parse(raw); } catch (e) { result = null; }

                if (!result || !response.ok || !result.success) {
                    throw new Error((result && result.message) ? result.message : @json(__('El servidor devolvió una respuesta inesperada. Revisa tu sesión e intenta de nuevo.')));
                }

                profileDisplay.src = result.foto_url;
                Swal.fire(@json(__('¡Éxito!')), @json(__('Foto de perfil actualizada.')), 'success');
            } catch (error) {
                Swal.fire(@json(__('Error')), error.message, 'error');
            }
        });
    </script>
</body>
</html>
