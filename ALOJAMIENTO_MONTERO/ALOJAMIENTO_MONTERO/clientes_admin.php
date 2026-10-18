<?php
require_once "verificar_sesion.php";
require_once "conexion.php";

$clientes = $conexion->query("SELECT * FROM cliente ORDER BY id_cliente DESC");
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Clientes</title><link rel="stylesheet" href="admin.css">
</head>
<body>
<header class="cabecera-admin"><h1>Clientes registrados</h1></header>
<?php include "menu_admin.php"; ?>
<main class="contenedor-admin">
    <p>Los clientes se registran automáticamente cuando envían una solicitud de reserva.</p>
    <div class="tabla-responsive">
        <table>
            <tr><th>ID</th><th>Nombre</th><th>Teléfono</th><th>Correo</th></tr>
            <?php while ($fila = $clientes->fetch_assoc()): ?>
            <tr>
                <td><?php echo $fila['id_cliente']; ?></td>
                <td><?php echo htmlspecialchars($fila['nombre']); ?></td>
                <td><?php echo htmlspecialchars($fila['telefono']); ?></td>
                <td><?php echo htmlspecialchars($fila['correo']); ?></td>
            </tr>
            <?php endwhile; ?>
        </table>
    </div>
</main>
</body>
</html>
