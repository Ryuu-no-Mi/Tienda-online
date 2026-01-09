<?php

/**
 * Carrito de compras - Cliente
 * CAPA CLIENTE
 */

session_start();
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/funciones.php';
require_once __DIR__ . '/../business/CarritoManager.php';

verificarCliente();

$carritoManager = new CarritoManager($conexion);

// Procesar acciones
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['actualizar_cantidad'])) {
        $carritoManager->actualizarCantidad($_POST['producto_id'], $_POST['cantidad']);
        header('Location: carrito.php');
        exit;
    } elseif (isset($_POST['eliminar_producto'])) {
        $carritoManager->eliminarProducto($_POST['producto_id']);
        header('Location: carrito.php');
        exit;
    } elseif (isset($_POST['confirmar_compra'])) {
        $resultado = $carritoManager->confirmarCompra($_SESSION['usuario_id']);
        if ($resultado['success']) {
            $_SESSION['pedido_id'] = $resultado['pedido_id'];
            header('Location: confirmacion.php');
            exit;
        } else {
            $error = implode(', ', $resultado['errores']);
        }
    } elseif (isset($_POST['vaciar_carrito'])) {
        $carritoManager->vaciarCarrito();
        header('Location: carrito.php');
        exit;
    }
}

$carrito = $carritoManager->obtenerCarrito();
$total = $carritoManager->calcularTotal();
$cantidad_carrito = count($carrito);
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Carrito de Compras - Tienda de Zapatillas</title>
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

        .carrito-link.activo {
            background: #e74c3c;
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

        .carrito-vacio {
            background: white;
            padding: 60px 20px;
            border-radius: 8px;
            text-align: center;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .carrito-vacio p {
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

        .carrito-contenido {
            display: grid;
            grid-template-columns: 1fr 300px;
            gap: 20px;
        }

        .tabla-carrito {
            background: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            padding: 15px;
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

        .producto-cell {
            font-weight: 600;
            color: #2c3e50;
        }

        .precio-cell {
            color: #27ae60;
            font-weight: 600;
        }

        .cantidad-cell {
            display: flex;
            gap: 5px;
            align-items: center;
        }

        .cantidad-input {
            width: 60px;
            padding: 6px;
            border: 1px solid #ddd;
            border-radius: 3px;
            text-align: center;
        }

        .btn-actualizar {
            background: #3498db;
            color: white;
            border: none;
            padding: 6px 12px;
            border-radius: 3px;
            cursor: pointer;
            font-size: 12px;
        }

        .btn-actualizar:hover {
            background: #2980b9;
        }

        .btn-eliminar {
            background: #e74c3c;
            color: white;
            border: none;
            padding: 6px 12px;
            border-radius: 3px;
            cursor: pointer;
            font-size: 12px;
        }

        .btn-eliminar:hover {
            background: #c0392b;
        }

        .resumen {
            background: white;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            height: fit-content;
            position: sticky;
            top: 20px;
        }

        .resumen h3 {
            color: #2c3e50;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #f0f0f0;
        }

        .resumen-item {
            display: flex;
            justify-content: space-between;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 1px solid #f0f0f0;
        }

        .resumen-item:last-of-type {
            border-bottom: 2px solid #3498db;
            font-weight: 600;
            font-size: 18px;
            margin-bottom: 20px;
            padding-bottom: 15px;
        }

        .resumen-item span:first-child {
            color: #7f8c8d;
        }

        .resumen-item span:last-child {
            color: #27ae60;
            font-weight: 600;
        }

        .btn-confirmar {
            width: 100%;
            padding: 12px;
            background: #27ae60;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.3s;
            margin-bottom: 10px;
        }

        .btn-confirmar:hover {
            background: #229954;
        }

        .btn-confirmar:disabled {
            background: #95a5a6;
            cursor: not-allowed;
        }

        .btn-vaciar {
            width: 100%;
            padding: 12px;
            background: #95a5a6;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.3s;
        }

        .btn-vaciar:hover {
            background: #7f8c8d;
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

            .carrito-contenido {
                grid-template-columns: 1fr;
            }

            .resumen {
                position: relative;
                top: 0;
            }

            table {
                font-size: 12px;
            }

            th,
            td {
                padding: 10px;
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
                <a href="carrito.php" class="carrito-link activo">
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
        <h2 class="titulo-seccion">Tu Carrito de Compras</h2>

        <?php if (isset($error)): ?>
            <div style="background: #f8d7da; color: #721c24; padding: 15px; border-radius: 5px; margin-bottom: 20px;">
                <?php echo escapar($error); ?>
            </div>
        <?php endif; ?>

        <?php if (empty($carrito)): ?>
            <div class="carrito-vacio">
                <h3>Tu carrito está vacío</h3>
                <p>No hay productos en tu carrito de compras</p>
                <a href="catalogo.php" class="btn-continuar">Continuar Comprando</a>
            </div>
        <?php else: ?>
            <div class="carrito-contenido">
                <div class="tabla-carrito">
                    <table>
                        <thead>
                            <tr>
                                <th>Producto</th>
                                <th>Precio</th>
                                <th>Cantidad</th>
                                <th>Subtotal</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($carrito as $producto): ?>
                                <tr>
                                    <td class="producto-cell"><?php echo escapar($producto['nombre']); ?></td>
                                    <td class="precio-cell"><?php echo formatearPrecio($producto['precio']); ?></td>
                                    <td>
                                        <form method="POST" style="display: flex; gap: 5px;">
                                            <input type="hidden" name="producto_id" value="<?php echo $producto['id']; ?>">
                                            <input type="number" name="cantidad" class="cantidad-input" value="<?php echo $producto['cantidad']; ?>" min="1">
                                            <button type="submit" name="actualizar_cantidad" class="btn-actualizar">✓</button>
                                        </form>
                                    </td>
                                    <td class="precio-cell"><?php echo formatearPrecio($producto['precio'] * $producto['cantidad']); ?></td>
                                    <td>
                                        <form method="POST" style="display: inline;">
                                            <input type="hidden" name="producto_id" value="<?php echo $producto['id']; ?>">
                                            <button type="submit" name="eliminar_producto" class="btn-eliminar">Eliminar</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <div class="resumen">
                    <h3>Resumen del Pedido</h3>
                    <div class="resumen-item">
                        <span>Subtotal</span>
                        <span><?php echo formatearPrecio($total); ?></span>
                    </div>
                    <div class="resumen-item">
                        <span>IVA (21%)</span>
                        <span><?php echo formatearPrecio($total * 0.21); ?></span>
                    </div>
                    <div class="resumen-item">
                        <span>Total</span>
                        <span><?php echo formatearPrecio($total * 1.21); ?></span>
                    </div>

                    <form method="POST">
                        <button type="submit" name="confirmar_compra" class="btn-confirmar">Confirmar Compra</button>
                        <button type="submit" name="vaciar_carrito" class="btn-vaciar" onclick="return confirm('¿Está seguro?')">Vaciar Carrito</button>
                    </form>

                    <a href="catalogo.php" class="btn-continuar" style="display: block; text-align: center; margin-top: 10px;">← Continuar Comprando</a>
                </div>
            </div>
        <?php endif; ?>
    </div>
</body>

</html>