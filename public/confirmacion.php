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
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .contenedor {
            background: white;
            border-radius: 10px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
            max-width: 600px;
            width: 100%;
            padding: 40px;
            text-align: center;
        }

        .icono-exito {
            font-size: 60px;
            margin-bottom: 20px;
        }

        h1 {
            color: #155724;
            margin-bottom: 10px;
            font-size: 28px;
        }

        .mensaje {
            color: #155724;
            margin-bottom: 30px;
            font-size: 16px;
        }

        .detalles-pedido {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 30px;
            text-align: left;
        }

        .detalle-item {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid #ddd;
        }

        .detalle-item:last-child {
            border-bottom: none;
        }

        .detalle-label {
            font-weight: 600;
            color: #2c3e50;
        }

        .detalle-valor {
            color: #27ae60;
        }

        .productos-pedido {
            margin-bottom: 30px;
            text-align: left;
        }

        .productos-pedido h3 {
            color: #2c3e50;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 2px solid #3498db;
        }

        .producto-item {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid #ddd;
        }

        .producto-item:last-child {
            border-bottom: none;
        }

        .producto-info {
            text-align: left;
        }

        .producto-nombre {
            font-weight: 600;
            color: #2c3e50;
        }

        .producto-marca {
            color: #7f8c8d;
            font-size: 13px;
        }

        .producto-cantidad {
            color: #7f8c8d;
            font-size: 13px;
        }

        .producto-precio {
            color: #27ae60;
            font-weight: 600;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        th {
            background: #f0f0f0;
            font-weight: 600;
            color: #2c3e50;
        }

        tr:last-child td {
            border-bottom: none;
        }

        .precio {
            color: #27ae60;
            font-weight: 600;
        }

        .acciones {
            display: flex;
            gap: 10px;
            justify-content: center;
        }

        .boton {
            display: inline-block;
            padding: 12px 25px;
            border-radius: 5px;
            text-decoration: none;
            font-weight: 600;
            transition: background 0.3s;
            border: none;
            cursor: pointer;
            font-size: 16px;
        }

        .boton-principal {
            background: #27ae60;
            color: white;
        }

        .boton-principal:hover {
            background: #229954;
        }

        .boton-secundario {
            background: #3498db;
            color: white;
        }

        .boton-secundario:hover {
            background: #2980b9;
        }

        @media (max-width: 600px) {
            .contenedor {
                padding: 20px;
            }

            h1 {
                font-size: 24px;
            }

            .acciones {
                flex-direction: column;
            }

            .boton {
                width: 100%;
            }
        }
    </style>
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
