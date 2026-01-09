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
