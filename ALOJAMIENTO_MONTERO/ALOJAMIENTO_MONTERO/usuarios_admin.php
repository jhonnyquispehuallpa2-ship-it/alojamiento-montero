<?php
require_once "verificar_sesion.php";
require_once "conexion.php";
require_once "funciones.php";

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
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Usuarios</title>
    <link rel="icon" href="img/favicon.svg" type="image/svg+xml">
    <link rel="stylesheet" href="<?php echo recurso('admin.css'); ?>">
</head>
<body class="admin">
<?php $titulo_pagina = "Gestión de usuarios"; include "menu_admin.php"; ?>

<main class="contenedor-admin">
    <?php if ($mensaje): ?><div class="alerta error" role="alert"><?php echo icono("alerta"); ?> <?php echo $mensaje; ?></div><?php endif; ?>

    <section class="dos-columnas">
        <div>
            <h2><?php echo $editar ? "Modificar usuario" : "Registrar usuario"; ?></h2>
            <form class="form-admin" method="POST">
                <input type="hidden" name="id_usuario" value="<?php echo $editar['id_usuario'] ?? 0; ?>">

                <label for="nombre">Nombre</label>
                <input type="text" name="nombre" id="nombre" value="<?php echo htmlspecialchars($editar['nombre'] ?? ''); ?>" required>

                <label for="usuario">Usuario</label>
                <input type="text" name="usuario" id="usuario" autocapitalize="none" value="<?php echo htmlspecialchars($editar['usuario'] ?? ''); ?>" required>

                <label for="clave">Contraseña</label>
                <input type="text" name="clave" id="clave" value="<?php echo htmlspecialchars($editar['clave'] ?? ''); ?>" required>

                <label for="rol">Rol</label>
                <?php $rol_actual = $editar['rol'] ?? 'Administrador'; ?>
                <select name="rol" id="rol">
                    <option <?php if ($rol_actual === 'Administrador') echo 'selected'; ?>>Administrador</option>
                    <option <?php if ($rol_actual === 'Recepción') echo 'selected'; ?>>Recepción</option>
                </select>

                <label for="estado">Estado</label>
                <?php $estado_actual = $editar['estado'] ?? 'Activo'; ?>
                <select name="estado" id="estado">
                    <option <?php if ($estado_actual === 'Activo') echo 'selected'; ?>>Activo</option>
                    <option <?php if ($estado_actual === 'Inactivo') echo 'selected'; ?>>Inactivo</option>
                </select>

                <div class="form-botones">
                    <button type="submit"><?php echo icono("guardar"); ?> Guardar</button>
                    <?php if ($editar): ?><a class="boton-secundario" href="usuarios_admin.php">Cancelar</a><?php endif; ?>
                </div>
            </form>
        </div>

        <div>
            <div class="titulo-seccion"><h2>Usuarios registrados</h2><span class="contador"><?php echo $usuarios->num_rows; ?></span></div>
            <div class="tabla-responsive">
                <table>
                    <thead>
                        <tr><th>Nombre</th><th>Usuario</th><th>Rol</th><th>Estado</th><th>Acciones</th></tr>
                    </thead>
                    <tbody>
                    <?php while ($fila = $usuarios->fetch_assoc()): ?>
                    <tr>
                        <td data-label="Nombre"><strong><?php echo htmlspecialchars($fila['nombre']); ?></strong></td>
                        <td data-label="Usuario"><?php echo htmlspecialchars($fila['usuario']); ?></td>
                        <td data-label="Rol"><?php echo htmlspecialchars($fila['rol']); ?></td>
                        <td data-label="Estado"><span class="insignia <?php echo clase_estado($fila['estado']); ?>"><?php echo htmlspecialchars($fila['estado']); ?></span></td>
                        <td data-label="Acciones" class="celda-acciones">
                            <div class="acciones">
                                <a class="boton-accion" href="?editar=<?php echo $fila['id_usuario']; ?>" title="Editar"><?php echo icono("editar"); ?> <span class="texto-boton">Editar</span></a>
                                <?php if ($fila['id_usuario'] != $_SESSION['id_usuario']): ?>
                                    <a class="boton-accion peligro" href="?eliminar=<?php echo $fila['id_usuario']; ?>" title="Eliminar" onclick="return confirm('¿Eliminar este usuario?')"><?php echo icono("eliminar"); ?> <span class="texto-boton">Eliminar</span></a>
                                <?php endif; ?>
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
