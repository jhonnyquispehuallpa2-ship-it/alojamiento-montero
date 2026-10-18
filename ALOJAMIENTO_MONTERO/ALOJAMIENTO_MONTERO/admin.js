/* =====================================================
   JAVASCRIPT DEL PANEL ADMINISTRATIVO Y DEL LOGIN
   ===================================================== */

// 1. ABRIR Y CERRAR EL MENÚ LATERAL EN CELULAR
const adminMenuBtn = document.getElementById("adminMenuBtn");
const menuAdmin = document.getElementById("menuAdmin");
const fondoMenu = document.getElementById("fondoMenu");

function abrirMenu(abrir) {
    menuAdmin.classList.toggle("abierto", abrir);
    fondoMenu.classList.toggle("visible", abrir);
    adminMenuBtn.setAttribute("aria-expanded", abrir ? "true" : "false");
}

if (adminMenuBtn) {
    adminMenuBtn.addEventListener("click", function () { abrirMenu(true); });
    fondoMenu.addEventListener("click", function () { abrirMenu(false); });

    document.addEventListener("keydown", function (evento) {
        if (evento.key === "Escape") {
            abrirMenu(false);
        }
    });
}

// 2. MOSTRAR U OCULTAR LA CONTRASEÑA EN EL LOGIN
const verClave = document.getElementById("verClave");

if (verClave) {
    verClave.addEventListener("click", function () {
        const campo = document.getElementById("clave");
        const mostrar = campo.type === "password";
        campo.type = mostrar ? "text" : "password";
        verClave.setAttribute("aria-pressed", mostrar ? "true" : "false");
        verClave.setAttribute("aria-label", mostrar ? "Ocultar contraseña" : "Mostrar contraseña");
    });
}

// 3. VISTA PREVIA DE LA IMAGEN AL REGISTRAR UNA HABITACIÓN
const campoImagen = document.getElementById("imagen");
const vistaPrevia = document.getElementById("vistaPrevia");

if (campoImagen && vistaPrevia) {
    const textoPrevia = document.getElementById("textoPrevia");

    function actualizarVistaPrevia() {
        const url = campoImagen.value.trim();
        vistaPrevia.src = url !== "" ? url : vistaPrevia.dataset.defecto;
        textoPrevia.textContent = url !== "" ? "Imagen de la URL" : "Imagen por defecto";
    }

    campoImagen.addEventListener("input", actualizarVistaPrevia);

    // Si la URL no carga, mostramos la imagen por defecto.
    vistaPrevia.addEventListener("error", function () {
        if (vistaPrevia.getAttribute("src") === vistaPrevia.dataset.defecto) {
            return;
        }
        vistaPrevia.src = vistaPrevia.dataset.defecto;
        textoPrevia.textContent = "La URL no carga: se usará la imagen por defecto";
    });
}
