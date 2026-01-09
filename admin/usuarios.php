<?php
/**
 * Gestión de usuarios - Admin
 * CAPA CLIENTE
 */

require_once __DIR__ . '/../business/UsuarioManager.php';

$usuarioManager = new UsuarioManager($conexion);

// Procesar acciones
$accion = $_GET['accion'] ?? '';
$id = $_GET['id'] ?? '';
$contenido = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['crear'])) {
        $resultado = $usuarioManager->registrar(
            $_POST['usuario'] ?? '',
            $_POST['email'] ?? '',
            $_POST['contraseña'] ?? '',
            $_POST['contraseña'] ?? '', // Confirmación es igual
            $_POST['telefono'] ?? '',
            $_POST['direccion'] ?? '',
            $_POST['rol'] ?? 'cliente'
        );

        if ($resultado['success']) {
            redirigirConMensaje('?pagina=usuarios', 'success', 'Usuario creado correctamente');
        } else {
            $_SESSION['errores'] = $resultado['errores'];
        }
    } elseif (isset($_POST['actualizar'])) {
        $resultado = $usuarioManager->actualizar(
            $_POST['id'],
            $_POST['email'] ?? '',
            $_POST['telefono'] ?? '',
            $_POST['direccion'] ?? '',
            $_POST['rol'] ?? 'cliente'
        );

        if ($resultado['success']) {
            redirigirConMensaje('?pagina=usuarios', 'success', 'Usuario actualizado correctamente');
        } else {
            $_SESSION['errores'] = $resultado['errores'];
        }
    } elseif (isset($_POST['eliminar'])) {
        $resultado = $usuarioManager->eliminar($_POST['id']);

        if ($resultado['success']) {
            redirigirConMensaje('?pagina=usuarios', 'success', 'Usuario eliminado correctamente');
        } else {
            $_SESSION['errores'] = $resultado['errores'];
        }
    }
}

if ($accion === 'crear') {
    $contenido = mostrarFormularioUsuario('crear');
} elseif ($accion === 'editar' && $id) {
    $usuario = $usuarioManager->obtenerPorId($id);
    if ($usuario) {
        $contenido = mostrarFormularioUsuario('editar', $usuario);
    } else {
        $contenido = '<div class="error">Usuario no encontrado</div>';
    }
} else {
    $contenido = mostrarListadoUsuarios($usuarioManager->obtenerTodos());
}

function mostrarListadoUsuarios($usuarios) {
    $html = '<h2>Gestión de Usuarios</h2>';
    $html .= '<a href="?pagina=usuarios&accion=crear" class="btn-nuevo">+ Crear Usuario</a>';

    if (empty($usuarios)) {
        $html .= '<p>No hay usuarios registrados</p>';
        return $html;
    }

    $html .= '<table>';
    $html .= '<thead><tr>';
    $html .= '<th>Usuario</th>';
    $html .= '<th>Email</th>';
    $html .= '<th>Teléfono</th>';
    $html .= '<th>Rol</th>';
    $html .= '<th>Estado</th>';
    $html .= '<th>Acciones</th>';
    $html .= '</tr></thead>';
    $html .= '<tbody>';

    foreach ($usuarios as $usuario) {
        $estado = $usuario['activo'] ? 'Activo' : 'Inactivo';
        $html .= '<tr>';
        $html .= '<td>' . escapar($usuario['usuario']) . '</td>';
        $html .= '<td>' . escapar($usuario['email']) . '</td>';
        $html .= '<td>' . escapar($usuario['telefono'] ?? '-') . '</td>';
        $html .= '<td>' . ucfirst($usuario['rol']) . '</td>';
        $html .= '<td>' . $estado . '</td>';
        $html .= '<td>';
        $html .= '<div class="boton-grupo">';
        $html .= '<a href="?pagina=usuarios&accion=editar&id=' . $usuario['id'] . '" class="btn-editar">Editar</a>';
        $html .= '<form method="POST" style="display:inline;" onsubmit="return confirm(\'¿Está seguro?\')">';
        $html .= '<input type="hidden" name="id" value="' . $usuario['id'] . '">';
        $html .= '<button type="submit" name="eliminar" class="btn-eliminar">Eliminar</button>';
        $html .= '</form>';
        $html .= '</div>';
        $html .= '</td>';
        $html .= '</tr>';
    }

    $html .= '</tbody></table>';
    return $html;
}

function mostrarFormularioUsuario($accion, $usuario = null) {
    $html = '<h2>' . ($accion === 'crear' ? 'Crear Usuario' : 'Editar Usuario') . '</h2>';

    if (isset($_SESSION['errores'])) {
        foreach ($_SESSION['errores'] as $error) {
            $html .= '<div class="error">' . escapar($error) . '</div>';
        }
        unset($_SESSION['errores']);
    }

    $html .= '<form method="POST">';

    if ($accion === 'editar') {
        $html .= '<input type="hidden" name="id" value="' . $usuario['id'] . '">';
    }

    $html .= '<div class="form-group">';
    $html .= '<label for="usuario">Usuario</label>';
    $html .= '<input type="text" id="usuario" name="usuario" value="' . ($usuario['usuario'] ?? '') . '" ' . ($accion === 'editar' ? 'disabled' : 'required') . '>';
    if ($accion === 'editar') {
        $html .= '<input type="hidden" name="usuario" value="' . escapar($usuario['usuario']) . '">';
    }
    $html .= '</div>';

    $html .= '<div class="form-group">';
    $html .= '<label for="email">Email</label>';
    $html .= '<input type="email" id="email" name="email" value="' . ($usuario['email'] ?? '') . '" required>';
    $html .= '</div>';

    if ($accion === 'crear') {
        $html .= '<div class="form-group">';
        $html .= '<label for="contraseña">Contraseña</label>';
        $html .= '<input type="password" id="contraseña" name="contraseña" required>';
        $html .= '</div>';
    }

    $html .= '<div class="form-group">';
    $html .= '<label for="telefono">Teléfono</label>';
    $html .= '<input type="tel" id="telefono" name="telefono" value="' . ($usuario['telefono'] ?? '') . '">';
    $html .= '</div>';

    $html .= '<div class="form-group">';
    $html .= '<label for="direccion">Dirección</label>';
    $html .= '<textarea id="direccion" name="direccion" rows="3">' . ($usuario['direccion'] ?? '') . '</textarea>';
    $html .= '</div>';

    $html .= '<div class="form-group">';
    $html .= '<label for="rol">Rol</label>';
    $html .= '<select id="rol" name="rol" required>';
    $html .= '<option value="cliente"' . ((($usuario['rol'] ?? '') === 'cliente') ? ' selected' : '') . '>Cliente</option>';
    $html .= '<option value="administrador"' . ((($usuario['rol'] ?? '') === 'administrador') ? ' selected' : '') . '>Administrador</option>';
    $html .= '</select>';
    $html .= '</div>';

    $html .= '<div class="form-acciones">';
    $html .= '<button type="submit" name="' . ($accion === 'crear' ? 'crear' : 'actualizar') . '" class="btn-guardar">Guardar</button>';
    $html .= '<a href="?pagina=usuarios" class="btn-cancelar" style="text-decoration: none; text-align: center; display: flex; align-items: center; justify-content: center;">Cancelar</a>';
    $html .= '</div>';

    $html .= '</form>';

    return $html;
}
?>
