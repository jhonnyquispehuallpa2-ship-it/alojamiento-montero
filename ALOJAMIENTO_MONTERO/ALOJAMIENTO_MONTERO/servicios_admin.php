<?php
require_once "verificar_sesion.php";
require_once "conexion.php";
require_once "funciones.php";

$editar = null;

if (isset($_GET["eliminar"])) {
    $id = (int) $_GET["eliminar"];
    $conexion->query("DELETE FROM servicio WHERE id_servicio=$id");
    header("Location: servicios_admin.php");
    exit;
}

if (isset($_GET["editar"])) {
    $id = (int) $_GET["editar"];
    $editar = $conexion->query("SELECT * FROM servicio WHERE id_servicio=$id")->fetch_assoc();
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $id = (int) $_POST["id_servicio"];
    $nombre = $conexion->real_escape_string(trim($_POST["nombre"]));
    $descripcion = $conexion->real_escape_string(trim($_POST["descripcion"]));

    if ($id === 0) {
        $conexion->query("INSERT INTO servicio (id_alojamiento, nombre, descripcion) VALUES (1, '$nombre', '$descripcion')");
    } else {
        $conexion->query("UPDATE servicio SET nombre='$nombre', descripcion='$descripcion' WHERE id_servicio=$id");
    }

    header("Location: servicios_admin.php");
    exit;
}

$servicios = $conexion->query("SELECT * FROM servicio ORDER BY id_servicio DESC");
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Servicios</title>
    <link rel="icon" href="img/favicon.svg" type="image/svg+xml">
    <link rel="stylesheet" href="<?php echo recurso('admin.css'); ?>">
</head>
<body class="admin">
<?php $titulo_pagina = "Gestión de servicios"; include "menu_admin.php"; ?>

<main class="contenedor-admin">
    <section class="dos-columnas">
        <div>
            <h2><?php echo $editar ? "Modificar servicio" : "Registrar servicio"; ?></h2>
            <form class="form-admin" method="POST">
                <input type="hidden" name="id_servicio" value="<?php echo $editar['id_servicio'] ?? 0; ?>">

                <label for="nombre">Nombre</label>
                <input type="text" name="nombre" id="nombre" value="<?php echo htmlspecialchars($editar['nombre'] ?? ''); ?>" placeholder="Ej.: WiFi, Desayuno, Estacionamiento" required>
                <p class="ayuda">El icono se elige automáticamente según el nombre.</p>

                <label for="descripcion">Descripción</label>
                <textarea name="descripcion" id="descripcion" rows="4"><?php echo htmlspecialchars($editar['descripcion'] ?? ''); ?></textarea>

                <div class="form-botones">
                    <button type="submit"><?php echo icono("guardar"); ?> Guardar</button>
                    <?php if ($editar): ?><a class="boton-secundario" href="servicios_admin.php">Cancelar</a><?php endif; ?>
                </div>
            </form>
        </div>

        <div>
            <div class="titulo-seccion"><h2>Servicios registrados</h2><span class="contador"><?php echo $servicios->num_rows; ?></span></div>
            <div class="tabla-responsive">
                <table>
                    <thead>
                        <tr><th>Servicio</th><th>Descripción</th><th>Acciones</th></tr>
                    </thead>
                    <tbody>
                    <?php while ($fila = $servicios->fetch_assoc()): ?>
                    <tr>
                        <td data-label="Servicio">
                            <div class="celda-principal">
                                <span class="tarjeta-icono"><?php echo icono(icono_servicio($fila['nombre'])); ?></span>
                                <strong><?php echo htmlspecialchars($fila['nombre']); ?></strong>
                            </div>
                        </td>
                        <td data-label="Descripción"><?php echo htmlspecialchars($fila['descripcion']); ?></td>
                        <td data-label="Acciones" class="celda-acciones">
                            <div class="acciones">
                                <a class="boton-accion" href="?editar=<?php echo $fila['id_servicio']; ?>" title="Editar"><?php echo icono("editar"); ?> <span class="texto-boton">Editar</span></a>
                                <a class="boton-accion peligro" href="?eliminar=<?php echo $fila['id_servicio']; ?>" title="Eliminar" onclick="return confirm('¿Eliminar este servicio?')"><?php echo icono("eliminar"); ?> <span class="texto-boton">Eliminar</span></a>
                            </div>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </section>
</main>
<script src="<?php echo recurso('admin.js'); ?>"></script>
</body>
</html>
