<?php

require_once __DIR__ . '/../data/UsuarioDAO.php';

class UsuarioManager
{
    private $usuarioDAO;

    public function __construct($conexion)
    {
        $this->usuarioDAO = new UsuarioDAO($conexion);
    }

    // Registrar nuevo usuario
    public function registrar($usuario, $email, $contraseña, $contraseña_confirm, $telefono = '', $direccion = '')
    {
        $errores = [];

        // Validaciones
        if (strlen($usuario) < 3) {
            $errores[] = "El usuario debe tener al menos 3 caracteres";
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errores[] = "Email inválido";
        }
        if ($contraseña !== $contraseña_confirm) {
            $errores[] = "Las contraseñas no coinciden";
        }
        if (strlen($contraseña) < 6) {
            $errores[] = "La contraseña debe tener al menos 6 caracteres";
        }
        if ($this->usuarioDAO->existe($usuario)) {
            $errores[] = "El usuario ya existe";
        }
        if ($this->usuarioDAO->existeEmail($email)) {
            $errores[] = "El email ya está registrado";
        }

        if (!empty($errores)) {
            return ['success' => false, 'errores' => $errores];
        }

        $resultado = $this->usuarioDAO->crear($usuario, $email, $contraseña, $telefono, $direccion);

        if ($resultado) {
            return ['success' => true, 'mensaje' => 'Usuario registrado correctamente'];
        } else {
            return ['success' => false, 'errores' => ['Error al registrar usuario']];
        }
    }

    // Login de usuario
    public function login($usuario, $contraseña)
    {
        $errores = [];

        if (empty($usuario) || empty($contraseña)) {
            $errores[] = "Usuario y contraseña requeridos";
        }

        if (!empty($errores)) {
            return ['success' => false, 'errores' => $errores];
        }

        $usuarioData = $this->usuarioDAO->obtenerPorUsuario($usuario);

        if (!$usuarioData || !password_verify($contraseña, $usuarioData['contraseña'])) {
            return ['success' => false, 'errores' => ['Usuario o contraseña incorrectos']];
        }

        if (!$usuarioData['activo']) {
            return ['success' => false, 'errores' => ['Usuario inactivo']];
        }

        return ['success' => true, 'usuario' => $usuarioData];
    }

    // Obtener todos los usuarios (admin)
    public function obtenerTodos()
    {
        return $this->usuarioDAO->obtenerTodos();
    }

    // Actualizar datos de usuario
    public function actualizar($id, $email, $telefono, $direccion, $rol)
    {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return ['success' => false, 'errores' => ['Email inválido']];
        }

        $resultado = $this->usuarioDAO->actualizar($id, $email, $telefono, $direccion, $rol);

        if ($resultado) {
            return ['success' => true, 'mensaje' => 'Usuario actualizado correctamente'];
        } else {
            return ['success' => false, 'errores' => ['Error al actualizar usuario']];
        }
    }

    // Eliminar usuario (solo admin)
    public function eliminar($id)
    {
        $resultado = $this->usuarioDAO->eliminar($id);

        if ($resultado) {
            return ['success' => true, 'mensaje' => 'Usuario eliminado correctamente'];
        } else {
            return ['success' => false, 'errores' => ['Error al eliminar usuario']];
        }
    }

    // Obtener usuario por ID
    public function obtenerPorId($id)
    {
        return $this->usuarioDAO->obtenerPorId($id);
    }
}
