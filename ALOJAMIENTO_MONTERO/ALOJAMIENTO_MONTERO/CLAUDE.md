# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project context

Educational project (Técnico Medio – Sistemas Informáticos, Bolivia): a website for a **single** lodging establishment in Montero, Santa Cruz, with a public page plus an admin panel. Plain PHP + MySQL (mysqli), vanilla JS/CSS, no framework, no build step, no dependency manager, no tests. All code, comments, UI text and DB identifiers are in Spanish — keep it that way.

The code is intentionally simple so a student can explain it. Preserve that style: procedural PHP, one file per screen, short explanatory comment banners (`/* ===== ... ===== */`), no abstractions/classes/frameworks unless asked. `INSTRUCCIONES.txt` is the student-facing install guide; `docs/` holds the project report (.docx).

**Deliberate design choices (do not "fix" unless asked):**
- Passwords are stored and compared in plain text (`usuario.clave`) by explicit project requirement. Only switch to `password_hash()`/`password_verify()` if requested.
- Only one `alojamiento` row is used; `index.php` reads `LIMIT 1` and admin inserts hardcode `id_alojamiento = 1`. It is not a multi-property platform.

## Running locally

Designed for XAMPP (Apache + MySQL/MariaDB). No PHP/MySQL is installed in this Linux environment by default.

1. Import `bd_alojamiento.sql` (creates DB `alojamiento_montero`, tables, and idempotent seed data via `INSERT ... WHERE NOT EXISTS`).
2. DB credentials are in `conexion.php` (default `root` / empty password / `localhost`).
3. Serve the folder (XAMPP htdocs, or `php -S localhost:8000` from the repo root) and open `index.php`.
4. Admin login: `admin` / `1234` (`login.php`).

Syntax-check a file: `php -l archivo.php`.

## Architecture

- **`conexion.php`** — creates the global `$conexion` (mysqli, utf8mb4). Every page does `require_once "conexion.php"`.
- **`funciones.php`** — presentation helpers: `imagen_habitacion($imagen, $tipo)` (falls back to `img/habitaciones/<tipo>.jpg` / `defecto.jpg` when the URL is empty), `icono_servicio($nombre)` (keyword → icon name), `icono($nombre)` (inlines `img/iconos/<nombre>.svg` into the HTML; the SVGs use `stroke="currentColor"`, so icons take the text color — don't switch back to CSS masks/`<img>`), `recurso($archivo)` (appends `?v=filemtime` — use it for every `.css`/`.js` link, since XAMPP sends no Cache-Control and browsers otherwise keep stale CSS), `clase_estado($estado)` (→ badge class `exito|aviso|peligro|neutro`). New icons: add a 24×24 stroke SVG to `img/iconos/`.
- **Assets are local** (`img/`): logo, favicon, photos, default room images and icons — the site must work offline in XAMPP, so don't reintroduce external image/font/CDN dependencies.
- **Public side**: `index.php` (single-page with anchors `#inicio`, `#habitaciones`, `#servicios`, `#reservas`, `#contacto`; pulls alojamiento/habitaciones/servicios from DB; the reservation `<select>` only lists rooms with `estado='Disponible'`) → posts to `guardar_reserva.php`, which inserts a new `cliente` row on every request (no dedup), then a `reserva` with `estado='Pendiente'`, and redirects back with `?mensaje=...#reservas`. `script.js` handles mobile menu (`aria-expanded`), night/total price estimate (reads `data-precio`/`data-imagen` on `<option>`), date min constraints, header shadow on scroll, active-section highlighting and back-to-top; it expects element IDs `menuBtn`, `menu`, `habitacion`, `entrada`, `salida`, `resumenReserva`, `resumenImagen`, `resumenHabitacion`, `btnArriba`, `header` in `index.php`. Styles: `styles.css`.
- **Admin side**: every `*_admin.php` and `panel.php` starts with `require_once "verificar_sesion.php"` (session guard → redirects to `login.php`), then `conexion.php`. The page uses `<body class="admin">`, sets `$titulo_pagina` and includes `menu_admin.php`, which renders the top app bar + navigation drawer (marks the active link via `basename($_SERVER['PHP_SELF'])`) and loads `funciones.php`; pages end with `<script src="admin.js">` (drawer toggle, login password toggle, room image preview). Styles: `admin.css`. Session keys: `id_usuario`, `nombre_usuario`, `rol` (role is stored but not used for authorization).
- **CRUD page pattern** (`habitaciones_admin.php`, `servicios_admin.php`, `usuarios_admin.php`): one self-posting file that handles `?eliminar=ID` (GET delete + redirect), `?editar=ID` (loads `$editar` to prefill the form), and POST save where a hidden `id_*` of `0` means INSERT, otherwise UPDATE. Left column form, right column table (`.dos-columnas`). Tables use `<thead>/<tbody>` and every `<td>` has `data-label="..."`: a CSS container query (`@container (max-width: 639px)`) turns rows into cards when the table's container is narrow — keep `data-label` on new columns and `class="celda-acciones"` on the actions cell. `usuarios_admin.php` prevents deleting the logged-in user. `clientes_admin.php` is read-only; `reservas_admin.php` lists reservations and changes `estado`.
- **SQL style**: string interpolation with `real_escape_string()` for strings and `(int)`/`(float)` casts for numbers; output escaped with `htmlspecialchars()`. Follow this same convention for new queries (or prepared statements if the user asks for an upgrade).

## Schema (bd_alojamiento.sql)

`alojamiento` 1—N `habitacion`, `servicio`; `cliente` 1—N `reserva` N—1 `habitacion`; `usuario` standalone. Status values are free-text VARCHARs set by UI selects: habitación `Disponible | Ocupada | Mantenimiento`; reserva starts `Pendiente`; usuario estado `Activo` (login requires it), rol `Administrador | Recepción`. Room images are URLs stored in `habitacion.imagen`. Prices are in Bolivianos (`Bs.`).

When changing the schema, update `bd_alojamiento.sql` (keep seed inserts idempotent) and the corresponding admin form.

## Responsive / UI conventions

Both stylesheets are mobile-first (base = phone, then `@media (min-width: 600px)` and `(min-width: 900px)`), use CSS variables on `:root`, Material-style elevation tokens (`--elevacion-1..3`), 8px spacing, pill buttons with a `::after` state layer, and 44–48px touch targets. Admin navigation: modal drawer below 900px, Material navigation rail (icon + label, `--ancho-riel`) from 900px, full drawer (`--ancho-menu`) from 1440px; `body.admin` gets the matching `padding-left`. Form + table side by side (`.dos-columnas`) only from 1280px. Keep admin tables narrow enough to stay tables at those widths (icon-only actions with `<span class="texto-boton">`, `.sin-salto` for short values) — the responsive test should report no internal table scroll.

Verify layout changes at 360/390/768/1024/1280/1440 px with no horizontal scroll on any page (public, login and every admin page).
