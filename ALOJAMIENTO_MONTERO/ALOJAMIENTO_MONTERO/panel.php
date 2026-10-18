<?php
require_once "verificar_sesion.php";
require_once "conexion.php";
require_once "funciones.php";

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
    <link rel="icon" href="img/favicon.svg" type="image/svg+xml">
    <link rel="stylesheet" href="<?php echo recurso('admin.css'); ?>">
</head>
<body class="admin">
<?php $titulo_pagina = "Panel Administrativo"; include "menu_admin.php"; ?>

<main class="contenedor-admin">
    <div class="saludo">
        <h2>Bienvenido, <?php echo htmlspecialchars($_SESSION["nombre_usuario"]); ?></h2>
        <p>Resumen del sistema</p>
    </div>

    <div class="tarjetas-resumen">
        <a class="tarjeta" href="habitaciones_admin.php">
            <span class="tarjeta-icono"><?php echo icono("cama"); ?></span>
            <div><strong><?php echo $total_habitaciones; ?></strong><span>Habitaciones</span></div>
        </a>
        <a class="tarjeta" href="clientes_admin.php">
            <span class="tarjeta-icono azul"><?php echo icono("grupo"); ?></span>
            <div><strong><?php echo $total_clientes; ?></strong><span>Clientes</span></div>
        </a>
        <a class="tarjeta" href="reservas_admin.php">
            <span class="tarjeta-icono morado"><?php echo icono("calendario"); ?></span>
            <div><strong><?php echo $total_reservas; ?></strong><span>Reservas</span></div>
        </a>
        <a class="tarjeta" href="reservas_admin.php">
            <span class="tarjeta-icono aviso"><?php echo icono("reloj"); ?></span>
            <div><strong><?php echo $pendientes; ?></strong><span>Pendientes</span></div>
        </a>
    </div>

    <h2>Accesos rápidos</h2>
    <div class="accesos">
        <a class="acceso" href="habitaciones_admin.php"><?php echo icono("mas"); ?> Registrar habitación <?php echo icono("flecha-derecha"); ?></a>
        <a class="acceso" href="reservas_admin.php"><?php echo icono("calendario"); ?> Revisar reservas <?php echo icono("flecha-derecha"); ?></a>
        <a class="acceso" href="servicios_admin.php"><?php echo icono("estrella"); ?> Gestionar servicios <?php echo icono("flecha-derecha"); ?></a>
        <a class="acceso" href="alojamiento_admin.php"><?php echo icono("hotel"); ?> Datos del alojamiento <?php echo icono("flecha-derecha"); ?></a>
    </div>

    <section class="panel-ayuda">
        <h3>¿Qué puede hacer desde el panel?</h3>
        <p>Puede actualizar los datos del alojamiento, registrar habitaciones y servicios, revisar clientes, controlar las reservas y registrar usuarios autorizados.</p>
    </section>
</main>
<script src="<?php echo recurso('admin.js'); ?>"></script>
</body>
</html>
