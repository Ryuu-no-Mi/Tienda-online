<?php

/**
 * Cerrar sesión - Cliente
 */

session_start();
session_destroy();
header('Location: ../index.php');
exit;
