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
    <link rel="stylesheet" href="../assets/css/styles-carrito.css">
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