<?php
require_once "verificar_sesion.php";
require_once "conexion.php";

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
    <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Servicios</title><link rel="stylesheet" href="admin.css">
</head>
<body>
<header class="cabecera-admin"><h1>Gestión de servicios</h1></header>
<?php include "menu_admin.php"; ?>
<main class="contenedor-admin">
    <section class="dos-columnas">
        <div>
            <h2><?php echo $editar ? "Modificar servicio" : "Registrar servicio"; ?></h2>
            <form class="form-admin" method="POST">
                <input type="hidden" name="id_servicio" value="<?php echo $editar['id_servicio'] ?? 0; ?>">
                <label>Nombre</label>
                <input type="text" name="nombre" value="<?php echo htmlspecialchars($editar['nombre'] ?? ''); ?>" required>
                <label>Descripción</label>
                <textarea name="descripcion" rows="4"><?php echo htmlspecialchars($editar['descripcion'] ?? ''); ?></textarea>
                <button type="submit">Guardar</button>
                <?php if ($editar): ?><a class="boton-secundario" href="servicios_admin.php">Cancelar</a><?php endif; ?>
            </form>
        </div>

        <div>
            <h2>Servicios registrados</h2>
            <div class="tabla-responsive">
                <table>
                    <tr><th>Servicio</th><th>Descripción</th><th>Acciones</th></tr>
                    <?php while ($fila = $servicios->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($fila['nombre']); ?></td>
                        <td><?php echo htmlspecialchars($fila['descripcion']); ?></td>
                        <td><a href="?editar=<?php echo $fila['id_servicio']; ?>">Editar</a> | <a href="?eliminar=<?php echo $fila['id_servicio']; ?>" onclick="return confirm('¿Eliminar este servicio?')">Eliminar</a></td>
                    </tr>
                    <?php endwhile; ?>
                </table>
            </div>
        </div>
    </section>
</main>
</body>
</html>
