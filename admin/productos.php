<?php

require_once __DIR__ . '/../business/ProductoManager.php';

$productoManager = new ProductoManager($conexion);

// Procesar acciones
$accion = $_GET['accion'] ?? '';
$id = $_GET['id'] ?? '';
$contenido = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['crear'])) {
        $resultado = $productoManager->crear(
            $_POST['nombre'] ?? '',
            $_POST['marca'] ?? '',
            $_POST['descripcion'] ?? '',
            $_POST['precio'] ?? 0,
            $_POST['stock'] ?? 0,
            $_POST['imagen'] ?? null
        );

        if ($resultado['success']) {
            redirigirConMensaje('?pagina=productos', 'success', 'Producto creado correctamente');
        } else {
            $_SESSION['errores'] = $resultado['errores'];
        }
    } elseif (isset($_POST['actualizar'])) {
        $resultado = $productoManager->actualizar(
            $_POST['id'],
            $_POST['nombre'] ?? '',
            $_POST['marca'] ?? '',
            $_POST['descripcion'] ?? '',
            $_POST['precio'] ?? 0,
            $_POST['stock'] ?? 0,
            $_POST['imagen'] ?? null
        );

        if ($resultado['success']) {
            redirigirConMensaje('?pagina=productos', 'success', 'Producto actualizado correctamente');
        } else {
            $_SESSION['errores'] = $resultado['errores'];
        }
    } elseif (isset($_POST['eliminar'])) {
        $resultado = $productoManager->eliminar($_POST['id']);

        if ($resultado['success']) {
            redirigirConMensaje('?pagina=productos', 'success', 'Producto eliminado correctamente');
        } else {
            $_SESSION['errores'] = $resultado['errores'];
        }
    }
}

if ($accion === 'crear') {
    $contenido = mostrarFormularioProducto('crear');
} elseif ($accion === 'editar' && $id) {
    $producto = $productoManager->obtenerPorId($id);
    if ($producto) {
        $contenido = mostrarFormularioProducto('editar', $producto);
    } else {
        $contenido = '<div class="error">Producto no encontrado</div>';
    }
} else {
    $contenido = mostrarListadoProductos($productoManager->obtenerTodosAdmin());
}

function mostrarListadoProductos($productos)
{
    $html = '<h2>Gestión de Productos</h2>';
    $html .= '<a href="?pagina=productos&accion=crear" class="btn-nuevo">+ Crear Producto</a>';

    if (empty($productos)) {
        $html .= '<p>No hay productos registrados</p>';
        return $html;
    }

    $html .= '<table>';
    $html .= '<thead><tr>';
    $html .= '<th>Nombre</th>';
    $html .= '<th>Marca</th>';
    $html .= '<th>Precio</th>';
    $html .= '<th>Stock</th>';
    $html .= '<th>Estado</th>';
    $html .= '<th>Acciones</th>';
    $html .= '</tr></thead>';
    $html .= '<tbody>';

    foreach ($productos as $producto) {
        $estado = $producto['activo'] ? 'Activo' : 'Inactivo';
        $html .= '<tr>';
        $html .= '<td>' . escapar($producto['nombre']) . '</td>';
        $html .= '<td>' . escapar($producto['marca']) . '</td>';
        $html .= '<td>' . formatearPrecio($producto['precio']) . '</td>';
        $html .= '<td>' . $producto['stock'] . '</td>';
        $html .= '<td>' . $estado . '</td>';
        $html .= '<td>';
        $html .= '<div class="boton-grupo">';
        $html .= '<a href="?pagina=productos&accion=editar&id=' . $producto['id'] . '" class="btn-editar">Editar</a>';
        $html .= '<form method="POST" style="display:inline;" onsubmit="return confirm(\'¿Está seguro?\')">';
        $html .= '<input type="hidden" name="id" value="' . $producto['id'] . '">';
        $html .= '<button type="submit" name="eliminar" class="btn-eliminar">Eliminar</button>';
        $html .= '</form>';
        $html .= '</div>';
        $html .= '</td>';
        $html .= '</tr>';
    }

    $html .= '</tbody></table>';
    return $html;
}

function mostrarFormularioProducto($accion, $producto = null)
{
    $html = '<h2>' . ($accion === 'crear' ? 'Crear Producto' : 'Editar Producto') . '</h2>';

    if (isset($_SESSION['errores'])) {
        foreach ($_SESSION['errores'] as $error) {
            $html .= '<div class="error">' . escapar($error) . '</div>';
        }
        unset($_SESSION['errores']);
    }

    $html .= '<form method="POST">';

    if ($accion === 'editar') {
        $html .= '<input type="hidden" name="id" value="' . $producto['id'] . '">';
    }

    $html .= '<div class="form-group">';
    $html .= '<label for="nombre">Nombre</label>';
    $html .= '<input type="text" id="nombre" name="nombre" value="' . ($producto['nombre'] ?? '') . '" required>';
    $html .= '</div>';

    $html .= '<div class="form-group">';
    $html .= '<label for="marca">Marca</label>';
    $html .= '<input type="text" id="marca" name="marca" value="' . ($producto['marca'] ?? '') . '" required>';
    $html .= '</div>';

    $html .= '<div class="form-group">';
    $html .= '<label for="descripcion">Descripción</label>';
    $html .= '<textarea id="descripcion" name="descripcion" rows="4">' . ($producto['descripcion'] ?? '') . '</textarea>';
    $html .= '</div>';

    $html .= '<div class="form-group">';
    $html .= '<label for="precio">Precio (€)</label>';
    $html .= '<input type="number" id="precio" name="precio" value="' . ($producto['precio'] ?? 0) . '" step="0.01" min="0" required>';
    $html .= '</div>';

    $html .= '<div class="form-group">';
    $html .= '<label for="stock">Stock</label>';
    $html .= '<input type="number" id="stock" name="stock" value="' . ($producto['stock'] ?? 0) . '" min="0" required>';
    $html .= '</div>';

    $html .= '<div class="form-group">';
    $html .= '<label for="imagen">Imagen (nombre archivo)</label>';
    $html .= '<input type="text" id="imagen" name="imagen" value="' . ($producto['imagen'] ?? '') . '" placeholder="ej: nike_airmax90.jpg">';
    $html .= '</div>';

    $html .= '<div class="form-acciones">';
    $html .= '<button type="submit" name="' . ($accion === 'crear' ? 'crear' : 'actualizar') . '" class="btn-guardar">Guardar</button>';
    $html .= '<a href="?pagina=productos" class="btn-cancelar" style="text-decoration: none; text-align: center; display: flex; align-items: center; justify-content: center;">Cancelar</a>';
    $html .= '</div>';

    $html .= '</form>';

    return $html;
}
