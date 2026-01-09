<?php

/**
 * Clase DAO para Productos
 * CAPA DE DATOS - Acceso a BD
 */

class ProductoDAO
{
    private $conexion;

    public function __construct($conexion)
    {
        $this->conexion = $conexion;
    }

    // Obtener producto por ID
    public function obtenerPorId($id)
    {
        $sql = "SELECT * FROM productos WHERE id = ? AND activo = 1";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param('i', $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    // Obtener todos los productos activos
    public function obtenerTodos()
    {
        $sql = "SELECT * FROM productos WHERE activo = 1 ORDER BY fecha_creacion DESC";
        $result = $this->conexion->query($sql);
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    // Obtener todos los productos (incluyendo inactivos, para admin)
    public function obtenerTodosAdmin()
    {
        $sql = "SELECT * FROM productos ORDER BY fecha_creacion DESC";
        $result = $this->conexion->query($sql);
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    // Crear nuevo producto
    public function crear($nombre, $marca, $descripcion, $precio, $stock, $imagen)
    {
        $sql = "INSERT INTO productos (nombre, marca, descripcion, precio, stock, imagen) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param('sssdi' . 's', $nombre, $marca, $descripcion, $precio, $stock, $imagen);
        return $stmt->execute();
    }

    // Actualizar producto
    public function actualizar($id, $nombre, $marca, $descripcion, $precio, $stock, $imagen = null)
    {
        if ($imagen) {
            $sql = "UPDATE productos SET nombre = ?, marca = ?, descripcion = ?, precio = ?, stock = ?, imagen = ? WHERE id = ?";
            $stmt = $this->conexion->prepare($sql);
            $stmt->bind_param('sssdi' . 'si', $nombre, $marca, $descripcion, $precio, $stock, $imagen, $id);
        } else {
            $sql = "UPDATE productos SET nombre = ?, marca = ?, descripcion = ?, precio = ?, stock = ? WHERE id = ?";
            $stmt = $this->conexion->prepare($sql);
            $stmt->bind_param('sssdi' . 'i', $nombre, $marca, $descripcion, $precio, $stock, $id);
        }
        return $stmt->execute();
    }

    // Eliminar producto (desactivar)
    public function eliminar($id)
    {
        $sql = "UPDATE productos SET activo = 0 WHERE id = ?";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param('i', $id);
        return $stmt->execute();
    }

    // Actualizar stock
    public function actualizarStock($id, $cantidad)
    {
        $sql = "UPDATE productos SET stock = stock - ? WHERE id = ? AND stock >= ?";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param('ii' . 'i', $cantidad, $id, $cantidad);
        return $stmt->execute();
    }

    // Buscar productos por marca
    public function buscarPorMarca($marca)
    {
        $sql = "SELECT * FROM productos WHERE marca LIKE ? AND activo = 1";
        $stmt = $this->conexion->prepare($sql);
        $param = '%' . $marca . '%';
        $stmt->bind_param('s', $param);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
}
