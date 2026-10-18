<?php
require_once "verificar_sesion.php";
require_once "conexion.php";

$mensaje = "";

// Guardar los cambios del alojamiento.
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $id = (int) $_POST["id_alojamiento"];
    $nombre = $conexion->real_escape_string(trim($_POST["nombre"]));
    $direccion = $conexion->real_escape_string(trim($_POST["direccion"]));
    $telefono = $conexion->real_escape_string(trim($_POST["telefono"]));
    $correo = $conexion->real_escape_string(trim($_POST["correo"]));
    $descripcion = $conexion->real_escape_string(trim($_POST["descripcion"]));

    $sql = "UPDATE alojamiento SET
            nombre='$nombre',
            direccion='$direccion',
            telefono='$telefono',
            correo='$correo',
            descripcion='$descripcion'
            WHERE id_alojamiento=$id";

    if ($conexion->query($sql)) {
        $mensaje = "Datos actualizados correctamente.";
    }
}

$alojamiento = $conexion->query("SELECT * FROM alojamiento LIMIT 1")->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Datos del alojamiento</title>
    <link rel="stylesheet" href="admin.css">
</head>
<body>
<header class="cabecera-admin"><h1>Datos del alojamiento</h1></header>
<?php include "menu_admin.php"; ?>

<main class="contenedor-admin">
    <?php if ($mensaje): ?><div class="alerta exito"><?php echo $mensaje; ?></div><?php endif; ?>

    <form class="form-admin" method="POST">
        <input type="hidden" name="id_alojamiento" value="<?php echo $alojamiento['id_alojamiento']; ?>">

        <label>Nombre del alojamiento</label>
        <input type="text" name="nombre" value="<?php echo htmlspecialchars($alojamiento['nombre']); ?>" required>

        <label>Dirección</label>
        <input type="text" name="direccion" value="<?php echo htmlspecialchars($alojamiento['direccion']); ?>" required>

        <label>Teléfono</label>
        <input type="text" name="telefono" value="<?php echo htmlspecialchars($alojamiento['telefono']); ?>">

        <label>Correo</label>
        <input type="email" name="correo" value="<?php echo htmlspecialchars($alojamiento['correo']); ?>">

        <label>Descripción</label>
        <textarea name="descripcion" rows="5"><?php echo htmlspecialchars($alojamiento['descripcion']); ?></textarea>

        <button type="submit">Guardar cambios</button>
    </form>
</main>
</body>
</html>
