<?php
/**
 * Historial de pedidos - Cliente
 * CAPA CLIENTE
 */

session_start();
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/funciones.php';
require_once __DIR__ . '/../business/CarritoManager.php';
require_once __DIR__ . '/../data/PedidoDAO.php';

verificarCliente();

$carritoManager = new CarritoManager($conexion);
$pedidoDAO = new PedidoDAO($conexion);

$carrito = $carritoManager->obtenerCarrito();
$cantidad_carrito = count($carrito);

// Obtener pedidos del usuario
$pedidos = $pedidoDAO->obtenerPorUsuario($_SESSION['usuario_id']);

// Procesar vista de detalles
$ver_detalles = $_GET['ver'] ?? '';
$detalles_pedido = null;

if ($ver_detalles) {
    $detalles_pedido = $pedidoDAO->obtenerDetalles($ver_detalles);
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mis Pedidos - Tienda de Zapatillas</title>
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

        .header-content {
            max-width: 1200px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        header h1 {
            font-size: 24px;
        }

        .header-right {
            display: flex;
            gap: 20px;
            align-items: center;
        }

        .carrito-link {
            position: relative;
            text-decoration: none;
            color: white;
            background: #3498db;
            padding: 10px 15px;
            border-radius: 5px;
            transition: background 0.3s;
        }

        .carrito-link:hover {
            background: #2980b9;
        }

        .carrito-badge {
            position: absolute;
            top: -8px;
            right: -8px;
            background: #e74c3c;
            color: white;
            width: 25px;
            height: 25px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 12px;
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

        .container {
            max-width: 1000px;
            margin: 30px auto;
            padding: 0 20px;
        }

        .titulo-seccion {
            color: #2c3e50;
            margin-bottom: 30px;
            border-bottom: 3px solid #3498db;
            padding-bottom: 10px;
        }

        .pedidos-vacio {
            background: white;
            padding: 60px 20px;
            border-radius: 8px;
            text-align: center;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .pedidos-vacio p {
            font-size: 18px;
            color: #7f8c8d;
            margin-bottom: 20px;
        }

        .btn-continuar {
            display: inline-block;
            background: #3498db;
            color: white;
            padding: 12px 30px;
            border-radius: 5px;
            text-decoration: none;
            transition: background 0.3s;
        }

        .btn-continuar:hover {
            background: #2980b9;
        }

        .pedido-card {
            background: white;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .pedido-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
            padding-bottom: 15px;
            border-bottom: 2px solid #f0f0f0;
        }

        .pedido-numero {
            font-size: 18px;
            font-weight: 600;
            color: #2c3e50;
        }

        .pedido-estado {
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: 600;
            text-transform: uppercase;
        }

        .estado-pendiente {
            background: #fff3cd;
            color: #856404;
        }

        .estado-confirmado {
            background: #d1ecf1;
            color: #0c5460;
        }

        .estado-entregado {
            background: #d4edda;
            color: #155724;
        }

        .estado-cancelado {
            background: #f8d7da;
            color: #721c24;
        }

        .pedido-info {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 15px;
            margin-bottom: 15px;
        }

        .info-item {
            text-align: center;
        }

        .info-label {
            color: #7f8c8d;
            font-size: 13px;
            margin-bottom: 5px;
        }

        .info-valor {
            color: #2c3e50;
            font-weight: 600;
            font-size: 16px;
        }

        .pedido-acciones {
            display: flex;
            gap: 10px;
        }

        .btn-ver {
            flex: 1;
            background: #3498db;
            color: white;
            padding: 10px 15px;
            border-radius: 5px;
            text-decoration: none;
            text-align: center;
            transition: background 0.3s;
        }

        .btn-ver:hover {
            background: #2980b9;
        }

        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 1000;
            align-items: center;
            justify-content: center;
        }

        .modal.mostrar {
            display: flex;
        }

        .modal-contenido {
            background: white;
            border-radius: 10px;
            padding: 30px;
            max-width: 600px;
            width: 90%;
            max-height: 80vh;
            overflow-y: auto;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.3);
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 2px solid #f0f0f0;
        }

        .modal-header h2 {
            color: #2c3e50;
            font-size: 20px;
        }

        .modal-close {
            font-size: 28px;
            color: #95a5a6;
            cursor: pointer;
            background: none;
            border: none;
            padding: 0;
            width: 30px;
            height: 30px;
        }

        .modal-close:hover {
            color: #2c3e50;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
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

        tr:last-child td {
            border-bottom: none;
        }

        @media (max-width: 768px) {
            .header-content {
                flex-direction: column;
                gap: 15px;
            }

            .header-right {
                flex-direction: column;
                width: 100%;
            }

            .pedido-info {
                grid-template-columns: 1fr;
            }

            .pedido-acciones {
                flex-direction: column;
            }

            .modal-contenido {
                padding: 20px;
            }
        }
    </style>
</head>
<body>
    <header>
        <div class="header-content">
            <h1>👟 Tienda de Zapatillas</h1>
            <div class="header-right">
                <a href="catalogo.php" class="carrito-link">
                    🏠 Catálogo
                </a>
                <a href="carrito.php" class="carrito-link">
                    🛒 Carrito
                    <?php if ($cantidad_carrito > 0): ?>
                        <span class="carrito-badge"><?php echo $cantidad_carrito; ?></span>
                    <?php endif; ?>
                </a>
                <div class="usuario-info">
                    <span>Hola, <?php echo escapar($_SESSION['usuario']); ?></span>
                    <a href="logout.php">Cerrar Sesión</a>
                </div>
            </div>
        </div>
    </header>

    <div class="container">
        <h2 class="titulo-seccion">Mis Pedidos</h2>

        <?php if (empty($pedidos)): ?>
            <div class="pedidos-vacio">
                <h3>No tienes pedidos</h3>
                <p>Aún no has realizado ninguna compra en nuestra tienda</p>
                <a href="catalogo.php" class="btn-continuar">Ir al Catálogo</a>
            </div>
        <?php else: ?>
            <div>
                <?php foreach ($pedidos as $pedido): ?>
                    <div class="pedido-card">
                        <div class="pedido-header">
                            <span class="pedido-numero">Pedido #<?php echo $pedido['id']; ?></span>
                            <span class="pedido-estado estado-<?php echo $pedido['estado']; ?>">
                                <?php echo ucfirst($pedido['estado']); ?>
                            </span>
                        </div>

                        <div class="pedido-info">
                            <div class="info-item">
                                <div class="info-label">Fecha</div>
                                <div class="info-valor"><?php echo date('d/m/Y', strtotime($pedido['fecha_pedido'])); ?></div>
                            </div>
                            <div class="info-item">
                                <div class="info-label">Total</div>
                                <div class="info-valor"><?php echo formatearPrecio($pedido['total'] * 1.21); ?></div>
                            </div>
                            <div class="info-item">
                                <div class="info-label">Hora</div>
                                <div class="info-valor"><?php echo date('H:i', strtotime($pedido['fecha_pedido'])); ?></div>
                            </div>
                        </div>

                        <div class="pedido-acciones">
                            <a href="?ver=<?php echo $pedido['id']; ?>" class="btn-ver" onclick="verDetalles(event, <?php echo $pedido['id']; ?>)">
                                Ver Detalles
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <!-- Modal para detalles del pedido -->
    <div class="modal" id="detallesModal">
        <div class="modal-contenido">
            <div class="modal-header">
                <h2 id="modalTitulo">Detalles del Pedido</h2>
                <button class="modal-close" onclick="cerrarModal()">&times;</button>
            </div>
            <div id="modalContenido"></div>
        </div>
    </div>

    <script>
        function verDetalles(event, pedidoId) {
            event.preventDefault();
            const modal = document.getElementById('detallesModal');
            const contenido = document.getElementById('modalContenido');

            // Obtener detalles mediante AJAX
            fetch('get_detalles_pedido.php?id=' + pedidoId)
                .then(response => response.text())
                .then(data => {
                    contenido.innerHTML = data;
                    modal.classList.add('mostrar');
                });
        }

        function cerrarModal() {
            document.getElementById('detallesModal').classList.remove('mostrar');
        }

        window.onclick = function(event) {
            const modal = document.getElementById('detallesModal');
            if (event.target === modal) {
                modal.classList.remove('mostrar');
            }
        }
    </script>
</body>
</html>
