<?php

/**
 * Clase DAO para Pedidos
 * CAPA DE DATOS - Acceso a BD
 */

class PedidoDAO
{
    private $conexion;

    public function __construct($conexion)
    {
        $this->conexion = $conexion;
    }

    // Crear nuevo pedido
    public function crear($usuario_id, $total)
    {
        $sql = "INSERT INTO pedidos (usuario_id, total) VALUES (?, ?)";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param('id', $usuario_id, $total);
        $stmt->execute();
        return $this->conexion->insert_id;
    }

    // Obtener pedido por ID
    public function obtenerPorId($id)
    {
        $sql = "SELECT * FROM pedidos WHERE id = ?";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param('i', $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    // Obtener todos los pedidos de un usuario
    public function obtenerPorUsuario($usuario_id)
    {
        $sql = "SELECT * FROM pedidos WHERE usuario_id = ? ORDER BY fecha_pedido DESC";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param('i', $usuario_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    // Obtener todos los pedidos (para admin)
    public function obtenerTodos()
    {
        $sql = "SELECT p.*, u.usuario FROM pedidos p JOIN usuarios u ON p.usuario_id = u.id ORDER BY p.fecha_pedido DESC";
        $result = $this->conexion->query($sql);
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    // Actualizar estado del pedido
    public function actualizarEstado($id, $estado)
    {
        $sql = "UPDATE pedidos SET estado = ? WHERE id = ?";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param('si', $estado, $id);
        return $stmt->execute();
    }

    // Crear detalle del pedido
    public function crearDetalle($pedido_id, $producto_id, $cantidad, $precio_unitario)
    {
        $subtotal = $cantidad * $precio_unitario;
        $sql = "INSERT INTO detallepedidos (pedido_id, producto_id, cantidad, precio_unitario, subtotal) VALUES (?, ?, ?, ?, ?)";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param('iiidd', $pedido_id, $producto_id, $cantidad, $precio_unitario, $subtotal);
        return $stmt->execute();
    }

    // Obtener detalles de un pedido
    public function obtenerDetalles($pedido_id)
    {
        $sql = "SELECT dp.*, p.nombre, p.marca FROM detallepedidos dp 
                JOIN productos p ON dp.producto_id = p.id WHERE dp.pedido_id = ?";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param('i', $pedido_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
}
