<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('Registrar Cuenta') }}</title>
    <link rel="stylesheet" href="{{ asset('register.css') }}">
     <link rel="shortcut icon" href="{{ asset('logo-utn.ico') }}" type="image/x-icon">

</head>
<body>
    <div class="container">
        
        <div class="logo-container">
            <img src="{{ asset('imgs/utn.png') }}" alt="{{ __('Logo') }}" class="logo-img">
        </div>

        <div class="form-container">
            <h2>{{ __('Registrar Cuenta') }}</h2>

            @if ($errors->any())
                <div class="alert alert-danger" style="color: #f44336; margin-bottom: 20px; font-size: 0.8em; text-align: left;">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('register.post') }}" method="POST">
                @csrf
                <div style="display: flex; gap: 10px;">
                    <div class="input-group">
                        <input type="text" name="Nombre" id="reg-nombre" required placeholder=" " value="{{ old('Nombre') }}">
                        <label for="reg-nombre">{{ __('Nombre(s)') }}</label>
                    </div>
                    <div class="input-group">
                        <input type="text" name="Apellido" id="reg-apellido" required placeholder=" " value="{{ old('Apellido') }}">
                        <label for="reg-apellido">{{ __('Apellido(s)') }}</label>
                    </div>
                </div>
                <div class="input-group">
                    <input type="text" name="Matricula" id="reg-matricula" required placeholder=" " value="{{ old('Matricula') }}">
                    <label for="reg-matricula">{{ __('Matrícula Escolar') }}</label>
                </div>
                <div class="input-group">
                    <input type="email" name="email" id="reg-email" required placeholder=" " value="{{ old('email') }}">
                    <label for="reg-email">{{ __('Correo Institucional') }}</label>
                </div>
                <div class="input-group">
                    <input type="password" name="password" id="reg-password" required placeholder=" ">
                    <label for="reg-password">{{ __('Contraseña') }}</label>
                </div>
                <div class="input-group">
                    <input type="password" name="password_confirmation" id="reg-confirm" required placeholder=" ">
                    <label for="reg-confirm">{{ __('Confirmar Contraseña') }}</label>
                </div>
                <button type="submit" class="btn">{{ __('Registrarse') }}</button>
            </form>
            <p class="toggle-text">{{ __('¿Ya tienes cuenta?') }} <a href="{{ route('login') }}">{{ __('Inicia Sesión') }}</a></p>
        </div>
    </div>
</body>
</html>
