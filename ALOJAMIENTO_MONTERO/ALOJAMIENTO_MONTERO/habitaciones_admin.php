<?php
require_once "verificar_sesion.php";
require_once "conexion.php";

$mensaje = "";
$editar = null;

// ELIMINAR una habitación.
if (isset($_GET["eliminar"])) {
    $id = (int) $_GET["eliminar"];
    $conexion->query("DELETE FROM habitacion WHERE id_habitacion=$id");
    header("Location: habitaciones_admin.php");
    exit;
}

// BUSCAR una habitación para editarla.
if (isset($_GET["editar"])) {
    $id = (int) $_GET["editar"];
    $resultado = $conexion->query("SELECT * FROM habitacion WHERE id_habitacion=$id");
    $editar = $resultado->fetch_assoc();
}

// GUARDAR o ACTUALIZAR.
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $id = (int) $_POST["id_habitacion"];
    $numero = $conexion->real_escape_string(trim($_POST["numero"]));
    $tipo = $conexion->real_escape_string(trim($_POST["tipo"]));
    $capacidad = (int) $_POST["capacidad"];
    $precio = (float) $_POST["precio"];
    $estado = $conexion->real_escape_string($_POST["estado"]);
    $imagen = $conexion->real_escape_string(trim($_POST["imagen"]));

    if ($id === 0) {
        $sql = "INSERT INTO habitacion
                (id_alojamiento, numero, tipo, capacidad, precio, estado, imagen)
                VALUES (1, '$numero', '$tipo', $capacidad, $precio, '$estado', '$imagen')";
        $mensaje = "Habitación registrada correctamente.";
    } else {
        $sql = "UPDATE habitacion SET
                numero='$numero', tipo='$tipo', capacidad=$capacidad,
                precio=$precio, estado='$estado', imagen='$imagen'
                WHERE id_habitacion=$id";
        $mensaje = "Habitación actualizada correctamente.";
    }

    $conexion->query($sql);
    $editar = null;
}

$habitaciones = $conexion->query("SELECT * FROM habitacion ORDER BY id_habitacion DESC");
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Habitaciones</title>
    <link rel="stylesheet" href="admin.css">
</head>
<body>
<header class="cabecera-admin"><h1>Gestión de habitaciones</h1></header>
<?php include "menu_admin.php"; ?>

<main class="contenedor-admin">
    <?php if ($mensaje): ?><div class="alerta exito"><?php echo $mensaje; ?></div><?php endif; ?>

    <section class="dos-columnas">
        <div>
            <h2><?php echo $editar ? "Modificar habitación" : "Registrar habitación"; ?></h2>

            <form class="form-admin" method="POST">
                <input type="hidden" name="id_habitacion" value="<?php echo $editar['id_habitacion'] ?? 0; ?>">

                <label>Número o código</label>
                <input type="text" name="numero" value="<?php echo htmlspecialchars($editar['numero'] ?? ''); ?>" required>

                <label>Tipo</label>
                <input type="text" name="tipo" value="<?php echo htmlspecialchars($editar['tipo'] ?? ''); ?>" placeholder="Ej.: Simple" required>

                <label>Capacidad</label>
                <input type="number" name="capacidad" min="1" value="<?php echo $editar['capacidad'] ?? 1; ?>" required>

                <label>Precio por noche (Bs.)</label>
                <input type="number" name="precio" min="0" step="0.01" value="<?php echo $editar['precio'] ?? ''; ?>" required>

                <label>Estado</label>
                <select name="estado">
                    <?php $estado_actual = $editar['estado'] ?? 'Disponible'; ?>
                    <option <?php if ($estado_actual === 'Disponible') echo 'selected'; ?>>Disponible</option>
                    <option <?php if ($estado_actual === 'Ocupada') echo 'selected'; ?>>Ocupada</option>
                    <option <?php if ($estado_actual === 'Mantenimiento') echo 'selected'; ?>>Mantenimiento</option>
                </select>

                <label>URL de imagen</label>
                <input type="text" name="imagen" value="<?php echo htmlspecialchars($editar['imagen'] ?? ''); ?>" placeholder="https://...">

                <button type="submit">Guardar</button>
                <?php if ($editar): ?><a class="boton-secundario" href="habitaciones_admin.php">Cancelar</a><?php endif; ?>
            </form>
        </div>

        <div>
            <h2>Habitaciones registradas</h2>
            <div class="tabla-responsive">
                <table>
                    <tr><th>N.º</th><th>Tipo</th><th>Precio</th><th>Estado</th><th>Acciones</th></tr>
                    <?php while ($fila = $habitaciones->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($fila['numero']); ?></td>
                        <td><?php echo htmlspecialchars($fila['tipo']); ?></td>
                        <td>Bs. <?php echo number_format($fila['precio'], 2); ?></td>
                        <td><?php echo htmlspecialchars($fila['estado']); ?></td>
                        <td>
                            <a href="?editar=<?php echo $fila['id_habitacion']; ?>">Editar</a> |
                            <a href="?eliminar=<?php echo $fila['id_habitacion']; ?>" onclick="return confirm('¿Eliminar esta habitación?')">Eliminar</a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </table>
            </div>
        </div>
    </section>
</main>
</body>
</html>
