<?php
require_once "verificar_sesion.php";
require_once "conexion.php";
require_once "funciones.php";

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
    <link rel="icon" href="img/favicon.svg" type="image/svg+xml">
    <link rel="stylesheet" href="<?php echo recurso('admin.css'); ?>">
</head>
<body class="admin">
<?php $titulo_pagina = "Gestión de habitaciones"; include "menu_admin.php"; ?>

<main class="contenedor-admin">
    <?php if ($mensaje): ?><div class="alerta exito" role="status"><?php echo icono("check"); ?> <?php echo $mensaje; ?></div><?php endif; ?>

    <section class="dos-columnas">
        <div>
            <h2><?php echo $editar ? "Modificar habitación" : "Registrar habitación"; ?></h2>

            <form class="form-admin" method="POST">
                <input type="hidden" name="id_habitacion" value="<?php echo $editar['id_habitacion'] ?? 0; ?>">

                <label for="numero">Número o código</label>
                <input type="text" name="numero" id="numero" value="<?php echo htmlspecialchars($editar['numero'] ?? ''); ?>" required>

                <label for="tipo">Tipo</label>
                <input type="text" name="tipo" id="tipo" list="tipos" value="<?php echo htmlspecialchars($editar['tipo'] ?? ''); ?>" placeholder="Ej.: Simple" required>
                <datalist id="tipos">
                    <option value="Simple"><option value="Doble"><option value="Matrimonial"><option value="Familiar"><option value="Suite">
                </datalist>

                <div class="fila-campos">
                    <div>
                        <label for="capacidad">Capacidad</label>
                        <input type="number" name="capacidad" id="capacidad" min="1" inputmode="numeric" value="<?php echo $editar['capacidad'] ?? 1; ?>" required>
                    </div>
                    <div>
                        <label for="precio">Precio / noche (Bs.)</label>
                        <input type="number" name="precio" id="precio" min="0" step="0.01" inputmode="decimal" value="<?php echo $editar['precio'] ?? ''; ?>" required>
                    </div>
                </div>

                <label for="estado">Estado</label>
                <select name="estado" id="estado">
                    <?php $estado_actual = $editar['estado'] ?? 'Disponible'; ?>
                    <option <?php if ($estado_actual === 'Disponible') echo 'selected'; ?>>Disponible</option>
                    <option <?php if ($estado_actual === 'Ocupada') echo 'selected'; ?>>Ocupada</option>
                    <option <?php if ($estado_actual === 'Mantenimiento') echo 'selected'; ?>>Mantenimiento</option>
                </select>

                <label for="imagen">URL de imagen <small>(opcional)</small></label>
                <input type="text" name="imagen" id="imagen" inputmode="url" value="<?php echo htmlspecialchars($editar['imagen'] ?? ''); ?>" placeholder="https://...">
                <p class="ayuda">Si la deja vacía se usa una imagen según el tipo: Simple, Doble, Matrimonial, Familiar o Suite.</p>

                <?php $imagen_defecto = imagen_habitacion("", $editar['tipo'] ?? ""); ?>
                <div class="vista-previa">
                    <img id="vistaPrevia"
                         src="<?php echo htmlspecialchars(imagen_habitacion($editar['imagen'] ?? "", $editar['tipo'] ?? "")); ?>"
                         data-defecto="<?php echo $imagen_defecto; ?>" alt="Vista previa de la imagen">
                    <span id="textoPrevia"><?php echo !empty($editar['imagen']) ? "Imagen de la URL" : "Imagen por defecto"; ?></span>
                </div>

                <div class="form-botones">
                    <button type="submit"><?php echo icono("guardar"); ?> Guardar</button>
                    <?php if ($editar): ?><a class="boton-secundario" href="habitaciones_admin.php">Cancelar</a><?php endif; ?>
                </div>
            </form>
        </div>

        <div>
            <div class="titulo-seccion"><h2>Habitaciones registradas</h2><span class="contador"><?php echo $habitaciones->num_rows; ?></span></div>
            <div class="tabla-responsive">
                <table>
                    <thead>
                        <tr><th>Habitación</th><th>Precio</th><th>Estado</th><th>Acciones</th></tr>
                    </thead>
                    <tbody>
                    <?php while ($fila = $habitaciones->fetch_assoc()): ?>
                    <tr>
                        <td data-label="Habitación">
                            <div class="celda-principal">
                                <img class="miniatura" src="<?php echo htmlspecialchars(imagen_habitacion($fila['imagen'], $fila['tipo'])); ?>" alt="" loading="lazy"
                                     onerror="this.onerror=null;this.src='img/habitaciones/defecto.jpg'">
                                <div>
                                    <strong>N.º <?php echo htmlspecialchars($fila['numero']); ?></strong><br>
                                    <small><?php echo htmlspecialchars($fila['tipo']); ?> · <?php echo $fila['capacidad']; ?> persona(s)</small>
                                </div>
                            </div>
                        </td>
                        <td data-label="Precio" class="sin-salto">Bs. <?php echo number_format($fila['precio'], 2); ?></td>
                        <td data-label="Estado"><span class="insignia <?php echo clase_estado($fila['estado']); ?>"><?php echo htmlspecialchars($fila['estado']); ?></span></td>
                        <td data-label="Acciones" class="celda-acciones">
                            <div class="acciones">
                                <a class="boton-accion" href="?editar=<?php echo $fila['id_habitacion']; ?>" title="Editar"><?php echo icono("editar"); ?> <span class="texto-boton">Editar</span></a>
                                <a class="boton-accion peligro" href="?eliminar=<?php echo $fila['id_habitacion']; ?>" title="Eliminar" onclick="return confirm('¿Eliminar esta habitación?')"><?php echo icono("eliminar"); ?> <span class="texto-boton">Eliminar</span></a>
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
