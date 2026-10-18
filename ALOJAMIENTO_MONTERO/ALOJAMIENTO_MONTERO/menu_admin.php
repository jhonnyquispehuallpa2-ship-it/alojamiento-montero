<?php
/* =====================================================
   MENÚ REUTILIZABLE DEL PANEL ADMINISTRATIVO
   Muestra la barra superior y el menú lateral.
   Antes de incluirlo, cada página define $titulo_pagina.
   ===================================================== */
require_once "funciones.php";

// Página actual, para marcar su enlace como activo.
$pagina_actual = basename($_SERVER["PHP_SELF"]);

$enlaces = [
    "panel.php"              => ["Inicio", "inicio"],
    "alojamiento_admin.php"  => ["Alojamiento", "hotel"],
    "habitaciones_admin.php" => ["Habitaciones", "cama"],
    "servicios_admin.php"    => ["Servicios", "estrella"],
    "clientes_admin.php"     => ["Clientes", "grupo"],
    "reservas_admin.php"     => ["Reservas", "calendario"],
    "usuarios_admin.php"     => ["Usuarios", "usuario-llave"],
];
?>
<header class="barra-admin">
    <button class="boton-icono" id="adminMenuBtn" type="button" aria-label="Abrir menú" aria-controls="menuAdmin" aria-expanded="false">
        <?php echo icono("menu"); ?>
    </button>

    <div class="barra-titulo">
        <small>Panel administrativo</small>
        <h1><?php echo htmlspecialchars($titulo_pagina ?? "Panel Administrativo"); ?></h1>
    </div>

    <div class="barra-acciones">
        <span class="usuario-chip">
            <span class="avatar"><?php echo icono("persona"); ?></span>
            <span>
                <?php echo htmlspecialchars($_SESSION["nombre_usuario"]); ?>
                <small><?php echo htmlspecialchars($_SESSION["rol"] ?? ""); ?></small>
            </span>
        </span>
        <a class="boton-icono" href="index.php" target="_blank" title="Ver página" aria-label="Ver página pública"><?php echo icono("externo"); ?></a>
        <a class="boton-icono peligro" href="logout.php" title="Salir" aria-label="Cerrar sesión"><?php echo icono("salir"); ?></a>
    </div>
</header>

<nav class="menu-admin" id="menuAdmin" aria-label="Menú del panel">
    <a class="menu-admin-logo" href="panel.php" title="Inicio del panel">
        <img src="img/logo.svg" alt="" width="44" height="44">
        <span><strong>Panel</strong><small>Administración</small></span>
    </a>

    <?php foreach ($enlaces as $archivo => $datos): ?>
        <a class="enlace-menu <?php if ($pagina_actual === $archivo) echo 'activo'; ?>"
           href="<?php echo $archivo; ?>"
           <?php if ($pagina_actual === $archivo) echo 'aria-current="page"'; ?>>
            <span class="indicador"><?php echo icono($datos[1]); ?></span>
            <span><?php echo $datos[0]; ?></span>
        </a>
    <?php endforeach; ?>

    <div class="menu-admin-pie">
        <a class="enlace-menu" href="index.php" target="_blank">
            <span class="indicador"><?php echo icono("externo"); ?></span><span>Ver página</span>
        </a>
        <a class="enlace-menu salir" href="logout.php">
            <span class="indicador"><?php echo icono("salir"); ?></span><span>Salir</span>
        </a>
    </div>
</nav>
<div class="fondo-menu" id="fondoMenu"></div>
