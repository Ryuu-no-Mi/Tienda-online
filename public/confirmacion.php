<?php

/**
 * Confirmación de compra - Cliente
 * CAPA CLIENTE
 */

session_start();
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/funciones.php';
require_once __DIR__ . '/../data/PedidoDAO.php';

verificarCliente();

if (!isset($_SESSION['pedido_id'])) {
    header('Location: catalogo.php');
    exit;
}

$pedidoDAO = new PedidoDAO($conexion);
$pedido = $pedidoDAO->obtenerPorId($_SESSION['pedido_id']);
$detalles = $pedidoDAO->obtenerDetalles($_SESSION['pedido_id']);

unset($_SESSION['pedido_id']);
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirmación de Compra - Tienda de Zapatillas</title>
    <link rel="stylesheet" href="../assets/css/styles-confirmacion.css">
</head>

<body>
    <div class="contenedor">
        <div class="icono-exito">✅</div>
        <h1>¡Compra Confirmada!</h1>
        <p class="mensaje">Tu pedido ha sido registrado correctamente en nuestro sistema.</p>

        <div class="detalles-pedido">
            <div class="detalle-item">
                <span class="detalle-label">Número de Pedido:</span>
                <span class="detalle-valor">#<?php echo $pedido['id']; ?></span>
            </div>
            <div class="detalle-item">
                <span class="detalle-label">Fecha:</span>
                <span class="detalle-valor"><?php echo date('d/m/Y H:i', strtotime($pedido['fecha_pedido'])); ?></span>
            </div>
            <div class="detalle-item">
                <span class="detalle-label">Estado:</span>
                <span class="detalle-valor"><?php echo ucfirst($pedido['estado']); ?></span>
            </div>
        </div>

        <div class="productos-pedido">
            <h3>Productos del Pedido</h3>
            <table>
                <thead>
                    <tr>
                        <th>Producto</th>
                        <th>Cantidad</th>
                        <th>Precio</th>
                        <th>Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($detalles as $detalle): ?>
                        <tr>
                            <td>
                                <div class="producto-nombre"><?php echo escapar($detalle['nombre']); ?></div>
                                <div class="producto-marca"><?php echo escapar($detalle['marca']); ?></div>
                            </td>
                            <td><?php echo $detalle['cantidad']; ?></td>
                            <td class="precio"><?php echo formatearPrecio($detalle['precio_unitario']); ?></td>
                            <td class="precio"><?php echo formatearPrecio($detalle['subtotal']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div class="detalles-pedido">
            <div class="detalle-item">
                <span class="detalle-label">Subtotal:</span>
                <span class="detalle-valor"><?php echo formatearPrecio($pedido['total']); ?></span>
            </div>
            <div class="detalle-item">
                <span class="detalle-label">IVA (21%):</span>
                <span class="detalle-valor"><?php echo formatearPrecio($pedido['total'] * 0.21); ?></span>
            </div>
            <div class="detalle-item">
                <span class="detalle-label" style="font-size: 18px;">Total:</span>
                <span class="detalle-valor" style="font-size: 18px;"><?php echo formatearPrecio($pedido['total'] * 1.21); ?></span>
            </div>
        </div>

        <div class="acciones">
            <a href="catalogo.php" class="boton boton-principal">Continuar Comprando</a>
            <a href="historial.php" class="boton boton-secundario">Ver Mis Pedidos</a>
        </div>
    </div>
</body>

</html>