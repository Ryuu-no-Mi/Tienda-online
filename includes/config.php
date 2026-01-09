<?php

/**
 * Configuración y conexión a la base de datos
 * CAPA DE DATOS - Acceso a BD
 */

// Configuración de la BD
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'tienda_zapatillas');

// Crear conexión
$conexion = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Verificar conexión
if ($conexion->connect_error) {
    die('Error de conexión: ' . $conexion->connect_error);
}

// Configurar charset UTF-8
$conexion->set_charset('utf8mb4');

// Variables globales
$conexion_global = $conexion;
