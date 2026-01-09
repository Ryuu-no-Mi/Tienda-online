<?php

/**
 * Clase DAO para Usuarios
 * CAPA DE DATOS - Acceso a BD
 */

class UsuarioDAO
{
    private $conexion;

    public function __construct($conexion)
    {
        $this->conexion = $conexion;
    }

    // Obtener usuario por nombre de usuario
    public function obtenerPorUsuario($usuario)
    {
        $sql = "SELECT * FROM usuarios WHERE usuario = ?";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param('s', $usuario);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    // Obtener usuario por ID
    public function obtenerPorId($id)
    {
        $sql = "SELECT * FROM usuarios WHERE id = ?";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param('i', $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    // Obtener todos los usuarios
    public function obtenerTodos()
    {
        $sql = "SELECT id, usuario, email, telefono, direccion, rol, fecha_registro, activo FROM usuarios ORDER BY fecha_registro DESC";
        $result = $this->conexion->query($sql);
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    // Crear nuevo usuario
    public function crear($usuario, $email, $contraseña, $telefono, $direccion, $rol = 'cliente')
    {
        $hash = password_hash($contraseña, PASSWORD_BCRYPT);
        $sql = "INSERT INTO usuarios (usuario, email, contraseña, telefono, direccion, rol) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param('ssssss', $usuario, $email, $hash, $telefono, $direccion, $rol);
        return $stmt->execute();
    }

    // Actualizar usuario
    public function actualizar($id, $email, $telefono, $direccion, $rol)
    {
        $sql = "UPDATE usuarios SET email = ?, telefono = ?, direccion = ?, rol = ? WHERE id = ?";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param('ssssi', $email, $telefono, $direccion, $rol, $id);
        return $stmt->execute();
    }

    // Eliminar usuario (desactivar)
    public function eliminar($id)
    {
        $sql = "UPDATE usuarios SET activo = 0 WHERE id = ?";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param('i', $id);
        return $stmt->execute();
    }

    // Verificar si usuario existe
    public function existe($usuario)
    {
        $sql = "SELECT id FROM usuarios WHERE usuario = ?";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param('s', $usuario);
        $stmt->execute();
        return $stmt->get_result()->num_rows > 0;
    }

    // Verificar si email existe
    public function existeEmail($email)
    {
        $sql = "SELECT id FROM usuarios WHERE email = ?";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param('s', $email);
        $stmt->execute();
        return $stmt->get_result()->num_rows > 0;
    }
}
