<?php

/**
 * AJAX - Obtener detalles del pedido
 * CAPA CLIENTE
 */

session_start();
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/funciones.php';
require_once __DIR__ . '/../data/PedidoDAO.php';

verificarCliente();

$pedido_id = $_GET['id'] ?? '';

if (!$pedido_id) {
    echo '<div class="error">Pedido no encontrado</div>';
    exit;
}

$pedidoDAO = new PedidoDAO($conexion);
$pedido = $pedidoDAO->obtenerPorId($pedido_id);

// Verificar que el pedido pertenece al usuario actual
if (!$pedido || $pedido['usuario_id'] != $_SESSION['usuario_id']) {
    echo '<div class="error">No tienes acceso a este pedido</div>';
    exit;
}

$detalles = $pedidoDAO->obtenerDetalles($pedido_id);

echo '<div style="background: #f8f9fa; padding: 15px; border-radius: 5px; margin-bottom: 20px;">';
echo '<p><strong>Pedido:</strong> #' . $pedido['id'] . '</p>';
echo '<p><strong>Fecha:</strong> ' . date('d/m/Y H:i', strtotime($pedido['fecha_pedido'])) . '</p>';
echo '<p><strong>Estado:</strong> ' . ucfirst($pedido['estado']) . '</p>';
echo '</div>';

echo '<h3 style="margin: 20px 0 15px 0; color: #2c3e50;">Productos</h3>';
echo '<table>';
echo '<thead>';
echo '<tr>';
echo '<th>Producto</th>';
echo '<th>Marca</th>';
echo '<th>Cantidad</th>';
echo '<th>Precio</th>';
echo '<th>Subtotal</th>';
echo '</tr>';
echo '</thead>';
echo '<tbody>';

foreach ($detalles as $detalle) {
    echo '<tr>';
    echo '<td>' . escapar($detalle['nombre']) . '</td>';
    echo '<td>' . escapar($detalle['marca']) . '</td>';
    echo '<td>' . $detalle['cantidad'] . '</td>';
    echo '<td>' . formatearPrecio($detalle['precio_unitario']) . '</td>';
    echo '<td style="color: #27ae60; font-weight: 600;">' . formatearPrecio($detalle['subtotal']) . '</td>';
    echo '</tr>';
}

echo '</tbody>';
echo '</table>';

echo '<div style="margin-top: 20px; padding-top: 20px; border-top: 2px solid #ddd;">';
echo '<p><strong>Subtotal:</strong> ' . formatearPrecio($pedido['total']) . '</p>';
echo '<p><strong>IVA (21%):</strong> ' . formatearPrecio($pedido['total'] * 0.21) . '</p>';
echo '<p style="font-size: 18px; color: #27ae60;"><strong>Total:</strong> ' . formatearPrecio($pedido['total'] * 1.21) . '</p>';
echo '</div>';
