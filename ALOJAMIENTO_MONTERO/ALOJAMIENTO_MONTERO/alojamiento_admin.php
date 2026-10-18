<?php
require_once "verificar_sesion.php";
require_once "conexion.php";
require_once "funciones.php";

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
    <link rel="icon" href="img/favicon.svg" type="image/svg+xml">
    <link rel="stylesheet" href="<?php echo recurso('admin.css'); ?>">
</head>
<body class="admin">
<?php $titulo_pagina = "Datos del alojamiento"; include "menu_admin.php"; ?>

<main class="contenedor-admin">
    <?php if ($mensaje): ?><div class="alerta exito" role="status"><?php echo icono("check"); ?> <?php echo $mensaje; ?></div><?php endif; ?>

    <p class="texto-ayuda">Estos datos se muestran en la página pública (portada, contacto y pie de página).</p>

    <form class="form-admin form-ancho" method="POST">
        <input type="hidden" name="id_alojamiento" value="<?php echo $alojamiento['id_alojamiento']; ?>">

        <label for="nombre">Nombre del alojamiento</label>
        <input type="text" name="nombre" id="nombre" value="<?php echo htmlspecialchars($alojamiento['nombre']); ?>" required>

        <label for="direccion">Dirección</label>
        <input type="text" name="direccion" id="direccion" value="<?php echo htmlspecialchars($alojamiento['direccion']); ?>" required>

        <label for="telefono">Teléfono</label>
        <input type="tel" name="telefono" id="telefono" inputmode="tel" value="<?php echo htmlspecialchars($alojamiento['telefono']); ?>">
        <p class="ayuda">También se usa para el botón de WhatsApp.</p>

        <label for="correo">Correo</label>
        <input type="email" name="correo" id="correo" value="<?php echo htmlspecialchars($alojamiento['correo']); ?>">

        <label for="descripcion">Descripción</label>
        <textarea name="descripcion" id="descripcion" rows="5"><?php echo htmlspecialchars($alojamiento['descripcion']); ?></textarea>

        <div class="form-botones">
            <button type="submit"><?php echo icono("guardar"); ?> Guardar cambios</button>
        </div>
    </form>
</main>
<script src="<?php echo recurso('admin.js'); ?>"></script>
</body>
</html>
