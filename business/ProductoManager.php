<?php

require_once __DIR__ . '/../data/ProductoDAO.php';

class ProductoManager
{
    private $productoDAO;

    public function __construct($conexion)
    {
        $this->productoDAO = new ProductoDAO($conexion);
    }

    // Crear producto (admin)
    public function crear($nombre, $marca, $descripcion, $precio, $stock, $imagen = null)
    {
        $errores = [];

        // Validaciones
        if (empty($nombre) || strlen($nombre) < 3) {
            $errores[] = "Nombre de producto inválido";
        }
        if (empty($marca)) {
            $errores[] = "Marca requerida";
        }
        if ($precio <= 0) {
            $errores[] = "Precio debe ser mayor a 0";
        }
        if ($stock < 0) {
            $errores[] = "Stock no puede ser negativo";
        }

        if (!empty($errores)) {
            return ['success' => false, 'errores' => $errores];
        }

        $resultado = $this->productoDAO->crear($nombre, $marca, $descripcion, $precio, $stock, $imagen);

        if ($resultado) {
            return ['success' => true, 'mensaje' => 'Producto creado correctamente'];
        } else {
            return ['success' => false, 'errores' => ['Error al crear producto']];
        }
    }

    // Actualizar producto (admin)
    public function actualizar($id, $nombre, $marca, $descripcion, $precio, $stock, $imagen = null)
    {
        $errores = [];

        if (empty($nombre) || strlen($nombre) < 3) {
            $errores[] = "Nombre de producto inválido";
        }
        if (empty($marca)) {
            $errores[] = "Marca requerida";
        }
        if ($precio <= 0) {
            $errores[] = "Precio debe ser mayor a 0";
        }
        if ($stock < 0) {
            $errores[] = "Stock no puede ser negativo";
        }

        if (!empty($errores)) {
            return ['success' => false, 'errores' => $errores];
        }

        $resultado = $this->productoDAO->actualizar($id, $nombre, $marca, $descripcion, $precio, $stock, $imagen);

        if ($resultado) {
            return ['success' => true, 'mensaje' => 'Producto actualizado correctamente'];
        } else {
            return ['success' => false, 'errores' => ['Error al actualizar producto']];
        }
    }

    // Eliminar producto (admin)
    public function eliminar($id)
    {
        $resultado = $this->productoDAO->eliminar($id);

        if ($resultado) {
            return ['success' => true, 'mensaje' => 'Producto eliminado correctamente'];
        } else {
            return ['success' => false, 'errores' => ['Error al eliminar producto']];
        }
    }

    // Obtener todos los productos (cliente)
    public function obtenerTodos()
    {
        return $this->productoDAO->obtenerTodos();
    }

    // Obtener todos los productos (admin)
    public function obtenerTodosAdmin()
    {
        return $this->productoDAO->obtenerTodosAdmin();
    }

    // Obtener producto por ID
    public function obtenerPorId($id)
    {
        return $this->productoDAO->obtenerPorId($id);
    }

    // Buscar por marca
    public function buscarPorMarca($marca)
    {
        if (empty($marca)) {
            return [];
        }
        return $this->productoDAO->buscarPorMarca($marca);
    }
}
