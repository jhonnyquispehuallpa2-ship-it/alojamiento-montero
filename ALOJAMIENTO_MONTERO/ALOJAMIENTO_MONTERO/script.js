/* =====================================================
   JAVASCRIPT BÁSICO DE LA PÁGINA
   Se mantiene corto para que sea fácil de explicar.
   ===================================================== */

// 1. MENÚ PARA CELULAR
const menuBtn = document.getElementById("menuBtn");
const menu = document.getElementById("menu");

function cerrarMenu() {
    menu.classList.remove("activo");
    menuBtn.setAttribute("aria-expanded", "false");
}

menuBtn.addEventListener("click", function () {
    const abierto = menu.classList.toggle("activo");
    menuBtn.setAttribute("aria-expanded", abierto ? "true" : "false");
});

// Al pulsar una opción del menú o la tecla Esc, lo cerramos.
document.querySelectorAll(".menu a").forEach(function (enlace) {
    enlace.addEventListener("click", cerrarMenu);
});

document.addEventListener("keydown", function (evento) {
    if (evento.key === "Escape") {
        cerrarMenu();
    }
});

// 2. SELECCIONAR HABITACIÓN DESDE UNA TARJETA
const selectHabitacion = document.getElementById("habitacion");

document.querySelectorAll(".seleccionar-habitacion").forEach(function (boton) {
    boton.addEventListener("click", function () {
        selectHabitacion.value = boton.dataset.id;
        calcularTotal();
    });
});

// 3. CÁLCULO SENCILLO DE NOCHES Y PRECIO
const entrada = document.getElementById("entrada");
const salida = document.getElementById("salida");
const resumen = document.getElementById("resumenReserva");
const resumenImagen = document.getElementById("resumenImagen");
const resumenHabitacion = document.getElementById("resumenHabitacion");

function calcularTotal() {
    const opcion = selectHabitacion.options[selectHabitacion.selectedIndex];

    // Mostramos la foto de la habitación elegida.
    if (opcion && opcion.dataset.imagen) {
        resumenImagen.src = opcion.dataset.imagen;
        resumenHabitacion.textContent = opcion.textContent.trim();
    }

    if (!entrada.value || !salida.value || !selectHabitacion.value) {
        return;
    }

    const fechaEntrada = new Date(entrada.value);
    const fechaSalida = new Date(salida.value);
    const diferencia = fechaSalida - fechaEntrada;
    const noches = diferencia / (1000 * 60 * 60 * 24);

    if (noches <= 0) {
        resumen.classList.add("error");
        resumen.textContent = "La fecha de salida debe ser posterior a la fecha de ingreso.";
        return;
    }

    const precio = Number(opcion.dataset.precio || 0);
    const total = noches * precio;

    resumen.classList.remove("error");
    resumen.innerHTML =
        "<div class='fila'><span>Precio por noche</span><span>Bs. " + precio.toFixed(2) + "</span></div>" +
        "<div class='fila'><span>Noches</span><span>" + noches + "</span></div>" +
        "<div class='fila total'><span>Total estimado</span><span>Bs. " + total.toFixed(2) + "</span></div>";
}

entrada.addEventListener("change", calcularTotal);
salida.addEventListener("change", calcularTotal);
selectHabitacion.addEventListener("change", calcularTotal);

// No permitir fechas anteriores a hoy.
const hoy = new Date().toISOString().split("T")[0];
entrada.min = hoy;
salida.min = hoy;

entrada.addEventListener("change", function () {
    salida.min = entrada.value;
});

// 4. BOTÓN PARA VOLVER ARRIBA Y ENCABEZADO CON SOMBRA
const btnArriba = document.getElementById("btnArriba");
const header = document.getElementById("header");

window.addEventListener("scroll", function () {
    btnArriba.classList.toggle("mostrar", window.scrollY > 350);
    header.classList.toggle("scrolled", window.scrollY > 10);
});

btnArriba.addEventListener("click", function () {
    window.scrollTo({ top: 0, behavior: "smooth" });
});

// 5. RESALTAR EN EL MENÚ LA SECCIÓN QUE SE ESTÁ VIENDO
const observador = new IntersectionObserver(function (entradas) {
    entradas.forEach(function (item) {
        if (item.isIntersecting) {
            document.querySelectorAll(".menu a").forEach(function (enlace) {
                enlace.classList.toggle("activo", enlace.getAttribute("href") === "#" + item.target.id);
            });
        }
    });
}, { rootMargin: "-45% 0px -50% 0px" });

document.querySelectorAll("main section[id]").forEach(function (seccion) {
    observador.observe(seccion);
});
