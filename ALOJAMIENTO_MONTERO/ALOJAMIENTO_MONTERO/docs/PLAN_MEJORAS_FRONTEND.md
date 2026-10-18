# Plan de mejoras visuales, UI/UX y diseño responsivo

**Proyecto:** Sistema web para alojamiento en Montero
**Fecha:** 18/10/2026
**Alcance:** página pública (`index.php`), login y panel administrativo completo.
**Estado:** Fases 1 a 6 implementadas el 18/10/2026. Fase 7 (opcional) pendiente.

---

## 1. Objetivos

1. Que la página de inicio sea **más atractiva**: logo propio, imágenes de calidad, iconos por servicio y mejor jerarquía visual.
2. Que una habitación registrada **sin URL de imagen** muestre igualmente una **imagen por defecto** acorde a su tipo.
3. Que cada servicio muestre un **icono adecuado** según su nombre (WiFi, estacionamiento, baño privado, etc.).
4. Mejorar la **experiencia de uso (UX)**: navegación clara, estados visibles, formularios cómodos y mensajes comprensibles.
5. Que **toda la aplicación sea responsiva** (celular, tablet y computadora), **incluido el panel de administración**.

## 2. Restricciones (se mantienen)

- Proyecto educativo de Técnico Medio: el código debe seguir siendo **sencillo de explicar**.
- PHP procedimental + MySQL (mysqli), HTML, CSS y JavaScript **sin frameworks** y **sin paso de compilación**.
- Debe funcionar en **XAMPP sin conexión a Internet** (para la defensa del proyecto). Por eso las imágenes por defecto, los iconos y el logo serán **archivos locales** dentro de `img/`.
- Todo el texto, los comentarios y los nombres siguen en **español**.
- No se cambia la lógica existente (reservas, login, CRUD). Los IDs que usa `script.js` (`menuBtn`, `menu`, `fechaActual`, `habitacion`, `entrada`, `salida`, `resumenReserva`, `btnArriba`) se conservan.

## 3. Problemas detectados en el estado actual

| Área | Problema |
|---|---|
| Habitaciones | Si `habitacion.imagen` está vacío se muestra una imagen rota. Si la URL externa falla tampoco hay respaldo. |
| Servicios | Todos los servicios usan el mismo icono `✓`. |
| Marca | No hay logo; el encabezado solo muestra el nombre en texto. No hay favicon. |
| Imágenes | Las imágenes del hero y de "Nosotros" dependen de Unsplash, así que sin Internet no se ven. La carpeta `img/` está vacía. |
| Público responsivo | Solo existe un punto de quiebre (850 px). Entre 850 y 1100 px la grilla de 3 habitaciones y la de 4 servicios quedan apretadas. La barra superior no se adapta en pantallas pequeñas. |
| Panel responsivo | El menú del panel (9 enlaces) se amontona en celular y no tiene botón hamburguesa. Las tablas solo tienen scroll horizontal, incómodo en móvil. La columna fija de `360px` del formulario desborda en pantallas angostas. |
| UX | El estado de habitaciones y reservas es texto plano, sin color. El menú no marca la sección actual. No hay estados de foco visibles para usar con teclado. |

## 4. Diseño propuesto (guía visual)

### 4.1 Identidad
- **Logo:** archivo `img/logo.svg`, un isotipo sencillo (casa/techo con una hoja o un sol que haga referencia a Montero) más el nombre. Se usará en el encabezado, el pie de página, el login, el panel y como favicon.
- **Paleta:** se mantiene la gama verde actual como base y se agrega un color de acento cálido para las llamadas a la acción.

```css
:root {
    --verde-oscuro: #12372a;
    --verde: #436850;
    --verde-claro: #adbc9f;
    --acento: #e0a458;      /* nuevo: botones principales y precios */
    --fondo: #f4f7f5;
    --blanco: #ffffff;
    --texto: #26332d;
    --gris: #66736c;
    --borde: #dfe8e2;
    --exito: #2e7d4f;  --aviso: #c98a1b;  --peligro: #b3261e;
    --radio: 12px;
    --sombra: 0 10px 30px rgba(18, 55, 42, .10);
}
```

- **Tipografía:** fuente del sistema (`system-ui, "Segoe UI", Roboto, Arial, sans-serif`), que no necesita Internet. Opcionalmente, "Poppins" desde Google Fonts, con la fuente del sistema como respaldo.
- **Componentes comunes:** botones (principal, secundario, peligro), tarjetas, insignias de estado (*badges*), alertas y campos de formulario con estilo de foco.

### 4.2 Puntos de quiebre (*mobile-first*)
| Nombre | Ancho | Uso |
|---|---|---|
| Base | < 600 px | Celular: 1 columna |
| Tablet | ≥ 600 px | 2 columnas |
| Laptop | ≥ 900 px | Menú horizontal, 3 columnas |
| Escritorio | ≥ 1200 px | 4 columnas en servicios |

Siempre que se pueda se usarán grillas automáticas, que se adaptan solas sin necesidad de media queries:
```css
.habitaciones-grid { grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); }
.servicios-grid    { grid-template-columns: repeat(auto-fill, minmax(220px, 1fr)); }
```

---

## 5. Fases de trabajo

### Fase 1: Recursos gráficos locales
**Archivos nuevos en `img/`:**
```
img/
├── logo.svg                    ← logo principal
├── favicon.svg
├── hero.jpg                    ← portada (reemplaza la URL de Unsplash)
├── nosotros.jpg
├── habitaciones/
│   ├── simple.jpg
│   ├── doble.jpg
│   ├── matrimonial.jpg
│   ├── familiar.jpg
│   ├── suite.jpg
│   └── defecto.jpg             ← cuando el tipo no coincide con ninguno
└── iconos/                     ← SVG de una sola línea (licencia libre, p. ej. Lucide, ISC)
    ├── wifi.svg  estacionamiento.svg  bano.svg  desayuno.svg
    ├── aire.svg  tv.svg  lavanderia.svg  piscina.svg
    ├── seguridad.svg  atencion.svg  limpieza.svg
    └── servicio.svg            ← icono genérico de respaldo
```
- Fotos optimizadas a unos 1200 px de ancho y menos de 250 KB cada una (JPG o WebP), de fuentes con licencia libre (Unsplash o Pexels). Se anotará la fuente en un `img/CREDITOS.txt`.

### Fase 2: Funciones auxiliares en PHP (`funciones.php`, nuevo)
Un archivo pequeño con dos funciones, incluido con `require_once` en `index.php` (y en el panel para la vista previa):

```php
// Devuelve la imagen de la habitación o una imagen local según su tipo.
function imagen_habitacion($imagen, $tipo) { ... }

// Devuelve la ruta del icono SVG según palabras clave del nombre del servicio.
function icono_servicio($nombre) { ... }
```

- **`imagen_habitacion`:** si `$imagen` no está vacío, la devuelve. Si no, normaliza `$tipo` (minúsculas, sin tildes) y busca coincidencias: "simple/individual" → `simple.jpg`, "doble" → `doble.jpg`, "matrimonial" → `matrimonial.jpg`, "familiar" → `familiar.jpg`, "suite" → `suite.jpg`. Si nada coincide → `defecto.jpg`.
- **`icono_servicio`:** tabla de palabras clave → icono, por ejemplo `wifi|internet` → `wifi.svg`, `estacionamiento|parqueo|garaje` → `estacionamiento.svg`, `baño|ducha` → `bano.svg`, `desayuno|comida|restaurante` → `desayuno.svg`, `aire|climatiz` → `aire.svg`, `tv|televis|cable` → `tv.svg`, `lavander` → `lavanderia.svg`, `piscina` → `piscina.svg`, `seguridad|cámara` → `seguridad.svg`, `atención|recepción|24` → `atencion.svg`, `limpieza` → `limpieza.svg`. Si nada coincide → `servicio.svg`.
- **Respaldo si la URL externa falla:** en la etiqueta `<img>` se agrega `onerror="this.onerror=null;this.src='img/habitaciones/defecto.jpg'"`.

> **No requiere cambiar la base de datos.** Una mejora opcional (Fase 7) sería agregar una columna `servicio.icono` para elegir el icono manualmente desde el panel.

### Fase 3: Página pública (`index.php`, `styles.css`, `script.js`)
1. **Encabezado**
   - Logo SVG y nombre del alojamiento.
   - Menú con botón hamburguesa animado (☰ → ✕) y `aria-expanded`. El menú desplegable ocupa todo el ancho en móvil.
   - El encabezado se vuelve compacto y con sombra al hacer scroll (clase `.scrolled`, aplicada desde `script.js`).
   - Resaltado del enlace de la sección visible (`IntersectionObserver`, unas 15 líneas).
   - La barra superior se oculta o se simplifica en pantallas de menos de 600 px.
2. **Hero / portada**
   - Imagen local `img/hero.jpg` con degradado verde.
   - Título, subtítulo y **dos botones**: "Reservar ahora" (acento) y "Ver habitaciones" (secundario).
   - Tres datos destacados debajo: "Desde Bs. XX/noche" (precio mínimo obtenido por consulta), "N habitaciones" y "Atención personalizada".
3. **Nosotros:** imagen local, texto, y una lista de 3 o 4 ventajas con iconos.
4. **Habitaciones: tarjetas rediseñadas**
   - Imagen con `imagen_habitacion()`, proporción fija (`aspect-ratio: 4/3; object-fit: cover`), `loading="lazy"` y efecto de zoom suave al pasar el mouse.
   - **Insignia de estado** sobre la imagen: verde "Disponible", naranja "Ocupada", gris "Mantenimiento".
   - Iconos pequeños para el número de habitación y la capacidad (👤 × N).
   - Precio destacado con el color de acento.
   - Botón "Reservar" a todo el ancho de la tarjeta. Al pulsarlo se preselecciona la habitación (ya existe) y se hace scroll al formulario.
5. **Servicios:** tarjetas con el **icono SVG** de `icono_servicio()` dentro de un círculo de color suave, título y descripción.
6. **Formulario de reserva**
   - En escritorio, dos columnas: el formulario y una tarjeta de **resumen** (habitación elegida, noches, total estimado) que se queda fija al hacer scroll. En móvil, el resumen va debajo del formulario.
   - Campos con `type="tel"`, `autocomplete` y `inputmode` adecuados para que el teclado del celular sea el correcto.
   - Estados de foco y de error visibles con CSS (`:focus-visible`, `:user-invalid`).
   - El mensaje de respuesta (`?mensaje=`) se muestra como alerta de éxito o error, con su icono y un botón para cerrarlo.
7. **Contacto**
   - Tarjetas con iconos. El teléfono abre `tel:` y el correo abre `mailto:`.
   - Botón opcional de WhatsApp (`https://wa.me/591XXXXXXXX`) usando el teléfono registrado.
   - Mapa opcional de Google Maps (iframe) solo si hay Internet. No es obligatorio.
8. **Pie de página:** logo, enlaces rápidos, datos de contacto y derechos, en 3 columnas que pasan a 1 en móvil.
9. **Detalles generales:** botón "volver arriba" con transición, `prefers-reduced-motion` para desactivar animaciones y contraste de color AA.

### Fase 4: Login (`login.php`, `admin.css`)
- Diseño en dos paneles en escritorio (imagen o color de marca con el logo a la izquierda, formulario a la derecha) y un solo panel centrado en móvil.
- Botón para mostrar u ocultar la contraseña.
- El bloque "Usuario inicial: admin / 1234" se mantiene, porque es parte de la demostración educativa.

### Fase 5: Panel administrativo responsivo (todas las páginas `*_admin.php`, `panel.php`, `menu_admin.php`, `admin.css` y un nuevo `admin.js`)
1. **Nueva estructura de diseño**
   - **Escritorio (≥ 900 px):** barra lateral fija a la izquierda con el logo, enlaces con icono y el botón "Salir" abajo. El contenido va a la derecha.
   - **Móvil y tablet:** barra superior con el logo y un botón hamburguesa. La barra lateral se abre como **menú deslizable** con un fondo oscuro detrás, y se cierra al tocar fuera o con la tecla Esc.
   - `menu_admin.php` **marca el enlace activo** comparando `basename($_SERVER['PHP_SELF'])` y agregando la clase `activo`.
   - `admin.js` (nuevo, unas 20 líneas) solo abre y cierra el menú.
2. **Tablas → tarjetas en móvil**
   - En pantallas de menos de 700 px, cada fila `<tr>` se muestra como una tarjeta y cada celda como "Etiqueta: valor".
   - Se agrega `data-label="..."` en cada `<td>` y se usa `td::before { content: attr(data-label); }`.
   - Se cambia la primera fila a `<thead><tr><th>…</th></tr></thead>` con `<tbody>`, para poder ocultar la cabecera en móvil.
   - Aplica a: habitaciones, servicios, clientes, reservas y usuarios.
3. **Formularios**
   - `.dos-columnas` pasa de `360px 1fr` a `minmax(0, 360px) 1fr` en escritorio y a 1 columna en móvil.
   - Los botones de los formularios ocupan todo el ancho en móvil.
   - En habitaciones: **vista previa de la imagen** (la de la URL o la imagen por defecto según el tipo), para que el administrador sepa qué se mostrará si deja la URL vacía.
4. **Inicio del panel (`panel.php`):** tarjetas de resumen con icono y color (habitaciones, clientes, reservas, pendientes) en `repeat(auto-fit, minmax(200px, 1fr))`, más accesos rápidos a las secciones.
5. **Estados con color:** insignias para las reservas (Pendiente = naranja, Confirmada = verde, Cancelada = rojo, Finalizada = gris) y para las habitaciones.
6. **Acciones:** "Editar" y "Eliminar" como botones pequeños con icono en lugar de enlaces de texto, con un área táctil mínima de 44 px.

### Fase 6: Pruebas y verificación
- **Anchos a revisar** con las herramientas de desarrollo del navegador (Ctrl+Shift+M): 360, 390, 768, 1024, 1280 y 1440 px. También en un celular real conectado a la misma red, usando `http://IP-de-la-PC/ALOJAMIENTO_MONTERO_PHP_MYSQL/`.
- **Lista de control:**
  - [x] No hay scroll horizontal en ninguna página y en ningún ancho.
  - [x] Una habitación registrada **sin URL** muestra la imagen por defecto de su tipo.
  - [x] Una habitación con **URL rota** muestra `defecto.jpg`.
  - [x] Un servicio nuevo (p. ej., "Desayuno incluido") muestra el icono correcto, y uno desconocido muestra el genérico.
  - [x] El menú hamburguesa funciona en la página pública y en el panel.
  - [x] Las tablas del panel se ven como tarjetas en el celular.
  - [x] Reservar desde una tarjeta preselecciona la habitación y calcula el total.
  - [x] Con el wifi apagado, todas las imágenes, iconos y el logo se siguen viendo (verificado: todos los recursos de la página son locales; las habitaciones 101–103 de los datos de prueba usan URL de Unsplash y sin Internet mostrarían `defecto.jpg`).
  - [ ] (no ejecutado) Lighthouse (Chrome) con Accesibilidad ≥ 90 y Buenas prácticas ≥ 90.
  - [x] `php -l` sin errores en todos los archivos modificados.

### Fase 7 (opcional): mejoras extra
- Columna `icono` en la tabla `servicio`, con un selector visual en `servicios_admin.php`. Requiere actualizar `bd_alojamiento.sql`.
- Subir imágenes de habitaciones desde el panel (`<input type="file">` y `move_uploaded_file` a `img/habitaciones/subidas/`) en lugar de pegar una URL.
- Galería con varias fotos por habitación.
- Modo oscuro con `prefers-color-scheme`.

---

## 6. Resumen de archivos afectados

| Archivo | Cambio |
|---|---|
| `img/**` | **Nuevo:** logo, favicon, fotos locales, imágenes por defecto e iconos SVG |
| `funciones.php` | **Nuevo:** `imagen_habitacion()` e `icono_servicio()` |
| `index.php` | Encabezado con logo, hero, tarjetas, iconos, formulario y pie de página |
| `styles.css` | Reescritura *mobile-first* con variables y componentes |
| `script.js` | Encabezado al hacer scroll, enlace activo y `aria-expanded` (se conserva lo existente) |
| `login.php` | Nuevo diseño y botón para ver la contraseña |
| `menu_admin.php` | Barra lateral con iconos, logo y enlace activo |
| `admin.js` | **Nuevo:** abrir y cerrar el menú del panel |
| `admin.css` | Reescritura responsiva: barra lateral, tablas como tarjetas e insignias |
| `panel.php`, `*_admin.php` | Nueva estructura (contenedor y cabecera), `thead/tbody`, `data-label`, insignias y vista previa de imagen |
| `INSTRUCCIONES.txt` | Mencionar `funciones.php`, `admin.js` y la carpeta `img/` |

## 7. Orden sugerido y estimación

| # | Tarea | Estimación |
|---|---|---|
| 1 | Fase 1: recursos gráficos | 1 h |
| 2 | Fase 2: `funciones.php` | 0,5 h |
| 3 | Fase 3: página pública | 3–4 h |
| 4 | Fase 4: login | 0,5 h |
| 5 | Fase 5: panel responsivo | 3–4 h |
| 6 | Fase 6: pruebas | 1 h |
| | **Total** | **≈ 9–11 h** |

Cada fase se puede entregar y probar por separado en XAMPP (`http://localhost/ALOJAMIENTO_MONTERO_PHP_MYSQL/`) sin romper las demás.
