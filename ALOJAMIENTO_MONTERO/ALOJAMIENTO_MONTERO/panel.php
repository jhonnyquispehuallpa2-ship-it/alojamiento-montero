<?php
require_once "verificar_sesion.php";
require_once "conexion.php";

// Contadores sencillos para mostrar un resumen.
$total_habitaciones = $conexion->query("SELECT COUNT(*) AS total FROM habitacion")->fetch_assoc()["total"];
$total_clientes = $conexion->query("SELECT COUNT(*) AS total FROM cliente")->fetch_assoc()["total"];
$total_reservas = $conexion->query("SELECT COUNT(*) AS total FROM reserva")->fetch_assoc()["total"];
$pendientes = $conexion->query("SELECT COUNT(*) AS total FROM reserva WHERE estado='Pendiente'")->fetch_assoc()["total"];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel Administrativo</title>
    <link rel="stylesheet" href="admin.css">
</head>
<body>
<header class="cabecera-admin">
    <div>
        <h1>Panel Administrativo</h1>
        <p>Bienvenido, <?php echo htmlspecialchars($_SESSION["nombre_usuario"]); ?></p>
    </div>
</header>

<?php include "menu_admin.php"; ?>

<main class="contenedor-admin">
    <h2>Resumen del sistema</h2>

    <div class="tarjetas-resumen">
        <div class="tarjeta"><strong><?php echo $total_habitaciones; ?></strong><span>Habitaciones</span></div>
        <div class="tarjeta"><strong><?php echo $total_clientes; ?></strong><span>Clientes</span></div>
        <div class="tarjeta"><strong><?php echo $total_reservas; ?></strong><span>Reservas</span></div>
        <div class="tarjeta"><strong><?php echo $pendientes; ?></strong><span>Pendientes</span></div>
    </div>

    <section class="panel-ayuda">
        <h3>¿Qué puede hacer desde el panel?</h3>
        <p>Puede actualizar los datos del alojamiento, registrar habitaciones y servicios, revisar clientes, controlar las reservas y registrar usuarios autorizados.</p>
    </section>
</main>
</body>
</html>
