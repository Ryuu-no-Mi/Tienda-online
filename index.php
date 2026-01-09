<?php

/**
 * Página principal - Login y Registro
 * CAPA CLIENTE
 */

session_start();

// Incluir configuración y conexión a BD
include 'includes/config.php';

// Incluir funciones
include 'includes/funciones.php';

// Si el usuario ya está logeado, redirigir
if (isset($_SESSION['usuario_id'])) {
    if ($_SESSION['rol'] === 'administrador') {
        header('Location: admin/dashboard.php');
    } else {
        header('Location: public/catalogo.php');
    }
    exit;
}

// Procesar login
$login_error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    $usuario = $_POST['usuario'];
    $contraseña = $_POST['contraseña'];
    $resultado_login = iniciarSesion($usuario, $contraseña);

    if (isset($resultado_login['exito']) && $resultado_login['exito']) {
        $_SESSION['usuario_id'] = $resultado_login['usuario']['id'];
        $_SESSION['usuario'] = $resultado_login['usuario']['usuario'];
        $_SESSION['rol'] = $resultado_login['usuario']['rol'];
        $_SESSION['email'] = $resultado_login['usuario']['email'];

        if ($_SESSION['rol'] === 'administrador') {
            header('Location: admin/dashboard.php');
        } else {
            header('Location: public/catalogo.php');
        }
        exit;
    } else {
        $login_error = $resultado_login['error'];
    }
}

// Procesar registro
$registro_error = '';
$registro_success = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['registro'])) {
    $usuario = $_POST['usuario'];
    $email = $_POST['email'];
    $contraseña = $_POST['contraseña'];
    $registro_error = registrarUsuario($usuario, $email, $contraseña); // Asegúrate de que esta función esté definida en funciones.php

    if (empty($registro_error)) {
        $registro_success = 'Registro exitoso. Puedes iniciar sesión.';
    }
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tienda de Zapatillas - Login</title>
    <link rel="stylesheet" href="assets/css/styles-login.css">
</head>

<body>
    <div class="container">
        <!-- Sección de Login -->
        <div class="form-section active" id="login-section">
            <div class="brand">
                <h1>👟 Tienda de Zapatillas</h1>
                <p>Tu tienda online de zapatillas deportivas</p>
            </div>

            <?php if ($login_error): ?>
                <div class="error"><?php echo htmlspecialchars($login_error); ?></div>
            <?php endif; ?>

            <form method="POST">
                <div class="form-group">
                    <label for="usuario">Usuario</label>
                    <input type="text" id="usuario" name="usuario" required>
                </div>

                <div class="form-group">
                    <label for="contraseña">Contraseña</label>
                    <input type="password" id="contraseña" name="contraseña" required>
                </div>

                <button type="submit" name="login">Iniciar Sesión</button>
            </form>

            <div class="toggle-form">
                ¿No tienes cuenta? <a onclick="toggleForms()">Regístrate aquí</a>
            </div>
        </div>

        <!-- Sección de Registro -->
        <div class="form-section" id="registro-section">
            <div class="brand">
                <h1>👟 Crear Cuenta</h1>
                <p>Únete a nuestra comunidad</p>
            </div>

            <?php if ($registro_error): ?>
                <div class="error"><?php echo htmlspecialchars($registro_error); ?></div>
            <?php endif; ?>

            <?php if ($registro_success): ?>
                <div class="success"><?php echo htmlspecialchars($registro_success); ?></div>
            <?php endif; ?>

            <form method="POST">
                <div class="form-group">
                    <label for="reg_usuario">Usuario</label>
                    <input type="text" id="reg_usuario" name="usuario" required>
                </div>

                <div class="form-group">
                    <label for="reg_email">Email</label>
                    <input type="email" id="reg_email" name="email" required>
                </div>

                <div class="form-group">
                    <label for="reg_contraseña">Contraseña</label>
                    <input type="password" id="reg_contraseña" name="contraseña" required>
                </div>

                <div class="form-group">
                    <label for="reg_contraseña_confirm">Confirmar Contraseña</label>
                    <input type="password" id="reg_contraseña_confirm" name="contraseña_confirm" required>
                </div>

                <div class="form-group">
                    <label for="telefono">Teléfono (opcional)</label>
                    <input type="tel" id="telefono" name="telefono">
                </div>

                <div class="form-group">
                    <label for="direccion">Dirección (opcional)</label>
                    <textarea id="direccion" name="direccion" rows="3"></textarea>
                </div>

                <button type="submit" name="registro">Registrarse</button>
            </form>

            <div class="toggle-form">
                ¿Ya tienes cuenta? <a onclick="toggleForms()">Inicia sesión aquí</a>
            </div>
        </div>

        <!-- Sección de información -->
        <div class="info-section">
            <h2>Bienvenido</h2>
            <p>Accede a nuestra plataforma de tienda online para explorar y comprar las mejores zapatillas deportivas.</p>

            <div class="demo-info">
                <h3>Datos de prueba:</h3>
                <p><strong>Admin:</strong> admin / admin123</p>
                <p><strong>Cliente:</strong> cliente / cliente123</p>
            </div>
        </div>
    </div>

    <script>
        function toggleForms() {
            const loginSection = document.getElementById('login-section');
            const registroSection = document.getElementById('registro-section');

            loginSection.classList.toggle('active');
            registroSection.classList.toggle('active');
        }
    </script>
</body>

</html>