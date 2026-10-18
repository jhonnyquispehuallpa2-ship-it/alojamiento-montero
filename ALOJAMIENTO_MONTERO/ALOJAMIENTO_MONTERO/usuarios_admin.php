<?php
require_once "verificar_sesion.php";
require_once "conexion.php";

$editar = null;
$mensaje = "";

if (isset($_GET["eliminar"])) {
    $id = (int) $_GET["eliminar"];

    // No permitimos eliminar el usuario con el que estamos trabajando.
    if ($id != $_SESSION["id_usuario"]) {
        $conexion->query("DELETE FROM usuario WHERE id_usuario=$id");
    }

    header("Location: usuarios_admin.php");
    exit;
}

if (isset($_GET["editar"])) {
    $id = (int) $_GET["editar"];
    $editar = $conexion->query("SELECT * FROM usuario WHERE id_usuario=$id")->fetch_assoc();
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $id = (int) $_POST["id_usuario"];
    $nombre = $conexion->real_escape_string(trim($_POST["nombre"]));
    $usuario = $conexion->real_escape_string(trim($_POST["usuario"]));
    $clave = $conexion->real_escape_string(trim($_POST["clave"]));
    $rol = $conexion->real_escape_string($_POST["rol"]);
    $estado = $conexion->real_escape_string($_POST["estado"]);

    if ($id === 0) {
        /* IMPORTANTE:
           La clave se guarda sin cifrado porque así fue definido
           para este proyecto educativo de nivel Técnico Medio. */
        $sql = "INSERT INTO usuario (nombre, usuario, clave, rol, estado)
                VALUES ('$nombre', '$usuario', '$clave', '$rol', '$estado')";
    } else {
        $sql = "UPDATE usuario SET nombre='$nombre', usuario='$usuario',
                clave='$clave', rol='$rol', estado='$estado'
                WHERE id_usuario=$id";
    }

    if (!$conexion->query($sql)) {
        $mensaje = "No se pudo guardar. Verifique que el nombre de usuario no esté repetido.";
    } else {
        header("Location: usuarios_admin.php");
        exit;
    }
}

$usuarios = $conexion->query("SELECT * FROM usuario ORDER BY id_usuario DESC");
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Usuarios</title><link rel="stylesheet" href="admin.css">
</head>
<body>
<header class="cabecera-admin"><h1>Gestión de usuarios</h1></header>
<?php include "menu_admin.php"; ?>
<main class="contenedor-admin">
    <?php if ($mensaje): ?><div class="alerta error"><?php echo $mensaje; ?></div><?php endif; ?>

    <section class="dos-columnas">
        <div>
            <h2><?php echo $editar ? "Modificar usuario" : "Registrar usuario"; ?></h2>
            <form class="form-admin" method="POST">
                <input type="hidden" name="id_usuario" value="<?php echo $editar['id_usuario'] ?? 0; ?>">

                <label>Nombre</label>
                <input type="text" name="nombre" value="<?php echo htmlspecialchars($editar['nombre'] ?? ''); ?>" required>

                <label>Usuario</label>
                <input type="text" name="usuario" value="<?php echo htmlspecialchars($editar['usuario'] ?? ''); ?>" required>

                <label>Contraseña</label>
                <input type="text" name="clave" value="<?php echo htmlspecialchars($editar['clave'] ?? ''); ?>" required>

                <label>Rol</label>
                <?php $rol_actual = $editar['rol'] ?? 'Administrador'; ?>
                <select name="rol">
                    <option <?php if ($rol_actual === 'Administrador') echo 'selected'; ?>>Administrador</option>
                    <option <?php if ($rol_actual === 'Recepción') echo 'selected'; ?>>Recepción</option>
                </select>

                <label>Estado</label>
                <?php $estado_actual = $editar['estado'] ?? 'Activo'; ?>
                <select name="estado">
                    <option <?php if ($estado_actual === 'Activo') echo 'selected'; ?>>Activo</option>
                    <option <?php if ($estado_actual === 'Inactivo') echo 'selected'; ?>>Inactivo</option>
                </select>

                <button type="submit">Guardar</button>
                <?php if ($editar): ?><a class="boton-secundario" href="usuarios_admin.php">Cancelar</a><?php endif; ?>
            </form>
        </div>

        <div>
            <h2>Usuarios registrados</h2>
            <div class="tabla-responsive">
                <table>
                    <tr><th>Nombre</th><th>Usuario</th><th>Rol</th><th>Estado</th><th>Acciones</th></tr>
                    <?php while ($fila = $usuarios->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($fila['nombre']); ?></td>
                        <td><?php echo htmlspecialchars($fila['usuario']); ?></td>
                        <td><?php echo htmlspecialchars($fila['rol']); ?></td>
                        <td><?php echo htmlspecialchars($fila['estado']); ?></td>
                        <td>
                            <a href="?editar=<?php echo $fila['id_usuario']; ?>">Editar</a>
                            <?php if ($fila['id_usuario'] != $_SESSION['id_usuario']): ?>
                                | <a href="?eliminar=<?php echo $fila['id_usuario']; ?>" onclick="return confirm('¿Eliminar este usuario?')">Eliminar</a>
                            <?php endif; ?>
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
