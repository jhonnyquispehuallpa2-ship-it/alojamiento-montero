<?php
require_once "verificar_sesion.php";
require_once "conexion.php";
require_once "funciones.php";

$clientes = $conexion->query("SELECT * FROM cliente ORDER BY id_cliente DESC");
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Clientes</title>
    <link rel="icon" href="img/favicon.svg" type="image/svg+xml">
    <link rel="stylesheet" href="<?php echo recurso('admin.css'); ?>">
</head>
<body class="admin">
<?php $titulo_pagina = "Clientes registrados"; include "menu_admin.php"; ?>

<main class="contenedor-admin">
    <p class="texto-ayuda">Los clientes se registran automáticamente cuando envían una solicitud de reserva.</p>
    <div class="tabla-responsive">
        <table>
            <thead>
                <tr><th>ID</th><th>Nombre</th><th>Teléfono</th><th>Correo</th></tr>
            </thead>
            <tbody>
            <?php while ($fila = $clientes->fetch_assoc()): ?>
            <tr>
                <td data-label="ID"><?php echo $fila['id_cliente']; ?></td>
                <td data-label="Nombre"><strong><?php echo htmlspecialchars($fila['nombre']); ?></strong></td>
                <td data-label="Teléfono"><a href="tel:<?php echo htmlspecialchars($fila['telefono']); ?>"><?php echo htmlspecialchars($fila['telefono']); ?></a></td>
                <td data-label="Correo"><a href="mailto:<?php echo htmlspecialchars($fila['correo']); ?>"><?php echo htmlspecialchars($fila['correo']); ?></a></td>
            </tr>
            <?php endwhile; ?>
            <?php if ($clientes->num_rows === 0): ?>
                <tr><td colspan="4" class="tabla-vacia">Todavía no hay clientes registrados.</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</main>
<script src="<?php echo recurso('admin.js'); ?>"></script>
</body>
</html>
