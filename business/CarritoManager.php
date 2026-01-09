<?php
require_once __DIR__ . '/../data/ProductoDAO.php';
require_once __DIR__ . '/../data/PedidoDAO.php';

class CarritoManager
{
    private $productoDAO;
    private $pedidoDAO;

    public function __construct($conexion)
    {
        $this->productoDAO = new ProductoDAO($conexion);
        $this->pedidoDAO = new PedidoDAO($conexion);
    }

    // Inicializar carrito en sesión
    public function inicializarCarrito()
    {
        if (!isset($_SESSION['carrito'])) {
            $_SESSION['carrito'] = [];
        }
    }

    // Agregar producto al carrito
    public function agregarProducto($producto_id, $cantidad = 1)
    {
        $this->inicializarCarrito();

        if ($cantidad <= 0) {
            return ['success' => false, 'errores' => ['Cantidad inválida']];
        }

        $producto = $this->productoDAO->obtenerPorId($producto_id);

        if (!$producto) {
            return ['success' => false, 'errores' => ['Producto no encontrado']];
        }

        if ($producto['stock'] < $cantidad) {
            return ['success' => false, 'errores' => ['Stock insuficiente']];
        }

        if (isset($_SESSION['carrito'][$producto_id])) {
            $_SESSION['carrito'][$producto_id]['cantidad'] += $cantidad;
        } else {
            $_SESSION['carrito'][$producto_id] = [
                'id' => $producto_id,
                'nombre' => $producto['nombre'],
                'marca' => $producto['marca'],
                'precio' => $producto['precio'],
                'cantidad' => $cantidad,
                'imagen' => $producto['imagen']
            ];
        }

        return ['success' => true, 'mensaje' => 'Producto agregado al carrito'];
    }

    // Obtener carrito
    public function obtenerCarrito()
    {
        $this->inicializarCarrito();
        return $_SESSION['carrito'];
    }

    // Actualizar cantidad de producto
    public function actualizarCantidad($producto_id, $cantidad)
    {
        $this->inicializarCarrito();

        if (!isset($_SESSION['carrito'][$producto_id])) {
            return ['success' => false, 'errores' => ['Producto no en carrito']];
        }

        if ($cantidad <= 0) {
            unset($_SESSION['carrito'][$producto_id]);
            return ['success' => true, 'mensaje' => 'Producto removido del carrito'];
        }

        $_SESSION['carrito'][$producto_id]['cantidad'] = $cantidad;
        return ['success' => true, 'mensaje' => 'Cantidad actualizada'];
    }

    // Eliminar producto del carrito
    public function eliminarProducto($producto_id)
    {
        $this->inicializarCarrito();

        if (!isset($_SESSION['carrito'][$producto_id])) {
            return ['success' => false, 'errores' => ['Producto no en carrito']];
        }

        unset($_SESSION['carrito'][$producto_id]);
        return ['success' => true, 'mensaje' => 'Producto removido del carrito'];
    }

    // Calcular total del carrito
    public function calcularTotal()
    {
        $this->inicializarCarrito();
        $total = 0;

        foreach ($_SESSION['carrito'] as $producto) {
            $total += $producto['precio'] * $producto['cantidad'];
        }

        return $total;
    }

    // Vaciar carrito
    public function vaciarCarrito()
    {
        $_SESSION['carrito'] = [];
        return ['success' => true, 'mensaje' => 'Carrito vaciado'];
    }

    // Confirmar compra
    public function confirmarCompra($usuario_id)
    {
        $this->inicializarCarrito();

        if (empty($_SESSION['carrito'])) {
            return ['success' => false, 'errores' => ['Carrito vacío']];
        }

        $total = $this->calcularTotal();

        // Crear pedido
        $pedido_id = $this->pedidoDAO->crear($usuario_id, $total);

        if (!$pedido_id) {
            return ['success' => false, 'errores' => ['Error al crear pedido']];
        }

        // Agregar detalles del pedido
        foreach ($_SESSION['carrito'] as $producto) {
            $this->pedidoDAO->crearDetalle(
                $pedido_id,
                $producto['id'],
                $producto['cantidad'],
                $producto['precio']
            );
        }

        // Vaciar carrito
        $_SESSION['carrito'] = [];

        return ['success' => true, 'mensaje' => 'Compra confirmada', 'pedido_id' => $pedido_id];
    }

    // Obtener historial de pedidos
    public function obtenerHistorial($usuario_id)
    {
        return $this->pedidoDAO->obtenerPorUsuario($usuario_id);
    }
}
