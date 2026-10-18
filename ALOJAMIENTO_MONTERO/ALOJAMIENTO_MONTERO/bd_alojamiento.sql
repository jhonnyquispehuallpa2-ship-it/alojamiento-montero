-- =====================================================
-- BASE DE DATOS: alojamiento_montero
-- Proyecto de Técnico Medio - Sistemas Informáticos
-- =====================================================

CREATE DATABASE IF NOT EXISTS alojamiento_montero
CHARACTER SET utf8mb4
COLLATE utf8mb4_general_ci;

USE alojamiento_montero;

-- -----------------------------------------------------
-- 1. TABLA ALOJAMIENTO
-- Guarda la información general del establecimiento.
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS alojamiento (
    id_alojamiento INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    direccion VARCHAR(150) NOT NULL,
    telefono VARCHAR(30),
    correo VARCHAR(100),
    descripcion TEXT
);

-- -----------------------------------------------------
-- 2. TABLA HABITACION
-- Guarda las habitaciones y su disponibilidad actual.
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS habitacion (
    id_habitacion INT AUTO_INCREMENT PRIMARY KEY,
    id_alojamiento INT NOT NULL,
    numero VARCHAR(20) NOT NULL,
    tipo VARCHAR(60) NOT NULL,
    capacidad INT NOT NULL,
    precio DECIMAL(10,2) NOT NULL,
    estado VARCHAR(30) NOT NULL DEFAULT 'Disponible',
    imagen VARCHAR(255),
    FOREIGN KEY (id_alojamiento) REFERENCES alojamiento(id_alojamiento)
);

-- -----------------------------------------------------
-- 3. TABLA SERVICIO
-- Guarda los servicios ofrecidos por el alojamiento.
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS servicio (
    id_servicio INT AUTO_INCREMENT PRIMARY KEY,
    id_alojamiento INT NOT NULL,
    nombre VARCHAR(80) NOT NULL,
    descripcion TEXT,
    FOREIGN KEY (id_alojamiento) REFERENCES alojamiento(id_alojamiento)
);

-- -----------------------------------------------------
-- 4. TABLA CLIENTE
-- Guarda los datos básicos del cliente.
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS cliente (
    id_cliente INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    telefono VARCHAR(30) NOT NULL,
    correo VARCHAR(100)
);

-- -----------------------------------------------------
-- 5. TABLA RESERVA
-- Cada solicitud inicia con estado Pendiente.
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS reserva (
    id_reserva INT AUTO_INCREMENT PRIMARY KEY,
    id_cliente INT NOT NULL,
    id_habitacion INT NOT NULL,
    fecha_ingreso DATE NOT NULL,
    fecha_salida DATE NOT NULL,
    cantidad_personas INT NOT NULL DEFAULT 1,
    comentario TEXT,
    estado VARCHAR(30) NOT NULL DEFAULT 'Pendiente',
    FOREIGN KEY (id_cliente) REFERENCES cliente(id_cliente),
    FOREIGN KEY (id_habitacion) REFERENCES habitacion(id_habitacion)
);

-- -----------------------------------------------------
-- 6. TABLA USUARIO
-- La clave se guarda sin cifrado por requerimiento del
-- proyecto educativo. En un sistema real debe cifrarse.
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS usuario (
    id_usuario INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    usuario VARCHAR(50) NOT NULL UNIQUE,
    clave VARCHAR(100) NOT NULL,
    rol VARCHAR(30) NOT NULL DEFAULT 'Administrador',
    estado VARCHAR(20) NOT NULL DEFAULT 'Activo'
);

-- =====================================================
-- DATOS DE PRUEBA
-- =====================================================

INSERT INTO alojamiento (nombre, direccion, telefono, correo, descripcion)
SELECT
    'Hospedajes Montero',
    'Montero, Santa Cruz - Bolivia',
    '70000000',
    'contacto@alojamiento.com',
    'Sistema web adaptable para promocionar habitaciones, servicios y gestionar solicitudes de reserva.'
WHERE NOT EXISTS (SELECT 1 FROM alojamiento);

INSERT INTO habitacion (id_alojamiento, numero, tipo, capacidad, precio, estado, imagen)
SELECT 1, '101', 'Simple', 1, 150.00, 'Disponible',
'https://images.unsplash.com/photo-1611892440504-42a792e24d32?auto=format&fit=crop&w=900&q=80'
WHERE NOT EXISTS (SELECT 1 FROM habitacion WHERE numero='101');

INSERT INTO habitacion (id_alojamiento, numero, tipo, capacidad, precio, estado, imagen)
SELECT 1, '102', 'Doble', 2, 220.00, 'Disponible',
'https://images.unsplash.com/photo-1590490360182-c33d57733427?auto=format&fit=crop&w=900&q=80'
WHERE NOT EXISTS (SELECT 1 FROM habitacion WHERE numero='102');

INSERT INTO habitacion (id_alojamiento, numero, tipo, capacidad, precio, estado, imagen)
SELECT 1, '103', 'Familiar', 4, 300.00, 'Disponible',
'https://images.unsplash.com/photo-1582719478250-c89cae4dc85b?auto=format&fit=crop&w=900&q=80'
WHERE NOT EXISTS (SELECT 1 FROM habitacion WHERE numero='103');

INSERT INTO servicio (id_alojamiento, nombre, descripcion)
SELECT 1, 'WiFi', 'Conexión a Internet para los huéspedes.'
WHERE NOT EXISTS (SELECT 1 FROM servicio WHERE nombre='WiFi');

INSERT INTO servicio (id_alojamiento, nombre, descripcion)
SELECT 1, 'Estacionamiento', 'Espacio disponible para vehículos.'
WHERE NOT EXISTS (SELECT 1 FROM servicio WHERE nombre='Estacionamiento');

INSERT INTO servicio (id_alojamiento, nombre, descripcion)
SELECT 1, 'Baño privado', 'Habitaciones con baño para mayor comodidad.'
WHERE NOT EXISTS (SELECT 1 FROM servicio WHERE nombre='Baño privado');

INSERT INTO servicio (id_alojamiento, nombre, descripcion)
SELECT 1, 'Atención', 'Atención a consultas de los huéspedes.'
WHERE NOT EXISTS (SELECT 1 FROM servicio WHERE nombre='Atención');

-- Usuario inicial del panel administrativo.
INSERT INTO usuario (nombre, usuario, clave, rol, estado)
SELECT 'Administrador', 'admin', '1234', 'Administrador', 'Activo'
WHERE NOT EXISTS (SELECT 1 FROM usuario WHERE usuario='admin');
