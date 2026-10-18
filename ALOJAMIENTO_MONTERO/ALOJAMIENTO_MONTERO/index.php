<?php
require_once "conexion.php";

// Leemos los datos principales del alojamiento.
$alojamiento = $conexion->query("SELECT * FROM alojamiento LIMIT 1")->fetch_assoc();

// Leemos habitaciones y servicios para mostrarlos en la página.
$habitaciones = $conexion->query("SELECT * FROM habitacion ORDER BY id_habitacion ASC");
$servicios = $conexion->query("SELECT * FROM servicio ORDER BY id_servicio ASC");

// Para el formulario de reserva mostramos solamente habitaciones disponibles.
$habitaciones_disponibles = $conexion->query("SELECT * FROM habitacion WHERE estado='Disponible' ORDER BY numero ASC");
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Sistema web para promoción y reservas de alojamiento en Montero.">
    <title><?php echo htmlspecialchars($alojamiento['nombre']); ?> | Montero</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>

<!-- ==================================================
     BARRA SUPERIOR
================================================== -->
<div class="barra-superior">
    <div class="contenedor barra-contenido">
        <span>📍 Montero - Santa Cruz</span>
        <span id="fechaActual">Alojamiento</span>
    </div>
</div>

<!-- ==================================================
     ENCABEZADO Y MENÚ
================================================== -->
<header id="header">
    <div class="contenedor navbar">
        <div class="logo"><?php echo htmlspecialchars($alojamiento['nombre']); ?></div>

        <button class="menu-btn" id="menuBtn" type="button">☰</button>

        <nav>
            <ul class="menu" id="menu">
                <li><a href="#inicio">Inicio</a></li>
                <li><a href="#nosotros">Nosotros</a></li>
                <li><a href="#habitaciones">Habitaciones</a></li>
                <li><a href="#servicios">Servicios</a></li>
                <li><a href="#reservas">Reservas</a></li>
                <li><a href="#contacto">Contacto</a></li>
                <li><a href="login.php">Administración</a></li>
            </ul>
        </nav>
    </div>
</header>

<main>

<!-- ==================================================
     INICIO
================================================== -->
<section id="inicio" class="hero">
    <div class="hero-contenido">
        <h1><?php echo htmlspecialchars($alojamiento['nombre']); ?></h1>
        <p>Un espacio cómodo y agradable para disfrutar tu estadía en Montero.</p>
        <a class="boton" href="#reservas">Solicitar reserva</a>
    </div>
</section>

<!-- ==================================================
     NOSOTROS
================================================== -->
<section id="nosotros" class="seccion">
    <div class="contenedor">
        <div class="titulo">
            <h2>Sobre el alojamiento</h2>
            <p>Información general del establecimiento.</p>
        </div>

        <div class="nosotros-grid">
            <img class="imagen-redondeada"
                 src="https://images.unsplash.com/photo-1564501049412-61c2a3083791?auto=format&fit=crop&w=1000&q=80"
                 alt="Interior del alojamiento">

            <div>
                <h3>Tu comodidad es importante</h3>
                <p><?php echo nl2br(htmlspecialchars($alojamiento['descripcion'])); ?></p>
                <p><strong>Dirección:</strong> <?php echo htmlspecialchars($alojamiento['direccion']); ?></p>
                <a class="boton" href="#habitaciones">Ver habitaciones</a>
            </div>
        </div>
    </div>
</section>

<!-- ==================================================
     HABITACIONES
================================================== -->
<section id="habitaciones" class="seccion fondo-claro">
    <div class="contenedor">
        <div class="titulo">
            <h2>Habitaciones</h2>
            <p>Consulta el tipo, capacidad, precio y estado.</p>
        </div>

        <div class="habitaciones-grid">
            <?php while ($hab = $habitaciones->fetch_assoc()): ?>
                <article class="habitacion">
                    <img src="<?php echo htmlspecialchars($hab['imagen']); ?>"
                         alt="Habitación <?php echo htmlspecialchars($hab['tipo']); ?>">

                    <div class="habitacion-contenido">
                        <h3><?php echo htmlspecialchars($hab['tipo']); ?></h3>
                        <p><strong>N.º:</strong> <?php echo htmlspecialchars($hab['numero']); ?></p>
                        <p><strong>Capacidad:</strong> <?php echo $hab['capacidad']; ?> persona(s)</p>
                        <p><strong>Estado:</strong> <?php echo htmlspecialchars($hab['estado']); ?></p>
                        <div class="precio">Bs. <?php echo number_format($hab['precio'], 2); ?> / noche</div>

                        <?php if ($hab['estado'] === 'Disponible'): ?>
                            <a href="#reservas"
                               class="boton seleccionar-habitacion"
                               data-id="<?php echo $hab['id_habitacion']; ?>">
                                Reservar
                            </a>
                        <?php else: ?>
                            <span class="no-disponible">No disponible</span>
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
            <h2>Servicios</h2>
            <p>Servicios principales que ofrece el alojamiento.</p>
        </div>

        <div class="servicios-grid">
            <?php while ($serv = $servicios->fetch_assoc()): ?>
                <article class="servicio">
                    <div class="servicio-icono">✓</div>
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
            <h2>Solicitud de reserva</h2>
            <p>Complete los datos. La solicitud quedará con estado Pendiente.</p>
        </div>

        <?php if (isset($_GET['mensaje'])): ?>
            <div class="mensaje-publico"><?php echo htmlspecialchars($_GET['mensaje']); ?></div>
        <?php endif; ?>

        <form class="formulario" action="guardar_reserva.php" method="POST" id="formReserva">
            <div class="form-grid">
                <div class="campo">
                    <label>Nombre completo</label>
                    <input type="text" name="nombre" required>
                </div>

                <div class="campo">
                    <label>Teléfono</label>
                    <input type="text" name="telefono" required>
                </div>

                <div class="campo">
                    <label>Correo</label>
                    <input type="email" name="correo" required>
                </div>

                <div class="campo">
                    <label>Habitación</label>
                    <select name="id_habitacion" id="habitacion" required>
                        <option value="">Seleccione</option>
                        <?php while ($opcion = $habitaciones_disponibles->fetch_assoc()): ?>
                            <option value="<?php echo $opcion['id_habitacion']; ?>"
                                    data-precio="<?php echo $opcion['precio']; ?>">
                                <?php echo htmlspecialchars($opcion['numero'] . ' - ' . $opcion['tipo']); ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div class="campo">
                    <label>Fecha de ingreso</label>
                    <input type="date" name="fecha_ingreso" id="entrada" required>
                </div>

                <div class="campo">
                    <label>Fecha de salida</label>
                    <input type="date" name="fecha_salida" id="salida" required>
                </div>

                <div class="campo">
                    <label>Cantidad de personas</label>
                    <input type="number" name="cantidad_personas" min="1" max="10" value="1" required>
                </div>

                <div class="campo campo-completo">
                    <label>Comentario</label>
                    <textarea name="comentario" rows="4" placeholder="Escriba una observación si es necesario"></textarea>
                </div>
            </div>

            <div class="resumen-reserva" id="resumenReserva">
                Seleccione una habitación y las fechas para ver un cálculo estimado.
            </div>

            <button class="boton" type="submit">Enviar solicitud</button>
        </form>
    </div>
</section>

<!-- ==================================================
     CONTACTO
================================================== -->
<section id="contacto" class="seccion">
    <div class="contenedor">
        <div class="titulo">
            <h2>Contacto</h2>
            <p>Datos configurables desde el panel administrativo.</p>
        </div>

        <div class="contacto-grid">
            <div class="contacto-card"><strong>📍 Dirección</strong><p><?php echo htmlspecialchars($alojamiento['direccion']); ?></p></div>
            <div class="contacto-card"><strong>📞 Teléfono</strong><p><?php echo htmlspecialchars($alojamiento['telefono']); ?></p></div>
            <div class="contacto-card"><strong>✉ Correo</strong><p><?php echo htmlspecialchars($alojamiento['correo']); ?></p></div>
        </div>
    </div>
</section>

</main>

<footer>
    <div class="contenedor">
        <p>&copy; 2026 <?php echo htmlspecialchars($alojamiento['nombre']); ?> - Proyecto Técnico Medio.</p>
    </div>
</footer>

<button id="btnArriba" class="btn-arriba" type="button">↑</button>
<script src="script.js"></script>
</body>
</html>
