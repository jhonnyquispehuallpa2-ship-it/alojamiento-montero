/* =====================================================
   JAVASCRIPT BÁSICO DE LA PÁGINA
   Se mantiene corto para que sea fácil de explicar.
   ===================================================== */

// 1. MENÚ PARA CELULAR
const menuBtn = document.getElementById("menuBtn");
const menu = document.getElementById("menu");

menuBtn.addEventListener("click", function () {
    menu.classList.toggle("activo");
});

// Al pulsar una opción del menú, lo cerramos.
document.querySelectorAll(".menu a").forEach(function (enlace) {
    enlace.addEventListener("click", function () {
        menu.classList.remove("activo");
    });
});

// 2. FECHA ACTUAL
const fechaActual = document.getElementById("fechaActual");
fechaActual.textContent = new Date().toLocaleDateString("es-BO");

// 3. SELECCIONAR HABITACIÓN DESDE UNA TARJETA
const selectHabitacion = document.getElementById("habitacion");

document.querySelectorAll(".seleccionar-habitacion").forEach(function (boton) {
    boton.addEventListener("click", function () {
        selectHabitacion.value = boton.dataset.id;
        calcularTotal();
    });
});

// 4. CÁLCULO SENCILLO DE NOCHES Y PRECIO
const entrada = document.getElementById("entrada");
const salida = document.getElementById("salida");
const resumen = document.getElementById("resumenReserva");

function calcularTotal() {
    if (!entrada.value || !salida.value || !selectHabitacion.value) {
        return;
    }

    const fechaEntrada = new Date(entrada.value);
    const fechaSalida = new Date(salida.value);
    const diferencia = fechaSalida - fechaEntrada;
    const noches = diferencia / (1000 * 60 * 60 * 24);

    if (noches <= 0) {
        resumen.textContent = "La fecha de salida debe ser posterior a la fecha de ingreso.";
        return;
    }

    const opcion = selectHabitacion.options[selectHabitacion.selectedIndex];
    const precio = Number(opcion.dataset.precio || 0);
    const total = noches * precio;

    resumen.innerHTML =
        "<strong>Noches:</strong> " + noches +
        " | <strong>Total estimado:</strong> Bs. " + total.toFixed(2);
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

// 5. BOTÓN PARA VOLVER ARRIBA
const btnArriba = document.getElementById("btnArriba");

window.addEventListener("scroll", function () {
    if (window.scrollY > 350) {
        btnArriba.classList.add("mostrar");
    } else {
        btnArriba.classList.remove("mostrar");
    }
});

btnArriba.addEventListener("click", function () {
    window.scrollTo({ top: 0, behavior: "smooth" });
});
