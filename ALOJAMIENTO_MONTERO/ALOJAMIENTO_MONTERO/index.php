<?php
require_once "conexion.php";
require_once "funciones.php";

// Leemos los datos principales del alojamiento.
$alojamiento = $conexion->query("SELECT * FROM alojamiento LIMIT 1")->fetch_assoc();

// Leemos habitaciones y servicios para mostrarlos en la página.
$habitaciones = $conexion->query("SELECT * FROM habitacion ORDER BY id_habitacion ASC");
$servicios = $conexion->query("SELECT * FROM servicio ORDER BY id_servicio ASC");

// Para el formulario de reserva mostramos solamente habitaciones disponibles.
$habitaciones_disponibles = $conexion->query("SELECT * FROM habitacion WHERE estado='Disponible' ORDER BY numero ASC");

// Datos destacados para la portada: precio más bajo y cantidad de habitaciones.
$resumen = $conexion->query("SELECT MIN(precio) AS minimo, COUNT(*) AS total FROM habitacion")->fetch_assoc();

// Número de WhatsApp: solo dígitos y con el código de Bolivia (591) si hace falta.
$whatsapp = preg_replace("/\D/", "", $alojamiento['telefono'] ?? "");
if (strlen($whatsapp) === 8) {
    $whatsapp = "591" . $whatsapp;
}

// Mensaje que llega después de enviar la reserva.
$mensaje = $_GET['mensaje'] ?? "";
$mensaje_exito = str_contains($mensaje, "correctamente");
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Sistema web para promoción y reservas de alojamiento en Montero.">
    <meta name="theme-color" content="#12372a">
    <title><?php echo htmlspecialchars($alojamiento['nombre']); ?> | Montero</title>
    <link rel="icon" href="img/favicon.svg" type="image/svg+xml">
    <link rel="stylesheet" href="<?php echo recurso('styles.css'); ?>">
</head>
<body>

<!-- ==================================================
     ENCABEZADO Y MENÚ (barra superior de la app)
================================================== -->
<header id="header">
    <div class="contenedor navbar">
        <a class="logo" href="#inicio">
            <img src="img/logo.svg" alt="" width="40" height="40">
            <span><?php echo htmlspecialchars($alojamiento['nombre']); ?></span>
        </a>

        <button class="menu-btn" id="menuBtn" type="button" aria-label="Abrir menú" aria-expanded="false" aria-controls="menu">
            <?php echo icono("menu", "icono-abrir"); ?>
            <?php echo icono("cerrar", "icono-cerrar"); ?>
        </button>

        <nav aria-label="Menú principal">
            <ul class="menu" id="menu">
                <li><a href="#inicio">Inicio</a></li>
                <li><a href="#nosotros">Nosotros</a></li>
                <li><a href="#habitaciones">Habitaciones</a></li>
                <li><a href="#servicios">Servicios</a></li>
                <li><a href="#reservas">Reservas</a></li>
                <li><a href="#contacto">Contacto</a></li>
                <li><a class="menu-admin-link" href="login.php"><?php echo icono("candado"); ?> Administración</a></li>
            </ul>
        </nav>
    </div>
</header>

<main id="contenido">

<!-- ==================================================
     INICIO
================================================== -->
<section id="inicio" class="hero">
    <div class="contenedor hero-contenido">
        <span class="hero-etiqueta"><?php echo icono("estrella"); ?> Alojamiento en Montero</span>
        <h1><?php echo htmlspecialchars($alojamiento['nombre']); ?></h1>
        <p>Un espacio cómodo y agradable para disfrutar tu estadía en Montero.</p>

        <div class="hero-botones">
            <a class="boton boton-acento" href="#reservas"><?php echo icono("calendario"); ?> Reservar ahora</a>
            <a class="boton boton-claro" href="#habitaciones">Ver habitaciones</a>
        </div>

        <ul class="hero-datos">
            <li>
                <strong>Bs. <?php echo number_format($resumen['minimo'] ?? 0, 0); ?></strong>
                <span>desde / noche</span>
            </li>
            <li>
                <strong><?php echo $resumen['total']; ?></strong>
                <span>habitaciones</span>
            </li>
            <li>
                <strong>24 h</strong>
                <span>atención</span>
            </li>
        </ul>
    </div>
</section>

<!-- ==================================================
     NOSOTROS
================================================== -->
<section id="nosotros" class="seccion">
    <div class="contenedor nosotros-grid">
        <img class="imagen-redondeada" src="img/nosotros.jpg" alt="Instalaciones del alojamiento" loading="lazy" width="1200" height="675">

        <div>
            <span class="sobretitulo">Sobre nosotros</span>
            <h2>Tu comodidad es importante</h2>
            <p><?php echo nl2br(htmlspecialchars($alojamiento['descripcion'])); ?></p>

            <ul class="ventajas">
                <li><span class="ventaja-icono"><?php echo icono("ubicacion"); ?></span> <?php echo htmlspecialchars($alojamiento['direccion']); ?></li>
                <li><span class="ventaja-icono"><?php echo icono("seguridad"); ?></span> Ambiente seguro y tranquilo</li>
                <li><span class="ventaja-icono"><?php echo icono("limpieza"); ?></span> Habitaciones limpias y ordenadas</li>
                <li><span class="ventaja-icono"><?php echo icono("atencion"); ?></span> Atención personalizada</li>
            </ul>

            <a class="boton" href="#habitaciones">Ver habitaciones <?php echo icono("flecha-derecha"); ?></a>
        </div>
    </div>
</section>

<!-- ==================================================
     HABITACIONES
================================================== -->
<section id="habitaciones" class="seccion fondo-claro">
    <div class="contenedor">
        <div class="titulo">
            <span class="sobretitulo">Habitaciones</span>
            <h2>Elige tu habitación</h2>
            <p>Consulta el tipo, capacidad, precio y estado.</p>
        </div>

        <div class="habitaciones-grid">
            <?php while ($hab = $habitaciones->fetch_assoc()): ?>
                <article class="habitacion">
                    <div class="habitacion-imagen">
                        <img src="<?php echo htmlspecialchars(imagen_habitacion($hab['imagen'], $hab['tipo'])); ?>"
                             alt="Habitación <?php echo htmlspecialchars($hab['tipo']); ?>"
                             loading="lazy" width="600" height="450"
                             onerror="this.onerror=null;this.src='img/habitaciones/defecto.jpg'">
                        <span class="insignia <?php echo clase_estado($hab['estado']); ?>"><?php echo htmlspecialchars($hab['estado']); ?></span>
                    </div>

                    <div class="habitacion-contenido">
                        <h3><?php echo htmlspecialchars($hab['tipo']); ?></h3>

                        <ul class="habitacion-datos">
                            <li><?php echo icono("cama"); ?> N.º <?php echo htmlspecialchars($hab['numero']); ?></li>
                            <li><?php echo icono("persona"); ?> <?php echo $hab['capacidad']; ?> persona(s)</li>
                        </ul>

                        <div class="precio">
                            <strong>Bs. <?php echo number_format($hab['precio'], 2); ?></strong>
                            <span>/ noche</span>
                        </div>

                        <?php if ($hab['estado'] === 'Disponible'): ?>
                            <a href="#reservas"
                               class="boton boton-bloque seleccionar-habitacion"
                               data-id="<?php echo $hab['id_habitacion']; ?>">
                                <?php echo icono("calendario"); ?> Reservar
                            </a>
                        <?php else: ?>
                            <span class="boton boton-bloque boton-desactivado" aria-disabled="true">No disponible</span>
                        <?php endif; ?>
                    </div>
                </article>
            <?php endwhile; ?>
        </div>
    </div>
</section>

<!-- ==================================================
     SERVICIOS
================================================== -->
<section id="servicios" class="seccion">
    <div class="contenedor">
        <div class="titulo">
            <span class="sobretitulo">Servicios</span>
            <h2>Todo lo que necesitas</h2>
            <p>Servicios principales que ofrece el alojamiento.</p>
        </div>

        <div class="servicios-grid">
            <?php while ($serv = $servicios->fetch_assoc()): ?>
                <article class="servicio">
                    <div class="servicio-icono"><?php echo icono(icono_servicio($serv['nombre'])); ?></div>
                    <h3><?php echo htmlspecialchars($serv['nombre']); ?></h3>
                    <p><?php echo htmlspecialchars($serv['descripcion']); ?></p>
                </article>
            <?php endwhile; ?>
        </div>
    </div>
</section>

<!-- ==================================================
     RESERVA
================================================== -->
<section id="reservas" class="seccion fondo-claro">
    <div class="contenedor">
        <div class="titulo">
            <span class="sobretitulo">Reservas</span>
            <h2>Solicitud de reserva</h2>
            <p>Complete los datos. La solicitud quedará con estado Pendiente.</p>
        </div>

        <?php if ($mensaje !== ""): ?>
            <div class="alerta <?php echo $mensaje_exito ? 'exito' : 'error'; ?>" role="status">
                <?php echo icono($mensaje_exito ? "check" : "alerta"); ?>
                <p><?php echo htmlspecialchars($mensaje); ?></p>
                <button type="button" class="alerta-cerrar" aria-label="Cerrar mensaje" onclick="this.parentElement.remove()">
                    <?php echo icono("cerrar"); ?>
                </button>
            </div>
        <?php endif; ?>

        <form class="reserva-layout" action="guardar_reserva.php" method="POST" id="formReserva">
            <div class="formulario">
                <h3>Datos del huésped</h3>
                <div class="form-grid">
                    <div class="campo">
                        <label for="nombre">Nombre completo</label>
                        <input type="text" name="nombre" id="nombre" autocomplete="name" required>
                    </div>

                    <div class="campo">
                        <label for="telefono">Teléfono</label>
                        <input type="tel" name="telefono" id="telefono" autocomplete="tel" inputmode="tel" placeholder="Ej.: 70000000" required>
                    </div>

                    <div class="campo campo-completo">
                        <label for="correo">Correo</label>
                        <input type="email" name="correo" id="correo" autocomplete="email" placeholder="nombre@correo.com" required>
                    </div>
                </div>

                <h3>Detalles de la estadía</h3>
                <div class="form-grid">
                    <div class="campo campo-completo">
                        <label for="habitacion">Habitación</label>
                        <select name="id_habitacion" id="habitacion" required>
                            <option value="">Seleccione una habitación</option>
                            <?php while ($opcion = $habitaciones_disponibles->fetch_assoc()): ?>
                                <option value="<?php echo $opcion['id_habitacion']; ?>"
                                        data-precio="<?php echo $opcion['precio']; ?>"
                                        data-imagen="<?php echo htmlspecialchars(imagen_habitacion($opcion['imagen'], $opcion['tipo'])); ?>">
                                    <?php echo htmlspecialchars($opcion['numero'] . ' - ' . $opcion['tipo'] . ' (Bs. ' . number_format($opcion['precio'], 2) . ')'); ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>

                    <div class="campo">
                        <label for="entrada">Fecha de ingreso</label>
                        <input type="date" name="fecha_ingreso" id="entrada" required>
                    </div>

                    <div class="campo">
                        <label for="salida">Fecha de salida</label>
                        <input type="date" name="fecha_salida" id="salida" required>
                    </div>

                    <div class="campo">
                        <label for="personas">Cantidad de personas</label>
                        <input type="number" name="cantidad_personas" id="personas" min="1" max="10" value="1" inputmode="numeric" required>
                    </div>

                    <div class="campo campo-completo">
                        <label for="comentario">Comentario <small>(opcional)</small></label>
                        <textarea name="comentario" id="comentario" rows="3" placeholder="Escriba una observación si es necesario"></textarea>
                    </div>
                </div>
            </div>

            <aside class="resumen-tarjeta">
                <div class="resumen-imagen">
                    <img id="resumenImagen" src="img/habitaciones/defecto.jpg" alt="" width="600" height="400">
                    <span id="resumenHabitacion">Ninguna habitación seleccionada</span>
                </div>
                <div class="resumen-cuerpo">
                    <h3>Resumen</h3>
                    <div class="resumen-reserva" id="resumenReserva" aria-live="polite">
                        Seleccione una habitación y las fechas para ver un cálculo estimado.
                    </div>
                    <button class="boton boton-acento boton-bloque" type="submit">
                        <?php echo icono("check"); ?> Enviar solicitud
                    </button>
                    <p class="nota">El administrador confirmará su reserva por teléfono o correo.</p>
                </div>
            </aside>
        </form>
    </div>
</section>

<!-- ==================================================
     CONTACTO
================================================== -->
<section id="contacto" class="seccion">
    <div class="contenedor">
        <div class="titulo">
            <span class="sobretitulo">Contacto</span>
            <h2>¿Tienes preguntas?</h2>
            <p>Escríbenos o llámanos, con gusto te atendemos.</p>
        </div>

        <div class="contacto-grid">
            <div class="contacto-card">
                <div class="servicio-icono"><?php echo icono("ubicacion"); ?></div>
                <strong>Dirección</strong>
                <p><?php echo htmlspecialchars($alojamiento['direccion']); ?></p>
            </div>
            <a class="contacto-card" href="tel:<?php echo htmlspecialchars($alojamiento['telefono']); ?>">
                <div class="servicio-icono"><?php echo icono("telefono"); ?></div>
                <strong>Teléfono</strong>
                <p><?php echo htmlspecialchars($alojamiento['telefono']); ?></p>
            </a>
            <a class="contacto-card" href="mailto:<?php echo htmlspecialchars($alojamiento['correo']); ?>">
                <div class="servicio-icono"><?php echo icono("correo"); ?></div>
                <strong>Correo</strong>
                <p><?php echo htmlspecialchars($alojamiento['correo']); ?></p>
            </a>
        </div>

        <?php if ($whatsapp !== ""): ?>
            <div class="centrado">
                <a class="boton boton-whatsapp" href="https://wa.me/<?php echo $whatsapp; ?>" target="_blank" rel="noopener">
                    <?php echo icono("whatsapp"); ?> Escribir por WhatsApp
                </a>
            </div>
        <?php endif; ?>
    </div>
</section>

</main>

<!-- ==================================================
     PIE DE PÁGINA
================================================== -->
<footer>
    <div class="contenedor footer-grid">
        <div>
            <div class="logo">
                <img src="img/logo.svg" alt="" width="40" height="40">
                <span><?php echo htmlspecialchars($alojamiento['nombre']); ?></span>
            </div>
            <p>Un espacio cómodo y agradable para disfrutar tu estadía en Montero.</p>
        </div>

        <div>
            <h4>Enlaces</h4>
            <ul>
                <li><a href="#habitaciones">Habitaciones</a></li>
                <li><a href="#servicios">Servicios</a></li>
                <li><a href="#reservas">Reservas</a></li>
                <li><a href="login.php">Administración</a></li>
            </ul>
        </div>

        <div>
            <h4>Contacto</h4>
            <ul>
                <li><?php echo icono("ubicacion"); ?> <?php echo htmlspecialchars($alojamiento['direccion']); ?></li>
                <li><?php echo icono("telefono"); ?> <?php echo htmlspecialchars($alojamiento['telefono']); ?></li>
                <li><?php echo icono("correo"); ?> <?php echo htmlspecialchars($alojamiento['correo']); ?></li>
            </ul>
        </div>
    </div>

    <div class="footer-derechos">
        <p>&copy; 2026 <?php echo htmlspecialchars($alojamiento['nombre']); ?> - Proyecto Técnico Medio.</p>
    </div>
</footer>

<button id="btnArriba" class="btn-arriba" type="button" aria-label="Volver arriba"><?php echo icono("flecha-arriba"); ?></button>
<script src="<?php echo recurso('script.js'); ?>"></script>
</body>
</html>
