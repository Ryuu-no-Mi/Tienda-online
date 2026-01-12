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
    <link rel="stylesheet" href="../assets/css/styles-historial.css">
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