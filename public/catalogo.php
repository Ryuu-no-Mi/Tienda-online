<?php

/**
 * Catálogo de productos - Cliente
 * CAPA CLIENTE
 */

session_start();
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/funciones.php';
require_once __DIR__ . '/../business/ProductoManager.php';
require_once __DIR__ . '/../business/CarritoManager.php';

verificarCliente();

$productoManager = new ProductoManager($conexion);
$carritoManager = new CarritoManager($conexion);

// Procesar acciones
$mensaje = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['agregar_producto'])) {
    $resultado = $carritoManager->agregarProducto($_POST['producto_id'], $_POST['cantidad'] ?? 1);
    $mensaje = $resultado['mensaje'] ?? implode(', ', $resultado['errores'] ?? []);
}

// Obtener productos
$productos = $productoManager->obtenerTodos();
$carrito = $carritoManager->obtenerCarrito();
$cantidad_carrito = count($carrito);
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Catálogo - Tienda de Zapatillas</title>
    <link rel="stylesheet" href="../assets/css/styles-catalogo.css">
</head>
</head>

<body>
    <header>
        <div class="header-content">
            <h1>👟 Tienda de Zapatillas</h1>
            <div class="header-right">
                <a href="catalogo.php" class="carrito-link" style="background: #27ae60;">
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
        <?php if ($mensaje): ?>
            <div class="mensaje <?php echo (strpos($mensaje, 'correctamente') !== false) ? 'success' : 'error'; ?>">
                <?php echo escapar($mensaje); ?>
            </div>
        <?php endif; ?>

        <h2 class="titulo-seccion">Catálogo de Zapatillas</h2>

        <?php if (empty($productos)): ?>
            <p style="text-align: center; padding: 40px; background: white; border-radius: 8px;">
                No hay productos disponibles en este momento.
            </p>
        <?php else: ?>
            <div class="productos-grid">
                <?php foreach ($productos as $producto): ?>
                    <div class="producto-card">
                        <div class="producto-imagen">
                            <img src="<?php echo obtenerUrlImagen($producto['imagen']); ?>" alt="<?php echo escapar($producto['nombre']); ?>" style="width: 100%; height: 200px; object-fit: cover; border-radius: 8px 8px 0 0;">
                        </div>
                        <div class="producto-info">
                            <div class="producto-nombre"><?php echo escapar($producto['nombre']); ?></div>
                            <div class="producto-marca"><?php echo escapar($producto['marca']); ?></div>
                            <div class="producto-descripcion"><?php echo escapar(substr($producto['descripcion'] ?? '', 0, 80)); ?></div>
                            <div class="producto-precio"><?php echo formatearPrecio($producto['precio']); ?></div>
                            <div class="producto-stock">
                                Stock:
                                <?php
                                if ($producto['stock'] > 0) {
                                    echo '<span style="color: #27ae60; font-weight: 600;">' . $producto['stock'] . '</span>';
                                } else {
                                    echo '<span style="color: #e74c3c; font-weight: 600;">Agotado</span>';
                                }
                                ?>
                            </div>
                            <form method="POST" style="display: flex; gap: 10px;">
                                <input type="hidden" name="producto_id" value="<?php echo $producto['id']; ?>">
                                <input type="number" name="cantidad" class="cantidad-input" value="1" min="1" max="<?php echo $producto['stock']; ?>">
                                <button type="submit" name="agregar_producto" class="btn-agregar" <?php echo ($producto['stock'] <= 0) ? 'disabled' : ''; ?>>
                                    <?php echo ($producto['stock'] > 0) ? 'Agregar' : 'Agotado'; ?>
                                </button>
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</body>

</html>