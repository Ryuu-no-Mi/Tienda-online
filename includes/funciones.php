<?php

/**
 * Funciones auxiliares globales
 * CAPA CLIENTE
 */

// Verificar si el usuario está autenticado
function verificarAutenticacion()
{
    if (!isset($_SESSION['usuario_id'])) {
        header('Location: /Tienda online/index.php');
        exit;
    }
}

// Verificar si el usuario es administrador
function verificarAdmin()
{
    verificarAutenticacion();
    if ($_SESSION['rol'] !== 'administrador') {
        header('Location: /Tienda online/public/catalogo.php');
        exit;
    }
}

// Verificar si el usuario es cliente
function verificarCliente()
{
    verificarAutenticacion();
    if ($_SESSION['rol'] !== 'cliente') {
        header('Location: /Tienda online/admin/dashboard.php');
        exit;
    }
}

// Cerrar sesión
function cerrarSesion()
{
    session_destroy();
    header('Location: /Tienda online/index.php');
    exit;
}

// Escapar salida HTML
function escapar($texto)
{
    return htmlspecialchars($texto, ENT_QUOTES, 'UTF-8');
}

// Formatear precio
function formatearPrecio($precio)
{
    return number_format($precio, 2, ',', '.') . '€';
}

// Obtener ruta relativa correcta
function obtenerRutaBase()
{
    return '/Tienda online/';
}

// Obtener URL de imagen
function obtenerUrlImagen($nombreImagen)
{
    if (empty($nombreImagen)) {
        return '/Tienda online/assets/images/placeholder.jpg';
    }
    return '/Tienda online/assets/images/' . htmlspecialchars($nombreImagen);
}

// Redirigir con mensaje
function redirigirConMensaje($url, $tipo, $mensaje)
{
    $_SESSION['mensaje'] = $mensaje;
    $_SESSION['tipo_mensaje'] = $tipo;
    header('Location: ' . $url);
    exit;
}

// Mostrar mensaje si existe
function mostrarMensaje()
{
    if (isset($_SESSION['mensaje'])) {
        $tipo = $_SESSION['tipo_mensaje'] ?? 'info';
        $mensaje = $_SESSION['mensaje'];
        unset($_SESSION['mensaje']);
        unset($_SESSION['tipo_mensaje']);
        return "<div class='mensaje {$tipo}'>" . escapar($mensaje) . "</div>";
    }
    return '';
}

function registrarUsuario($usuario, $email, $contraseña)
{
    global $conexion_global;

    // Verificar si el usuario ya existe
    $stmt = $conexion_global->prepare("SELECT * FROM usuarios WHERE usuario = ?");
    $stmt->bind_param("s", $usuario);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($resultado->num_rows > 0) {
        return "El usuario ya existe.";
    }

    // Insertar nuevo usuario
    $stmt = $conexion_global->prepare("INSERT INTO usuarios (usuario, email, contraseña) VALUES (?, ?, ?)");
    $contraseña_hash = password_hash($contraseña, PASSWORD_DEFAULT);
    $stmt->bind_param("sss", $usuario, $email, $contraseña_hash);
    if ($stmt->execute()) {
        return "Registro exitoso.";
    } else {
        return "Error al registrar el usuario.";
    }
}

function iniciarSesion($usuario, $contraseña)
{
    global $conexion_global;

    $stmt = $conexion_global->prepare("SELECT id, usuario, email, contraseña, rol FROM usuarios WHERE usuario = ?");
    $stmt->bind_param("s", $usuario);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($resultado->num_rows === 0) {
        return array('error' => 'Usuario no encontrado.');
    }

    $fila = $resultado->fetch_assoc();

    // Verificar contraseña con bcrypt
    if (password_verify($contraseña, $fila['contraseña'])) {
        return array(
            'exito' => true,
            'usuario' => $fila
        );
    } else {
        return array('error' => 'Contraseña incorrecta.');
    }
}
