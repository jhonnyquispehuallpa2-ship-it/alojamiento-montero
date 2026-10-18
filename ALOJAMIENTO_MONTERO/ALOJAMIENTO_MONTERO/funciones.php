<?php
/* =====================================================
   FUNCIONES AUXILIARES DE PRESENTACIÓN
   Ayudan a mostrar imágenes, iconos y estados con estilo.
   ===================================================== */

// Pasa un texto a minúsculas y sin tildes para comparar fácilmente.
function normalizar($texto) {
    $texto = mb_strtolower(trim($texto), "UTF-8");
    return strtr($texto, ["á" => "a", "é" => "e", "í" => "i", "ó" => "o", "ú" => "u", "ñ" => "n", "ü" => "u"]);
}

// Devuelve la imagen de la habitación. Si no tiene URL, usa una imagen local según su tipo.
function imagen_habitacion($imagen, $tipo) {
    if (trim($imagen ?? "") !== "") {
        return $imagen;
    }

    $tipo = normalizar($tipo);
    $imagenes = [
        "simple"      => "simple.jpg",
        "individual"  => "simple.jpg",
        "doble"       => "doble.jpg",
        "matrimonial" => "matrimonial.jpg",
        "familiar"    => "familiar.jpg",
        "suite"       => "suite.jpg",
    ];

    foreach ($imagenes as $palabra => $archivo) {
        if (str_contains($tipo, $palabra)) {
            return "img/habitaciones/" . $archivo;
        }
    }

    return "img/habitaciones/defecto.jpg";
}

// Devuelve el nombre del icono según las palabras del nombre del servicio.
function icono_servicio($nombre) {
    $nombre = normalizar($nombre);
    $iconos = [
        "wifi"            => ["wifi", "internet"],
        "estacionamiento" => ["estacionamiento", "parqueo", "garaje", "parking"],
        "bano"            => ["bano", "ducha"],
        "desayuno"        => ["desayuno", "comida", "restaurante", "cafe"],
        "aire"            => ["aire", "climatiz", "ventilador"],
        "tv"              => ["tv", "televis", "cable"],
        "lavanderia"      => ["lavander", "lavado"],
        "piscina"         => ["piscina"],
        "seguridad"       => ["seguridad", "camara", "vigilancia"],
        "atencion"        => ["atencion", "recepcion", "24"],
        "limpieza"        => ["limpieza"],
    ];

    foreach ($iconos as $icono => $palabras) {
        foreach ($palabras as $palabra) {
            if (str_contains($nombre, $palabra)) {
                return $icono;
            }
        }
    }

    return "servicio";
}

// Escribe un icono SVG de la carpeta img/iconos directamente en el HTML.
// El icono usa "currentColor", por eso toma el color del texto que lo rodea.
function icono($nombre, $clase = "") {
    $archivo = __DIR__ . "/img/iconos/" . $nombre . ".svg";
    if (!is_file($archivo)) {
        $archivo = __DIR__ . "/img/iconos/servicio.svg";
    }

    $svg = trim(file_get_contents($archivo));
    return str_replace("<svg ", '<svg class="icono ' . $clase . '" aria-hidden="true" focusable="false" ', $svg);
}

// Agrega la fecha de modificación al archivo (styles.css?v=123456).
// Así el navegador descarga la versión nueva cuando el archivo cambia.
function recurso($archivo) {
    return $archivo . "?v=" . filemtime(__DIR__ . "/" . $archivo);
}

// Devuelve la clase CSS de la insignia según el estado (habitación, reserva o usuario).
function clase_estado($estado) {
    $clases = [
        "disponible"    => "exito",
        "confirmada"    => "exito",
        "activo"        => "exito",
        "pendiente"     => "aviso",
        "ocupada"       => "aviso",
        "cancelada"     => "peligro",
        "inactivo"      => "peligro",
        "mantenimiento" => "neutro",
        "finalizada"    => "neutro",
    ];

    return $clases[normalizar($estado)] ?? "neutro";
}
?>
