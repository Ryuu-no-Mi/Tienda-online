/**
 * SCRIPT SQL - Base de Datos Tienda de Zapatillas
 * Crear tablas: usuarios, productos, pedidos, detallepedidos
 */

-- Crear base de datos
CREATE DATABASE IF NOT EXISTS tienda_zapatillas CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE tienda_zapatillas;

-- Tabla de usuarios
CREATE TABLE usuarios (
    id INT PRIMARY KEY AUTO_INCREMENT,
    usuario VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    contraseña VARCHAR(255) NOT NULL,
    telefono VARCHAR(20),
    direccion VARCHAR(200),
    rol ENUM('cliente', 'administrador') DEFAULT 'cliente',
    fecha_registro DATETIME DEFAULT CURRENT_TIMESTAMP,
    activo TINYINT(1) DEFAULT 1
) ENGINE=InnoDB;

-- Tabla de productos (zapatillas)
CREATE TABLE productos (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nombre VARCHAR(100) NOT NULL,
    marca VARCHAR(50) NOT NULL,
    descripcion TEXT,
    precio DECIMAL(10, 2) NOT NULL,
    stock INT NOT NULL DEFAULT 0,
    imagen VARCHAR(255),
    fecha_creacion DATETIME DEFAULT CURRENT_TIMESTAMP,
    activo TINYINT(1) DEFAULT 1
) ENGINE=InnoDB;

-- Tabla de pedidos
CREATE TABLE pedidos (
    id INT PRIMARY KEY AUTO_INCREMENT,
    usuario_id INT NOT NULL,
    total DECIMAL(10, 2) NOT NULL,
    estado ENUM('pendiente', 'confirmado', 'entregado', 'cancelado') DEFAULT 'pendiente',
    fecha_pedido DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Tabla de detalles de pedidos
CREATE TABLE detallepedidos (
    id INT PRIMARY KEY AUTO_INCREMENT,
    pedido_id INT NOT NULL,
    producto_id INT NOT NULL,
    cantidad INT NOT NULL,
    precio_unitario DECIMAL(10, 2) NOT NULL,
    subtotal DECIMAL(10, 2) NOT NULL,
    FOREIGN KEY (pedido_id) REFERENCES pedidos(id) ON DELETE CASCADE,
    FOREIGN KEY (producto_id) REFERENCES productos(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Insertar usuario administrador de prueba
-- Usuario: admin | Contraseña: admin123
INSERT INTO usuarios (usuario, email, contraseña, telefono, direccion, rol) 
VALUES ('admin', 'admin@tienda.com', '$2y$10$YOixf6EKZc7h1DX7TKLKJu6A5Yj4q7zC8K5Q2L3M9N0P1R2S3T4U5', '666000000', 'Tienda Principal', 'administrador');

-- Insertar usuario cliente de prueba
-- Usuario: cliente | Contraseña: cliente123
INSERT INTO usuarios (usuario, email, contraseña, telefono, direccion, rol) 
VALUES ('cliente', 'cliente@ejemplo.com', '$2y$10$X8Y9Z0A1B2C3D4E5F6G7H8I9J0K1L2M3N4O5P6Q7R8S9T0U1V2W3', '666111111', 'Calle Principal 123', 'cliente');

-- Insertar productos de ejemplo
INSERT INTO productos (nombre, marca, descripcion, precio, stock, imagen) VALUES
('Nike Air Max 90', 'Nike', 'Zapatillas deportivas de correr, comodidad y estilo', 89.99, 50, 'nike_airmax90.jpg'),
('Adidas Ultraboost 21', 'Adidas', 'Zapatillas de alto rendimiento con Boost technology', 129.99, 30, 'adidas_ultraboost.jpg'),
('Puma RS-X', 'Puma', 'Zapatillas retro modernas para uso casual', 69.99, 45, 'puma_rsx.jpg'),
('New Balance 574', 'New Balance', 'Comodidad clásica y durabilidad garantizada', 79.99, 60, 'newbalance_574.jpg'),
('Reebok Nano 10', 'Reebok', 'Zapatillas de fitness y entrenamiento cruzado', 119.99, 25, 'reebok_nano10.jpg');

-- Índices para optimización
CREATE INDEX idx_usuario_rol ON usuarios(rol);
CREATE INDEX idx_producto_activo ON productos(activo);
CREATE INDEX idx_pedido_usuario ON pedidos(usuario_id);
CREATE INDEX idx_detallepedido_pedido ON detallepedidos(pedido_id);
