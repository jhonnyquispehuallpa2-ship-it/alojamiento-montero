<?php
require_once "verificar_sesion.php";
require_once "conexion.php";

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
    <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reservas</title><link rel="stylesheet" href="admin.css">
</head>
<body>
<header class="cabecera-admin"><h1>Gestión de reservas</h1></header>
<?php include "menu_admin.php"; ?>
<main class="contenedor-admin">
    <div class="tabla-responsive">
        <table>
            <tr>
                <th>Cliente</th><th>Habitación</th><th>Ingreso</th><th>Salida</th>
                <th>Personas</th><th>Estado</th><th>Actualizar</th>
            </tr>
            <?php while ($fila = $reservas->fetch_assoc()): ?>
            <tr>
                <td>
                    <?php echo htmlspecialchars($fila['cliente']); ?><br>
                    <small><?php echo htmlspecialchars($fila['telefono']); ?></small>
                </td>
                <td><?php echo htmlspecialchars($fila['habitacion_numero'] . ' - ' . $fila['habitacion_tipo']); ?></td>
                <td><?php echo $fila['fecha_ingreso']; ?></td>
                <td><?php echo $fila['fecha_salida']; ?></td>
                <td><?php echo $fila['cantidad_personas']; ?></td>
                <td><?php echo htmlspecialchars($fila['estado']); ?></td>
                <td>
                    <form method="POST" class="form-estado">
                        <input type="hidden" name="id_reserva" value="<?php echo $fila['id_reserva']; ?>">
                        <select name="estado">
                            <?php foreach (["Pendiente","Confirmada","Cancelada","Finalizada"] as $estado): ?>
                                <option <?php if ($fila['estado'] === $estado) echo 'selected'; ?>><?php echo $estado; ?></option>
                            <?php endforeach; ?>
                        </select>
                        <button type="submit">Guardar</button>
                    </form>
                </td>
            </tr>
            <?php endwhile; ?>
        </table>
    </div>
</main>
</body>
</html>
