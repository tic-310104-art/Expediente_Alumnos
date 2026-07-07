<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Acceso Denegado — Sistema de Expedientes</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Inter', sans-serif;
            min-height: 100vh;
            background: linear-gradient(135deg, #0f0c29 0%, #302b63 50%, #24243e 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            overflow: hidden;
        }

        /* Animated background blobs */
        .blob {
            position: fixed;
            border-radius: 50%;
            filter: blur(80px);
            opacity: 0.3;
            animation: float 8s ease-in-out infinite;
            pointer-events: none;
        }
        .blob-1 { width: 400px; height: 400px; background: #e94560; top: -100px; left: -100px; animation-delay: 0s; }
        .blob-2 { width: 300px; height: 300px; background: #7b2ff7; bottom: -80px; right: -80px; animation-delay: 3s; }
        .blob-3 { width: 200px; height: 200px; background: #f5a623; top: 50%; left: 60%; animation-delay: 5s; }

        @keyframes float {
            0%, 100% { transform: translateY(0) scale(1); }
            50% { transform: translateY(-30px) scale(1.05); }
        }

        .card {
            background: rgba(255, 255, 255, 0.07);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.12);
            border-radius: 24px;
            padding: 56px 48px;
            text-align: center;
            max-width: 480px;
            width: 90%;
            position: relative;
            z-index: 10;
            animation: slideUp 0.5s ease-out;
            box-shadow: 0 32px 80px rgba(0,0,0,0.4);
        }

        @keyframes slideUp {
            from { opacity: 0; transform: translateY(40px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        .icon-wrapper {
            width: 96px;
            height: 96px;
            background: linear-gradient(135deg, rgba(233,69,96,0.3), rgba(123,47,247,0.3));
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 28px;
            border: 1px solid rgba(233,69,96,0.4);
        }

        .icon-wrapper svg {
            width: 48px;
            height: 48px;
            color: #e94560;
        }

        .badge {
            display: inline-block;
            background: rgba(233, 69, 96, 0.2);
            border: 1px solid rgba(233, 69, 96, 0.4);
            color: #e94560;
            font-size: 12px;
            font-weight: 600;
            letter-spacing: 2px;
            text-transform: uppercase;
            padding: 6px 16px;
            border-radius: 100px;
            margin-bottom: 20px;
        }

        h1 {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 12px;
            background: linear-gradient(135deg, #fff, #a0a0c0);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        p {
            color: rgba(255,255,255,0.6);
            font-size: 1rem;
            line-height: 1.6;
            margin-bottom: 36px;
        }

        .btn-primary {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 14px 32px;
            background: linear-gradient(135deg, #7b2ff7, #e94560);
            color: #fff;
            text-decoration: none;
            border-radius: 12px;
            font-weight: 600;
            font-size: 0.95rem;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
            box-shadow: 0 8px 24px rgba(123,47,247,0.3);
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 32px rgba(123,47,247,0.5);
        }

        .divider {
            height: 1px;
            background: rgba(255,255,255,0.1);
            margin: 28px 0;
        }

        .user-info {
            font-size: 0.85rem;
            color: rgba(255,255,255,0.4);
        }
        .user-info strong {
            color: rgba(255,255,255,0.7);
        }
    </style>
</head>
<body>
    <div class="blob blob-1"></div>
    <div class="blob blob-2"></div>
    <div class="blob blob-3"></div>

    <div class="card">
        <div class="icon-wrapper">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" />
            </svg>
        </div>

        <div class="badge">Error 403</div>

        <h1>Acceso Denegado</h1>

        <p>No tienes los permisos necesarios para ver esta página. Si crees que esto es un error, por favor contacta al administrador del sistema.</p>

        @auth
            <a href="{{ match(auth()->user()->role) {
                'admin'  => route('expedienteGeneral'),
                'tutor'  => route('tutor.dashboard', ['id' => optional(auth()->user()->tutor)->idTutores]),
                default  => route('alumno.dashboard', ['id' => optional(auth()->user()->alumno)->idAlumnos])
            } }}" class="btn-primary">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" style="width:18px;height:18px;">
                    <path fill-rule="evenodd" d="M9.293 2.293a1 1 0 0 1 1.414 0l7 7A1 1 0 0 1 17 11h-1v6a1 1 0 0 1-1 1h-2a1 1 0 0 1-1-1v-3a1 1 0 0 0-1-1H9a1 1 0 0 0-1 1v3a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1v-6H3a1 1 0 0 1-.707-1.707l7-7Z" clip-rule="evenodd" />
                </svg>
                Ir a mi Dashboard
            </a>

            <div class="divider"></div>
            <div class="user-info">
                Sesión activa como: <strong>{{ auth()->user()->name }}</strong>
                &nbsp;·&nbsp;
                Rol: <strong>{{ ucfirst(auth()->user()->role) }}</strong>
            </div>
        @else
            <a href="{{ route('login') }}" class="btn-primary">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" style="width:18px;height:18px;">
                    <path fill-rule="evenodd" d="M3 4.25A2.25 2.25 0 0 1 5.25 2h5.5A2.25 2.25 0 0 1 13 4.25v2a.75.75 0 0 1-1.5 0v-2a.75.75 0 0 0-.75-.75h-5.5a.75.75 0 0 0-.75.75v11.5c0 .414.336.75.75.75h5.5a.75.75 0 0 0 .75-.75v-2a.75.75 0 0 1 1.5 0v2A2.25 2.25 0 0 1 10.75 18h-5.5A2.25 2.25 0 0 1 3 15.75V4.25Z" clip-rule="evenodd" />
                    <path fill-rule="evenodd" d="M6 10a.75.75 0 0 1 .75-.75h9.546l-1.048-.943a.75.75 0 1 1 1.004-1.114l2.5 2.25a.75.75 0 0 1 0 1.114l-2.5 2.25a.75.75 0 1 1-1.004-1.114l1.048-.943H6.75A.75.75 0 0 1 6 10Z" clip-rule="evenodd" />
                </svg>
                Iniciar Sesión
            </a>
        @endauth
    </div>
</body>
</html>
