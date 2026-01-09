<?php

require_once __DIR__ . '/../data/PedidoDAO.php';

$pedidoDAO = new PedidoDAO($conexion);

// Procesar acciones
$accion = $_GET['accion'] ?? '';
$id = $_GET['id'] ?? '';
$contenido = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['actualizar_estado'])) {
    $resultado = $pedidoDAO->actualizarEstado($_POST['id'], $_POST['estado']);

    if ($resultado) {
        redirigirConMensaje('?pagina=pedidos', 'success', 'Estado del pedido actualizado');
    } else {
        $_SESSION['error'] = 'Error al actualizar el estado';
    }
}

if ($accion === 'ver' && $id) {
    $contenido = mostrarDetallePedido($id);
} else {
    $contenido = mostrarListadoPedidos($pedidoDAO->obtenerTodos());
}

function mostrarListadoPedidos($pedidos)
{
    $html = '<h2>Gestión de Pedidos</h2>';

    if (empty($pedidos)) {
        $html .= '<p>No hay pedidos registrados</p>';
        return $html;
    }

    $html .= '<table>';
    $html .= '<thead><tr>';
    $html .= '<th>ID Pedido</th>';
    $html .= '<th>Usuario</th>';
    $html .= '<th>Total</th>';
    $html .= '<th>Estado</th>';
    $html .= '<th>Fecha</th>';
    $html .= '<th>Acciones</th>';
    $html .= '</tr></thead>';
    $html .= '<tbody>';

    foreach ($pedidos as $pedido) {
        $html .= '<tr>';
        $html .= '<td>#' . $pedido['id'] . '</td>';
        $html .= '<td>' . escapar($pedido['usuario']) . '</td>';
        $html .= '<td>' . formatearPrecio($pedido['total']) . '</td>';
        $html .= '<td>' . ucfirst($pedido['estado']) . '</td>';
        $html .= '<td>' . date('d/m/Y H:i', strtotime($pedido['fecha_pedido'])) . '</td>';
        $html .= '<td>';
        $html .= '<a href="?pagina=pedidos&accion=ver&id=' . $pedido['id'] . '" class="btn-editar">Ver</a>';
        $html .= '</td>';
        $html .= '</tr>';
    }

    $html .= '</tbody></table>';
    return $html;
}

function mostrarDetallePedido($pedido_id)
{
    global $pedidoDAO;

    $pedido = $pedidoDAO->obtenerPorId($pedido_id);
    if (!$pedido) {
        return '<div class="error">Pedido no encontrado</div>';
    }

    $detalles = $pedidoDAO->obtenerDetalles($pedido_id);

    $html = '<h2>Pedido #' . $pedido['id'] . '</h2>';
    $html .= '<a href="?pagina=pedidos" class="btn-nuevo" style="margin-bottom: 20px;">← Volver</a>';

    $html .= '<div style="background: #f8f9fa; padding: 20px; border-radius: 5px; margin-bottom: 20px;">';
    $html .= '<p><strong>Usuario ID:</strong> ' . $pedido['usuario_id'] . '</p>';
    $html .= '<p><strong>Total:</strong> ' . formatearPrecio($pedido['total']) . '</p>';
    $html .= '<p><strong>Fecha:</strong> ' . date('d/m/Y H:i', strtotime($pedido['fecha_pedido'])) . '</p>';
    $html .= '<p><strong>Estado:</strong> ' . ucfirst($pedido['estado']) . '</p>';
    $html .= '</div>';

    $html .= '<h3>Productos del Pedido</h3>';
    $html .= '<table>';
    $html .= '<thead><tr>';
    $html .= '<th>Producto</th>';
    $html .= '<th>Marca</th>';
    $html .= '<th>Cantidad</th>';
    $html .= '<th>Precio Unit.</th>';
    $html .= '<th>Subtotal</th>';
    $html .= '</tr></thead>';
    $html .= '<tbody>';

    foreach ($detalles as $detalle) {
        $html .= '<tr>';
        $html .= '<td>' . escapar($detalle['nombre']) . '</td>';
        $html .= '<td>' . escapar($detalle['marca']) . '</td>';
        $html .= '<td>' . $detalle['cantidad'] . '</td>';
        $html .= '<td>' . formatearPrecio($detalle['precio_unitario']) . '</td>';
        $html .= '<td>' . formatearPrecio($detalle['subtotal']) . '</td>';
        $html .= '</tr>';
    }

    $html .= '</tbody></table>';

    $html .= '<h3>Cambiar Estado</h3>';
    $html .= '<form method="POST" style="max-width: 300px;">';
    $html .= '<input type="hidden" name="id" value="' . $pedido['id'] . '">';
    $html .= '<div class="form-group">';
    $html .= '<label for="estado">Nuevo Estado</label>';
    $html .= '<select id="estado" name="estado" required>';
    $html .= '<option value="pendiente"' . ($pedido['estado'] === 'pendiente' ? ' selected' : '') . '>Pendiente</option>';
    $html .= '<option value="confirmado"' . ($pedido['estado'] === 'confirmado' ? ' selected' : '') . '>Confirmado</option>';
    $html .= '<option value="entregado"' . ($pedido['estado'] === 'entregado' ? ' selected' : '') . '>Entregado</option>';
    $html .= '<option value="cancelado"' . ($pedido['estado'] === 'cancelado' ? ' selected' : '') . '>Cancelado</option>';
    $html .= '</select>';
    $html .= '</div>';
    $html .= '<button type="submit" name="actualizar_estado" class="btn-guardar">Actualizar Estado</button>';
    $html .= '</form>';

    return $html;
}
