@php
    $user = Auth::user();
    $role = $user->role;
    $title = __("UniAdmin");
    if($role === 'admin') $title = __("Servicios Escolares");
    elseif($role === 'tutor') $title = __("Panel Tutor");
    elseif($role === 'alumno') $title = __("Panel Alumno");
@endphp

<aside class="sidebar" id="sidebar">
    <div class="sidebar-logo">
        <img src="{{ asset('imgs/utn.png') }}" alt="{{ __('Logo Institucional') }}" class="custom-sidebar-logo">
        <span>{{ $title }}</span>
    </div>
    
    <nav class="sidebar-nav">
        {{-- SELECTOR DE IDIOMA Y MODO OSCURO --}}
        <div class="nav-extra-controls" style="padding: 10px 25px; display: flex; gap: 15px; align-items: center; border-bottom: 1px solid rgba(255,255,255,0.1); margin-bottom: 10px;">
            <div class="language-selector" style="display: flex; gap: 5px;">
                <a href="{{ route('set-locale', 'es') }}" title="Español" style="color: {{ app()->getLocale() == 'es' ? '#fff' : 'rgba(255,255,255,0.5)' }}; text-decoration: none; font-weight: bold; font-size: 14px;">ES</a>
                <span style="color: rgba(255,255,255,0.3)">|</span>
                <a href="{{ route('set-locale', 'en') }}" title="English" style="color: {{ app()->getLocale() == 'en' ? '#fff' : 'rgba(255,255,255,0.5)' }}; text-decoration: none; font-weight: bold; font-size: 14px;">EN</a>
            </div>
            <div class="dark-mode-toggle" style="margin-left: auto; display: flex; gap: 15px; align-items: center;">
                @if(auth()->user()->role === 'admin')

                <a class="notification-bell" href="{{ route('logs.index') }}" style="position: relative; cursor: pointer; display: inline-flex; text-decoration: none;">
                    <i class="fa-solid fa-bell" style="color: rgba(255,255,255,0.8); font-size: 18px;"></i>
                    @php
                        $lastSeen = (int) (session('logs_last_seen_id') ?? 0);
                        $newLogs = \App\Models\LogActivity::where('id','>', $lastSeen)->count();
                    @endphp
                    @if($newLogs > 0)
                    <span style="position: absolute; top: -5px; right: -5px; background: #ef4444; color: white; border-radius: 50%; min-width: 16px; height: 16px; padding: 0 4px; font-size: 10px; display: inline-flex; align-items: center; justify-content: center;">{{ $newLogs }}</span>
                    @endif
                </a>
                @endif
                @if(auth()->user()->role === 'tutor')
                <button id="calendar-toggle" class="btn-icon" style="color: rgba(255,255,255,0.8); background: none; border: none; cursor: pointer; font-size: 18px;">
                    <i class="fa-solid fa-calendar-days"></i>
                </button>
                @endif
                <button id="theme-toggle" class="btn-icon" style="color: rgba(255,255,255,0.8); background: none; border: none; cursor: pointer; font-size: 18px;">
                    <i class="fa-solid fa-moon"></i>
                </button>
            </div>
        </div>

        {{-- MENU PARA ADMINISTRADOR --}}
        @if($role === 'admin')
            <a href="/expedienteGeneral" class="nav-item {{ ($active ?? '') == 'resumen' ? 'active' : '' }}">
                <i class="fa-solid fa-chart-pie"></i> {{ __('General') }}
            </a>
            <a href="{{ route('alumnos.index') }}" class="nav-item {{ ($active ?? '') == 'alumnos' ? 'active' : '' }}">
                <i class="fa-solid fa-user-graduate"></i> {{ __('Gestión de Alumnos') }}
            </a>
            <a href="{{ route('tutores.index') }}" class="nav-item {{ ($active ?? '') == 'tutores' ? 'active' : '' }}">
                <i class="fa-solid fa-chalkboard-user"></i> {{ __('Gestión de Tutores') }}
            </a>
            <a href="{{ route('servicios.index') }}" class="nav-item {{ ($active ?? '') == 'admins' ? 'active' : '' }}">
                <i class="fa-solid fa-shield-halved"></i> {{ __('Gestión Administradores') }}
            </a>
            <a href="{{ route('carreras.index') }}" class="nav-item {{ ($active ?? '') == 'carreras' ? 'active' : '' }}">
                <i class="fa-solid fa-layer-group"></i> {{ __('Carreras y Grupos') }}
            </a>
            <a href="{{ route('becas.index') }}" class="nav-item {{ ($active ?? '') == 'becas' ? 'active' : '' }}">
                <i class="fa-solid fa-hand-holding-dollar"></i> {{ __('Becas') }}
            </a>
        @endif

        {{-- MENU PARA TUTOR --}}
        @if($role === 'tutor')
            @php $tutorId = $user->tutor->idTutores ?? 0; @endphp
            <a href="{{ route('tutor.dashboard', $tutorId) }}" class="nav-item {{ ($active ?? '') == 'inicio' ? 'active' : '' }}">
                <i class="fa-solid fa-house"></i> {{ __('Mi Inicio') }}
            </a>
        @endif

        {{-- MENU PARA ALUMNO --}}
        @if($role === 'alumno')
            @php $alumnoId = $user->alumno->idAlumnos ?? 0; @endphp
            <a href="{{ route('alumno.dashboard', $alumnoId) }}" class="nav-item {{ ($active ?? '') == 'perfil' ? 'active' : '' }}">
                <i class="fa-solid fa-user"></i> {{ __('Mi Perfil') }}
            </a>
            <a href="{{ route('alumno.citas', $alumnoId) }}" class="nav-item {{ ($active ?? '') == 'citas' ? 'active' : '' }}">
                <i class="fa-solid fa-calendar-check"></i> {{ __('Mis Citas') }}
            </a>
            <a href="{{ route('alumno.reporte', $alumnoId) }}" class="nav-item {{ ($active ?? '') == 'desempeno' ? 'active' : '' }}">
                <i class="fa-solid fa-chart-line"></i> {{ __('Mi Desempeño') }}
            </a>
            <a href="{{ route('alumno.psicologia', $alumnoId) }}" class="nav-item {{ ($active ?? '') == 'psicologia' ? 'active' : '' }}">
                <i class="fa-solid fa-brain"></i> {{ __('Psicología') }}
            </a>
            <a href="{{ route('alumno.asesorias', $alumnoId) }}" class="nav-item {{ ($active ?? '') == 'asesorias' ? 'active' : '' }}">
                <i class="fa-solid fa-chalkboard-user"></i> {{ __('Mis Asesorías') }}
            </a>
        @endif
    </nav>

    <div class="sidebar-footer">
        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
            @csrf
        </form>
        <a href="#" class="nav-item" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
            <i class="fa-solid fa-arrow-right-from-bracket"></i> {{ __('Salir') }}
        </a>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // --- Lógica de Menú Hamburguesa Profesional (Mobile) ---
            const sidebar = document.getElementById('sidebar');
            
            // Crear el Header de Mobile
            const mobileHeader = document.createElement('div');
            mobileHeader.className = 'mobile-header';
            mobileHeader.innerHTML = `
                <div class="mobile-header-logo">
                    <img src="{{ asset('imgs/utn.png') }}" alt="Logo">
                    <span>{{ $title }}</span>
                </div>
                <button class="mobile-toggle">
                    <i class="fa-solid fa-bars"></i>
                </button>
            `;
            document.body.prepend(mobileHeader);

            const mobileToggle = mobileHeader.querySelector('.mobile-toggle');
            const overlay = document.createElement('div');
            overlay.className = 'sidebar-overlay';
            document.body.appendChild(overlay);

            function toggleSidebar() {
                sidebar.classList.toggle('active');
                overlay.classList.toggle('active');
                mobileToggle.innerHTML = sidebar.classList.contains('active') 
                    ? '<i class="fa-solid fa-xmark"></i>' 
                    : '<i class="fa-solid fa-bars"></i>';
            }

            mobileToggle.addEventListener('click', toggleSidebar);
            overlay.addEventListener('click', toggleSidebar);

            // Cerrar al hacer clic en un item
            const navItems = sidebar.querySelectorAll('.nav-item');
            navItems.forEach(item => {
                item.addEventListener('click', () => {
                    if (window.innerWidth <= 900) {
                        sidebar.classList.remove('active');
                        overlay.classList.remove('active');
                        mobileToggle.innerHTML = '<i class="fa-solid fa-bars"></i>';
                    }
                });
            });

            // --- Lógica del Tema (Modo Oscuro) ---
            const themeToggle = document.getElementById('theme-toggle');
            const body = document.body;
            const icon = themeToggle ? themeToggle.querySelector('i') : null;

            // Cargar preferencia
            if (localStorage.getItem('theme') === 'dark') {
                body.classList.add('dark-mode');
                if (icon) icon.classList.replace('fa-moon', 'fa-sun');
            }

            if (themeToggle) {
                themeToggle.addEventListener('click', () => {
                    body.classList.toggle('dark-mode');
                    const isDark = body.classList.contains('dark-mode');
                    localStorage.setItem('theme', isDark ? 'dark' : 'light');
                    
                    if (isDark) {
                        if (icon) icon.classList.replace('fa-moon', 'fa-sun');
                    } else {
                        if (icon) icon.classList.replace('fa-sun', 'fa-moon');
                    }
                });
            }

            // --- Lógica de Autorización para Acciones Críticas ---
            window.promptTokenAndActivate = async function() {
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
                
                return Swal.fire({
                    title: '{{ __("Confirmación de Seguridad") }}',
                    text: '{{ __("¿Estás seguro de que deseas realizar esta acción crítica?") }}',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: '{{ __("Sí, continuar") }}',
                    cancelButtonText: '{{ __("Cancelar") }}',
                    confirmButtonColor: '#10504B',
                    showLoaderOnConfirm: true,
                    preConfirm: () => {
                        return fetch("{{ route('jwt.verify') }}", {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': csrfToken,
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({}) // El controlador usará el token de la sesión automáticamente
                        })
                        .then(response => {
                            if (!response.ok) return response.json().then(json => { throw new Error(json.message) });
                            return response.json();
                        })
                        .catch(error => Swal.showValidationMessage(`${error.message}`));
                    }
                }).then(result => result.isConfirmed);
            };

            window.submitWithMethod = function(url, method) {
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
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
            };
        });
    </script>
</aside>
