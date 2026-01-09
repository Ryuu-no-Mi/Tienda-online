<?php
// Script para generar hashes válidos de bcrypt
// Este archivo es solo para desarrollo - ELIMINAR después de usar

// Generar hashes
$hash_admin = password_hash('admin123', PASSWORD_DEFAULT);
$hash_cliente = password_hash('cliente123', PASSWORD_DEFAULT);

echo "<!-- COPIAR ESTOS HASHES EN LA BD -->\n";
echo "Hash para admin (admin123): " . $hash_admin . "\n";
echo "Hash para cliente (cliente123): " . $hash_cliente . "\n";
echo "\n";
echo "Ejecuta estas consultas SQL:\n";
echo "UPDATE usuarios SET contraseña = '" . $hash_admin . "' WHERE usuario = 'admin';\n";
echo "UPDATE usuarios SET contraseña = '" . $hash_cliente . "' WHERE usuario = 'cliente';\n";
echo "\n";

// Verificar que funciona
echo "<!-- PRUEBA DE VERIFICACIÓN -->\n";
echo "password_verify('admin123', '" . $hash_admin . "'): " . (password_verify('admin123', $hash_admin) ? 'SÍ' : 'NO') . "\n";
echo "password_verify('cliente123', '" . $hash_cliente . "'): " . (password_verify('cliente123', $hash_cliente) ? 'SÍ' : 'NO') . "\n";
