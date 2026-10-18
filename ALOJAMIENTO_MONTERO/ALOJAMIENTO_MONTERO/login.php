<?php
session_start();
require_once "conexion.php";
require_once "funciones.php";

$mensaje = "";

/* =====================================================
   VALIDAR EL INGRESO
   La contraseña se compara de forma directa porque este
   proyecto educativo usa un registro básico sin cifrado.
   ===================================================== */
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $usuario = $conexion->real_escape_string(trim($_POST["usuario"]));
    $clave = $conexion->real_escape_string(trim($_POST["clave"]));

    $sql = "SELECT * FROM usuario
            WHERE usuario='$usuario'
            AND clave='$clave'
            AND estado='Activo'";

    $resultado = $conexion->query($sql);

    if ($resultado && $resultado->num_rows === 1) {
        $fila = $resultado->fetch_assoc();

        $_SESSION["id_usuario"] = $fila["id_usuario"];
        $_SESSION["nombre_usuario"] = $fila["nombre"];
        $_SESSION["rol"] = $fila["rol"];

        header("Location: panel.php");
        exit;
    } else {
        $mensaje = "Usuario o contraseña incorrectos.";
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#12372a">
    <title>Ingreso al panel</title>
    <link rel="icon" href="img/favicon.svg" type="image/svg+xml">
    <link rel="stylesheet" href="<?php echo recurso('admin.css'); ?>">
</head>
<body class="login-body">

<main class="login-contenedor">
    <div class="login-imagen">
        <h2>Bienvenido de nuevo</h2>
        <p>Administre habitaciones, servicios, clientes y reservas desde un solo lugar.</p>
    </div>

    <div class="login-caja">
        <div class="login-logo">
            <img src="img/logo.svg" alt="" width="48" height="48">
            <div>
                <h1>Panel Administrativo</h1>
            </div>
        </div>
        <p>Ingrese su usuario y contraseña.</p>

        <form method="POST" action="login.php">
            <?php if ($mensaje != ""): ?>
                <div class="alerta error" role="alert"><?php echo icono("alerta"); ?> <?php echo $mensaje; ?></div>
            <?php endif; ?>

            <label for="usuario">Usuario</label>
            <div class="campo-icono">
                <?php echo icono("persona"); ?>
                <input type="text" name="usuario" id="usuario" autocomplete="username" autocapitalize="none" required autofocus>
            </div>

            <label for="clave">Contraseña</label>
            <div class="campo-icono">
                <?php echo icono("candado"); ?>
                <input type="password" name="clave" id="clave" autocomplete="current-password" required>
                <button type="button" class="boton-icono ver-clave" id="verClave" aria-label="Mostrar contraseña" aria-pressed="false">
                    <?php echo icono("ojo", "icono-mostrar"); ?>
                    <?php echo icono("ojo-cerrado", "icono-ocultar"); ?>
                </button>
            </div>

            <button type="submit">Ingresar</button>
        </form>

        <div class="dato-prueba">
            <?php echo icono("alerta"); ?>
            <p>Usuario inicial: <strong>admin</strong><br>Contraseña: <strong>1234</strong></p>
        </div>

        <a class="volver" href="index.php"><?php echo icono("flecha-izquierda"); ?> Volver a la página</a>
    </div>
</main>

<script src="<?php echo recurso('admin.js'); ?>"></script>
</body>
</html>
