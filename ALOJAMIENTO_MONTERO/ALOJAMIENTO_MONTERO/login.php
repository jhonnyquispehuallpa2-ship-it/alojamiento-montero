<?php
session_start();
require_once "conexion.php";

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
    <title>Ingreso al panel</title>
    <link rel="stylesheet" href="admin.css">
</head>
<body class="login-body">

<div class="login-caja">
    <h1>Panel Administrativo</h1>
    <p>Ingrese su usuario y contraseña.</p>

    <?php if ($mensaje != ""): ?>
        <div class="alerta error"><?php echo $mensaje; ?></div>
    <?php endif; ?>

    <form method="POST" action="login.php">
        <label>Usuario</label>
        <input type="text" name="usuario" required>

        <label>Contraseña</label>
        <input type="password" name="clave" required>

        <button type="submit">Ingresar</button>
    </form>

    <p class="dato-prueba">
        Usuario inicial: <strong>admin</strong><br>
        Contraseña: <strong>1234</strong>
    </p>

    <a class="volver" href="index.php">← Volver a la página</a>
</div>

</body>
</html>
