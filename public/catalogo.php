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
            max-width: 1200px;
            margin: 30px auto;
            padding: 0 20px;
        }

        .mensaje {
            padding: 12px;
            border-radius: 5px;
            margin-bottom: 20px;
            text-align: center;
        }

        .mensaje.success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .mensaje.error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .titulo-seccion {
            color: #2c3e50;
            margin-bottom: 30px;
            border-bottom: 3px solid #3498db;
            padding-bottom: 10px;
        }

        .productos-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 25px;
            margin-bottom: 40px;
        }

        .producto-card {
            background: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s, box-shadow 0.3s;
        }

        .producto-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.15);
        }

        .producto-imagen {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            height: 200px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 60px;
        }

        .producto-info {
            padding: 20px;
        }

        .producto-nombre {
            font-size: 18px;
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 5px;
        }

        .producto-marca {
            color: #7f8c8d;
            font-size: 14px;
            margin-bottom: 10px;
        }

        .producto-descripcion {
            color: #555;
            font-size: 13px;
            line-height: 1.4;
            margin-bottom: 15px;
            min-height: 40px;
        }

        .producto-precio {
            font-size: 24px;
            font-weight: bold;
            color: #27ae60;
            margin-bottom: 15px;
        }

        .producto-stock {
            font-size: 13px;
            color: #7f8c8d;
            margin-bottom: 15px;
        }

        .producto-acciones {
            display: flex;
            gap: 10px;
        }

        .cantidad-input {
            width: 60px;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 5px;
            text-align: center;
        }

        .btn-agregar {
            flex: 1;
            padding: 10px;
            background: #3498db;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: 600;
            transition: background 0.3s;
        }

        .btn-agregar:hover {
            background: #2980b9;
        }

        .btn-agregar:disabled {
            background: #95a5a6;
            cursor: not-allowed;
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

            .productos-grid {
                grid-template-columns: 1fr;
            }

            .carrito-link {
                width: 100%;
                text-align: center;
            }
        }
    </style>
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
                        <div class="producto-imagen">👟</div>
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
