<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inicio de Sesión</title>
    <link rel="stylesheet" href="{{ asset('login.css') }}">
     <link rel="shortcut icon" href="{{ asset('logo-utn.ico') }}" type="image/x-icon">

</head>
<body>
    <div class="auth-shell">
        <div class="auth-card">
            <section class="auth-visual" aria-hidden="true">
                <div class="visual-content">
                </div>
                <div class="visual-footer">
                </div>
            </section>

            <section class="auth-form">
                <div class="form-container" id="login-form">
                    <h2>Iniciar Sesión</h2>
                    
                    @if ($errors->any())
                        <div class="alert alert-danger" style="color: #f44336; margin-bottom: 20px; font-size: 0.9em;">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('login.post') }}" method="POST">
                        @csrf
                        <div class="input-group">
                            <input type="email" name="email" id="login-email" required placeholder=" " value="{{ old('email') }}">
                            <label for="login-email">Correo Electrónico</label>
                        </div>
                        <div class="input-group">
                            <input type="password" name="password" id="login-password" required placeholder=" ">
                            <label for="login-password">Contraseña</label>
                        </div>
                        <button type="submit" class="btn">Iniciar</button>
                    </form>
                </div>
            </section>
        </div>
    </div>
</body>
</html>
