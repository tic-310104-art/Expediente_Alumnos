<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrar Cuenta</title>
    <link rel="stylesheet" href="register.css">
     <link rel="shortcut icon" href="logo-utn.ico" type="image/x-icon">

</head>
<body>
    <div class="container">
        
        <div class="logo-container">
            <img src="logo-utn.png" alt="Logo" class="logo-img">
        </div>

        <div class="form-container">
            <h2>Registrar Cuenta</h2>
            <form>
                <div class="input-group">
                    <input type="text" id="reg-name" required placeholder=" ">
                    <label for="reg-name">Nombre Completo</label>
                </div>
                <div class="input-group">
                    <input type="email" id="reg-email" required placeholder=" ">
                    <label for="reg-email">Correo Electrónico</label>
                </div>
                <div class="input-group">
                    <input type="password" id="reg-password" required placeholder=" ">
                    <label for="reg-password">Contraseña</label>
                </div>
                <div class="input-group">
                    <input type="password" id="reg-confirm" required placeholder=" ">
                    <label for="reg-confirm">Confirmar Contraseña</label>
                </div>
                <button type="submit" class="btn">Registrarse</button>
            </form>
            <p class="toggle-text">¿Ya tienes cuenta? <a href="{{ asset('/sesion') }}">Inicia Sesión</a></p>
        </div>
    </div>
</body>
</html>