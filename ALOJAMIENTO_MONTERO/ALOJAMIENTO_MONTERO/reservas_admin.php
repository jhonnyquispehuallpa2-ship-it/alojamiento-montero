<?php
require_once "verificar_sesion.php";
require_once "conexion.php";
require_once "funciones.php";

// Actualizar el estado de una reserva.
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $id = (int) $_POST["id_reserva"];
    $estado = $conexion->real_escape_string($_POST["estado"]);

    $conexion->query("UPDATE reserva SET estado='$estado' WHERE id_reserva=$id");
    header("Location: reservas_admin.php");
    exit;
}

$sql = "SELECT r.*, c.nombre AS cliente, c.telefono, c.correo,
               h.numero AS habitacion_numero, h.tipo AS habitacion_tipo
        FROM reserva r
        INNER JOIN cliente c ON r.id_cliente = c.id_cliente
        INNER JOIN habitacion h ON r.id_habitacion = h.id_habitacion
        ORDER BY r.id_reserva DESC";
$reservas = $conexion->query($sql);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reservas</title>
    <link rel="icon" href="img/favicon.svg" type="image/svg+xml">
    <link rel="stylesheet" href="<?php echo recurso('admin.css'); ?>">
</head>
<body class="admin">
<?php $titulo_pagina = "Gestión de reservas"; include "menu_admin.php"; ?>

<main class="contenedor-admin">
    <p class="texto-ayuda">Cambie el estado de cada solicitud: Pendiente, Confirmada, Cancelada o Finalizada.</p>
    <div class="tabla-responsive">
        <table>
            <thead>
                <tr>
                    <th>Cliente</th><th>Habitación y estadía</th><th>Estado</th><th>Actualizar</th>
                </tr>
            </thead>
            <tbody>
            <?php while ($fila = $reservas->fetch_assoc()): ?>
            <tr>
                <td data-label="Cliente">
                    <div>
                        <strong><?php echo htmlspecialchars($fila['cliente']); ?></strong><br>
                        <small><?php echo htmlspecialchars($fila['telefono']); ?></small>
                    </div>
                </td>
                <?php $noches = (int) round((strtotime($fila['fecha_salida']) - strtotime($fila['fecha_ingreso'])) / 86400); ?>
                <td data-label="Estadía">
                    <div>
                        <strong class="sin-salto"><?php echo htmlspecialchars($fila['habitacion_numero'] . ' - ' . $fila['habitacion_tipo']); ?></strong><br>
                        <span class="sin-salto"><?php echo date("d/m/Y", strtotime($fila['fecha_ingreso'])); ?> → <?php echo date("d/m/Y", strtotime($fila['fecha_salida'])); ?></span><br>
                        <small><?php echo $noches; ?> noche(s) · <?php echo $fila['cantidad_personas']; ?> persona(s)</small>
                    </div>
                </td>
                <td data-label="Estado"><span class="insignia <?php echo clase_estado($fila['estado']); ?>"><?php echo htmlspecialchars($fila['estado']); ?></span></td>
                <td data-label="Actualizar" class="celda-acciones">
                    <form method="POST" class="form-estado">
                        <input type="hidden" name="id_reserva" value="<?php echo $fila['id_reserva']; ?>">
                        <select name="estado" aria-label="Nuevo estado">
                            <?php foreach (["Pendiente","Confirmada","Cancelada","Finalizada"] as $estado): ?>
                                <option <?php if ($fila['estado'] === $estado) echo 'selected'; ?>><?php echo $estado; ?></option>
                            <?php endforeach; ?>
                        </select>
                        <button type="submit" title="Guardar estado"><?php echo icono("guardar"); ?> <span class="texto-boton">Guardar</span></button>
                    </form>
                </td>
            </tr>
            <?php endwhile; ?>
            <?php if ($reservas->num_rows === 0): ?>
                <tr><td colspan="4" class="tabla-vacia">Todavía no hay reservas registradas.</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</main>
<script src="<?php echo recurso('admin.js'); ?>"></script>
</body>
</html>
