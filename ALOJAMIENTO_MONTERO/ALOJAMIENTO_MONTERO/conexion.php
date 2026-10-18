<?php
/* =====================================================
   CONEXIÓN A LA BASE DE DATOS
   Archivo sencillo para trabajar con XAMPP y MySQL.
   ===================================================== */

$servidor = "localhost";
$usuario_bd = "root";
$clave_bd = "";          // En XAMPP normalmente está vacío.
$base_datos = "alojamiento_montero";

$conexion = new mysqli($servidor, $usuario_bd, $clave_bd, $base_datos);

// Verificamos si ocurrió un error al conectar.
if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}

// Permite trabajar correctamente con tildes y la letra ñ.
$conexion->set_charset("utf8mb4");
?>
