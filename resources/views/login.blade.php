<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inicio de Sesion</title>
    <link rel="stylesheet" href="login.css">
     <link rel="shortcut icon" href="logo-utn.ico" type="image/x-icon">

</head>
<body>
    <div class="container">
        
        <div class="logo-container">
            <img src="logo-utn.png" alt="Logo" class="logo-img">
        </div>

        <div class="form-container" id="login-form">
            <h2>Iniciar Sesión</h2>
            <form>
                <div class="input-group">
                    <input type="email" id="login-email" required placeholder=" ">
                    <label for="login-email">Correo Electrónico</label>
                </div>
                <div class="input-group">
                    <input type="password" id="login-password" required placeholder=" ">
                    <label for="login-password">Contraseña</label>
                </div>
                <button type="submit" class="btn">Iniciar</button>
            </form>
            <p class="toggle-text">¿No tienes cuenta? <a href="{{ asset('/Registro') }}">Regístrate</a></p>
        </div>
    </div>
</body>
</html>