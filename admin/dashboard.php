<?php

/**
 * Panel administrador - Dashboard
 * CAPA CLIENTE
 */

session_start();
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/funciones.php';

verificarAdmin();

// Variables para el dashboard
$titulo = 'Dashboard Administrador';
$contenido = '';

// Procesar acciones según la página solicitada
$pagina = $_GET['pagina'] ?? 'inicio';

switch ($pagina) {
    case 'usuarios':
        require_once __DIR__ . '/usuarios.php';
        break;
    case 'productos':
        require_once __DIR__ . '/productos.php';
        break;
    case 'pedidos':
        require_once __DIR__ . '/pedidos.php';
        break;
    default:
        $contenido = generarPaginaInicio();
}

function generarPaginaInicio()
{
    global $conexion;
    require_once __DIR__ . '/../business/UsuarioManager.php';
    require_once __DIR__ . '/../business/ProductoManager.php';
    require_once __DIR__ . '/../data/PedidoDAO.php';

    $usuarioManager = new UsuarioManager($conexion);
    $productoManager = new ProductoManager($conexion);
    $pedidoDAO = new PedidoDAO($conexion);

    $totalUsuarios = count($usuarioManager->obtenerTodos());
    $totalProductos = count($productoManager->obtenerTodosAdmin());
    $totalPedidos = count($pedidoDAO->obtenerTodos());

    return <<<HTML
    <div class="dashboard-grid">
        <div class="tarjeta">
            <h3>Usuarios Registrados</h3>
            <p class="numero">$totalUsuarios</p>
            <a href="?pagina=usuarios" class="boton">Gestionar</a>
        </div>
        <div class="tarjeta">
            <h3>Productos en Catálogo</h3>
            <p class="numero">$totalProductos</p>
            <a href="?pagina=productos" class="boton">Gestionar</a>
        </div>
        <div class="tarjeta">
            <h3>Pedidos Totales</h3>
            <p class="numero">$totalPedidos</p>
            <a href="?pagina=pedidos" class="boton">Ver</a>
        </div>
    </div>
    HTML;
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo escapar($titulo); ?></title>
    <link rel="stylesheet" href="../assets/css/styles-dashboard.css">


</head>

<body>
    <header>
        <div class="header-info">
            <h1>👟 Tienda de Zapatillas - Admin</h1>
            <div class="usuario-info">
                <span>Bienvenido, <?php echo escapar($_SESSION['usuario']); ?></span>
                <a href="logout.php">Cerrar Sesión</a>
            </div>
        </div>
    </header>

    <nav>
        <ul>
            <li><a href="dashboard.php">Inicio</a></li>
            <li><a href="?pagina=usuarios">Usuarios</a></li>
            <li><a href="?pagina=productos">Productos</a></li>
            <li><a href="?pagina=pedidos">Pedidos</a></li>
        </ul>
    </nav>

    <div class="container">
        <div class="content">
            <?php echo mostrarMensaje(); ?>
            <?php echo $contenido; ?>
        </div>
    </div>
</body>

</html>