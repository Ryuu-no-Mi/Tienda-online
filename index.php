<?php

/**
 * Página principal - Login y Registro
 * CAPA CLIENTE
 */

session_start();

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
    require_once 'includes/config.php';
    require_once 'business/UsuarioManager.php';

    $usuarioManager = new UsuarioManager($conexion);
    $resultado = $usuarioManager->login($_POST['usuario'] ?? '', $_POST['contraseña'] ?? '');

    if ($resultado['success']) {
        $_SESSION['usuario_id'] = $resultado['usuario']['id'];
        $_SESSION['usuario'] = $resultado['usuario']['usuario'];
        $_SESSION['rol'] = $resultado['usuario']['rol'];
        $_SESSION['email'] = $resultado['usuario']['email'];

        if ($_SESSION['rol'] === 'administrador') {
            header('Location: admin/dashboard.php');
        } else {
            header('Location: public/catalogo.php');
        }
        exit;
    } else {
        $login_error = implode(', ', $resultado['errores']);
    }
}

// Procesar registro
$registro_error = '';
$registro_success = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['registro'])) {
    require_once 'includes/config.php';
    require_once 'business/UsuarioManager.php';

    $usuarioManager = new UsuarioManager($conexion);
    $resultado = $usuarioManager->registrar(
        $_POST['usuario'] ?? '',
        $_POST['email'] ?? '',
        $_POST['contraseña'] ?? '',
        $_POST['contraseña_confirm'] ?? '',
        $_POST['telefono'] ?? '',
        $_POST['direccion'] ?? ''
    );

    if ($resultado['success']) {
        $registro_success = $resultado['mensaje'];
    } else {
        $registro_error = implode(', ', $resultado['errores']);
    }
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tienda de Zapatillas - Login</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }

        .container {
            display: flex;
            width: 100%;
            max-width: 1000px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.3);
            overflow: hidden;
        }

        .form-section {
            flex: 1;
            padding: 40px;
            display: none;
        }

        .form-section.active {
            display: block;
        }

        .brand {
            text-align: center;
            margin-bottom: 30px;
        }

        .brand h1 {
            color: #667eea;
            font-size: 28px;
            margin-bottom: 5px;
        }

        .brand p {
            color: #999;
            font-size: 14px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            color: #333;
            font-weight: 500;
        }

        input,
        textarea {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 14px;
            transition: border-color 0.3s;
        }

        input:focus,
        textarea:focus {
            outline: none;
            border-color: #667eea;
        }

        button {
            width: 100%;
            padding: 12px;
            background: #667eea;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.3s;
        }

        button:hover {
            background: #5568d3;
        }

        .toggle-form {
            text-align: center;
            margin-top: 20px;
            color: #666;
        }

        .toggle-form a {
            color: #667eea;
            cursor: pointer;
            text-decoration: none;
            font-weight: 600;
        }

        .toggle-form a:hover {
            text-decoration: underline;
        }

        .error {
            background: #f8d7da;
            color: #721c24;
            padding: 12px;
            border-radius: 5px;
            margin-bottom: 20px;
            border: 1px solid #f5c6cb;
        }

        .success {
            background: #d4edda;
            color: #155724;
            padding: 12px;
            border-radius: 5px;
            margin-bottom: 20px;
            border: 1px solid #c3e6cb;
        }

        .info-section {
            flex: 1;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 40px;
            color: white;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .info-section h2 {
            font-size: 32px;
            margin-bottom: 20px;
        }

        .info-section p {
            font-size: 16px;
            line-height: 1.6;
            margin-bottom: 15px;
        }

        .demo-info {
            background: rgba(255, 255, 255, 0.1);
            padding: 20px;
            border-radius: 5px;
            margin-top: 30px;
        }

        .demo-info h3 {
            margin-bottom: 10px;
        }

        .demo-info p {
            font-size: 14px;
            margin-bottom: 5px;
        }

        @media (max-width: 768px) {
            .container {
                flex-direction: column;
            }

            .info-section {
                display: none;
            }

            .form-section {
                padding: 30px 20px;
            }
        }
    </style>
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