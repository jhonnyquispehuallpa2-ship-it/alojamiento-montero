<?php
require_once "conexion.php";

/* =====================================================
   GUARDAR UNA SOLICITUD DE RESERVA
   1. Registramos al cliente.
   2. Obtenemos su ID.
   3. Registramos la reserva como Pendiente.
   ===================================================== */

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: index.php");
    exit;
}

$nombre = $conexion->real_escape_string(trim($_POST["nombre"]));
$telefono = $conexion->real_escape_string(trim($_POST["telefono"]));
$correo = $conexion->real_escape_string(trim($_POST["correo"]));
$id_habitacion = (int) $_POST["id_habitacion"];
$fecha_ingreso = $conexion->real_escape_string($_POST["fecha_ingreso"]);
$fecha_salida = $conexion->real_escape_string($_POST["fecha_salida"]);
$cantidad_personas = (int) $_POST["cantidad_personas"];
$comentario = $conexion->real_escape_string(trim($_POST["comentario"]));

// Validación básica de fechas.
if ($fecha_salida <= $fecha_ingreso) {
    header("Location: index.php?mensaje=" . urlencode("La fecha de salida debe ser posterior a la fecha de ingreso.") . "#reservas");
    exit;
}

// Registramos los datos del cliente.
$sql_cliente = "INSERT INTO cliente (nombre, telefono, correo)
                VALUES ('$nombre', '$telefono', '$correo')";

if (!$conexion->query($sql_cliente)) {
    die("No se pudo registrar al cliente.");
}

$id_cliente = $conexion->insert_id;

// Registramos la solicitud de reserva.
$sql_reserva = "INSERT INTO reserva
                (id_cliente, id_habitacion, fecha_ingreso, fecha_salida,
                 cantidad_personas, comentario, estado)
                VALUES
                ($id_cliente, $id_habitacion, '$fecha_ingreso', '$fecha_salida',
                 $cantidad_personas, '$comentario', 'Pendiente')";

if ($conexion->query($sql_reserva)) {
    $mensaje = "Solicitud registrada correctamente. El administrador debe confirmar la reserva.";
} else {
    $mensaje = "Ocurrió un problema al registrar la reserva.";
}

header("Location: index.php?mensaje=" . urlencode($mensaje) . "#reservas");
exit;
?>
