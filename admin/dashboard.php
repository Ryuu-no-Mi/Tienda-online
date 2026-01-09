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

function generarPaginaInicio() {
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
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f5f5f5;
        }

        header {
            background: #2c3e50;
            color: white;
            padding: 20px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        header h1 {
            font-size: 24px;
        }

        .header-info {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .usuario-info {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .usuario-info a {
            color: white;
            text-decoration: none;
            background: #e74c3c;
            padding: 8px 15px;
            border-radius: 3px;
            transition: background 0.3s;
        }

        .usuario-info a:hover {
            background: #c0392b;
        }

        nav {
            background: #34495e;
            padding: 0;
        }

        nav ul {
            list-style: none;
            display: flex;
            margin: 0;
        }

        nav li {
            margin: 0;
        }

        nav a {
            display: block;
            color: white;
            padding: 15px 20px;
            text-decoration: none;
            transition: background 0.3s;
        }

        nav a:hover {
            background: #2c3e50;
        }

        .container {
            max-width: 1200px;
            margin: 30px auto;
            padding: 0 20px;
        }

        .dashboard-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .tarjeta {
            background: white;
            padding: 25px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        .tarjeta h3 {
            color: #2c3e50;
            margin-bottom: 15px;
            font-size: 18px;
        }

        .tarjeta .numero {
            font-size: 48px;
            color: #3498db;
            font-weight: bold;
            margin-bottom: 15px;
        }

        .tarjeta .boton {
            display: inline-block;
            background: #3498db;
            color: white;
            padding: 10px 20px;
            border-radius: 5px;
            text-decoration: none;
            transition: background 0.3s;
        }

        .tarjeta .boton:hover {
            background: #2980b9;
        }

        .content {
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        th {
            background: #f8f9fa;
            font-weight: 600;
            color: #2c3e50;
        }

        tr:hover {
            background: #f8f9fa;
        }

        .boton-grupo {
            display: flex;
            gap: 8px;
        }

        .btn-editar, .btn-eliminar, .btn-nuevo {
            padding: 8px 12px;
            border: none;
            border-radius: 3px;
            cursor: pointer;
            text-decoration: none;
            font-size: 14px;
            transition: background 0.3s;
        }

        .btn-editar {
            background: #3498db;
            color: white;
        }

        .btn-editar:hover {
            background: #2980b9;
        }

        .btn-eliminar {
            background: #e74c3c;
            color: white;
        }

        .btn-eliminar:hover {
            background: #c0392b;
        }

        .btn-nuevo {
            background: #27ae60;
            color: white;
            display: inline-block;
            margin-bottom: 20px;
        }

        .btn-nuevo:hover {
            background: #229954;
        }

        form {
            max-width: 500px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #2c3e50;
        }

        input, textarea, select {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 14px;
            font-family: inherit;
        }

        input:focus, textarea:focus, select:focus {
            outline: none;
            border-color: #3498db;
            box-shadow: 0 0 5px rgba(52, 152, 219, 0.3);
        }

        .form-acciones {
            display: flex;
            gap: 10px;
        }

        .form-acciones button {
            flex: 1;
            padding: 12px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            font-weight: 600;
            transition: background 0.3s;
        }

        .btn-guardar {
            background: #27ae60;
            color: white;
        }

        .btn-guardar:hover {
            background: #229954;
        }

        .btn-cancelar {
            background: #95a5a6;
            color: white;
        }

        .btn-cancelar:hover {
            background: #7f8c8d;
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

        @media (max-width: 768px) {
            .header-info {
                flex-direction: column;
                gap: 15px;
            }

            nav ul {
                flex-direction: column;
            }

            .dashboard-grid {
                grid-template-columns: 1fr;
            }

            table {
                font-size: 12px;
            }

            .boton-grupo {
                flex-direction: column;
            }
        }
    </style>
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
