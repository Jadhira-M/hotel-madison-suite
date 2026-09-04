CREATE DATABASE IF NOT EXISTS hotel_madison_suite
  DEFAULT CHARACTER SET utf8
  COLLATE utf8_spanish_ci;

USE hotel_madison_suite;

ALTER TABLE usuarios CONVERT TO CHARACTER SET utf8 COLLATE utf8_spanish_ci;
ALTER TABLE habitaciones CONVERT TO CHARACTER SET utf8 COLLATE utf8_spanish_ci;
ALTER TABLE reservas CONVERT TO CHARACTER SET utf8 COLLATE utf8_spanish_ci;
ALTER TABLE reclamaciones CONVERT TO CHARACTER SET utf8 COLLATE utf8_spanish_ci;

ALTER TABLE usuarios
  ADD COLUMN IF NOT EXISTS telefono VARCHAR(30) NULL AFTER correo,
  ADD COLUMN IF NOT EXISTS ciudad VARCHAR(80) NULL AFTER telefono;

ALTER TABLE reservas
  ADD COLUMN IF NOT EXISTS codigo VARCHAR(20) NULL AFTER id,
  ADD COLUMN IF NOT EXISTS noches INT NOT NULL DEFAULT 1 AFTER personas,
  ADD COLUMN IF NOT EXISTS precio_noche DECIMAL(10,2) NOT NULL DEFAULT 0.00 AFTER noches,
  ADD COLUMN IF NOT EXISTS total DECIMAL(10,2) NOT NULL DEFAULT 0.00 AFTER precio_noche,
  ADD COLUMN IF NOT EXISTS metodo_pago VARCHAR(40) NULL AFTER total,
  ADD COLUMN IF NOT EXISTS creado_en TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP;

ALTER TABLE reclamaciones
  ADD COLUMN IF NOT EXISTS codigo VARCHAR(20) NULL AFTER id,
  ADD COLUMN IF NOT EXISTS nombre VARCHAR(120) NULL AFTER usuario_id,
  ADD COLUMN IF NOT EXISTS documento VARCHAR(30) NULL AFTER nombre,
  ADD COLUMN IF NOT EXISTS domicilio VARCHAR(180) NULL AFTER documento,
  ADD COLUMN IF NOT EXISTS correo VARCHAR(120) NULL AFTER domicilio,
  ADD COLUMN IF NOT EXISTS telefono VARCHAR(30) NULL AFTER correo,
  ADD COLUMN IF NOT EXISTS tipo_bien VARCHAR(40) NULL AFTER tipo,
  ADD COLUMN IF NOT EXISTS bien_servicio VARCHAR(180) NULL AFTER tipo_bien,
  ADD COLUMN IF NOT EXISTS monto DECIMAL(10,2) NOT NULL DEFAULT 0.00 AFTER bien_servicio,
  ADD COLUMN IF NOT EXISTS pedido TEXT NULL AFTER detalle,
  ADD COLUMN IF NOT EXISTS prioridad VARCHAR(30) NOT NULL DEFAULT 'Media' AFTER pedido,
  ADD COLUMN IF NOT EXISTS fecha_atencion DATETIME NULL AFTER estado;

CREATE TABLE IF NOT EXISTS tarifas_habitacion (
  id INT AUTO_INCREMENT PRIMARY KEY,
  habitacion_id INT NOT NULL,
  precio_base DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  precio_fin_semana DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  precio_feriado DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  actualizado_en TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  UNIQUE KEY uq_tarifa_habitacion (habitacion_id),
  CONSTRAINT fk_tarifa_habitacion FOREIGN KEY (habitacion_id) REFERENCES habitaciones(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

INSERT INTO tarifas_habitacion (habitacion_id, precio_base, precio_fin_semana, precio_feriado)
SELECT id, precio, precio, precio
FROM habitaciones
ON DUPLICATE KEY UPDATE
  precio_base = VALUES(precio_base),
  precio_fin_semana = VALUES(precio_fin_semana),
  precio_feriado = VALUES(precio_feriado);

CREATE TABLE IF NOT EXISTS temporadas_especiales (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nombre VARCHAR(120) NOT NULL,
  fecha_inicio DATE NOT NULL,
  fecha_fin DATE NOT NULL,
  multiplicador DECIMAL(4,2) NOT NULL DEFAULT 1.00,
  estado VARCHAR(20) NOT NULL DEFAULT 'activa',
  creado_en TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

INSERT INTO temporadas_especiales (nombre, fecha_inicio, fecha_fin, multiplicador, estado)
SELECT 'Temporada Alta - Verano', '2026-12-15', '2027-03-15', 1.30, 'activa'
WHERE NOT EXISTS (SELECT 1 FROM temporadas_especiales WHERE nombre = 'Temporada Alta - Verano');

INSERT INTO temporadas_especiales (nombre, fecha_inicio, fecha_fin, multiplicador, estado)
SELECT 'Fiestas Patrias', '2026-07-26', '2026-07-31', 1.50, 'activa'
WHERE NOT EXISTS (SELECT 1 FROM temporadas_especiales WHERE nombre = 'Fiestas Patrias');

INSERT INTO temporadas_especiales (nombre, fecha_inicio, fecha_fin, multiplicador, estado)
SELECT 'Año Nuevo', '2026-12-28', '2027-01-03', 1.60, 'activa'
WHERE NOT EXISTS (SELECT 1 FROM temporadas_especiales WHERE nombre = 'Año Nuevo');

CREATE TABLE IF NOT EXISTS reserva_pagos (
  id INT AUTO_INCREMENT PRIMARY KEY,
  reserva_id INT NOT NULL,
  metodo VARCHAR(40) NOT NULL,
  monto DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  estado VARCHAR(20) NOT NULL DEFAULT 'pendiente',
  creado_en TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_pago_reserva FOREIGN KEY (reserva_id) REFERENCES reservas(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

CREATE TABLE IF NOT EXISTS servicios_hotel (
  id INT AUTO_INCREMENT PRIMARY KEY,
  icono VARCHAR(50) NOT NULL DEFAULT 'stars',
  nombre VARCHAR(120) NOT NULL,
  descripcion VARCHAR(255) NOT NULL,
  categoria VARCHAR(80) NOT NULL,
  estado VARCHAR(20) NOT NULL DEFAULT 'activo'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

INSERT INTO servicios_hotel (icono, nombre, descripcion, categoria, estado)
SELECT 'wifi', 'WiFi Gratis', 'Internet de alta velocidad en todo el hotel', 'Tecnologia', 'activo'
WHERE NOT EXISTS (SELECT 1 FROM servicios_hotel WHERE nombre = 'WiFi Gratis');
INSERT INTO servicios_hotel (icono, nombre, descripcion, categoria, estado)
SELECT 'car-front', 'Cochera Privada', 'Capacidad para 14 vehiculos', 'Estacionamiento', 'activo'
WHERE NOT EXISTS (SELECT 1 FROM servicios_hotel WHERE nombre = 'Cochera Privada');
INSERT INTO servicios_hotel (icono, nombre, descripcion, categoria, estado)
SELECT 'cup-hot', 'Desayuno Buffet', 'Buffet continental incluido', 'Alimentacion', 'activo'
WHERE NOT EXISTS (SELECT 1 FROM servicios_hotel WHERE nombre = 'Desayuno Buffet');
INSERT INTO servicios_hotel (icono, nombre, descripcion, categoria, estado)
SELECT 'egg-fried', 'Restaurante', 'Servicio de restaurante y bar', 'Alimentacion', 'activo'
WHERE NOT EXISTS (SELECT 1 FROM servicios_hotel WHERE nombre = 'Restaurante');
INSERT INTO servicios_hotel (icono, nombre, descripcion, categoria, estado)
SELECT 'stars', 'Suite Hidromasaje', 'Tinas de hidromasaje en suites premium', 'Habitaciones', 'activo'
WHERE NOT EXISTS (SELECT 1 FROM servicios_hotel WHERE nombre = 'Suite Hidromasaje');
INSERT INTO servicios_hotel (icono, nombre, descripcion, categoria, estado)
SELECT 'droplet', 'Lavanderia', 'Servicio de lavado y planchado', 'Servicios', 'activo'
WHERE NOT EXISTS (SELECT 1 FROM servicios_hotel WHERE nombre = 'Lavanderia');
INSERT INTO servicios_hotel (icono, nombre, descripcion, categoria, estado)
SELECT 'wind', 'Aire Acondicionado', 'Climatizacion en todas las habitaciones', 'Habitaciones', 'activo'
WHERE NOT EXISTS (SELECT 1 FROM servicios_hotel WHERE nombre = 'Aire Acondicionado');
INSERT INTO servicios_hotel (icono, nombre, descripcion, categoria, estado)
SELECT 'tv', 'TV Cable', 'Canales de cable en todas las habitaciones', 'Entretenimiento', 'activo'
WHERE NOT EXISTS (SELECT 1 FROM servicios_hotel WHERE nombre = 'TV Cable');

CREATE TABLE IF NOT EXISTS proveedores (
  id INT AUTO_INCREMENT PRIMARY KEY,
  categoria VARCHAR(100) NOT NULL,
  calificacion INT NOT NULL DEFAULT 5,
  email VARCHAR(120) NOT NULL,
  telefono VARCHAR(30) NOT NULL,
  direccion VARCHAR(180) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

INSERT INTO proveedores (categoria, calificacion, email, telefono, direccion)
SELECT 'Tecnologia', 5, 'tecnologia@madisonsuite.com', '+51 987 654 320', 'Tacna, Peru'
WHERE NOT EXISTS (SELECT 1 FROM proveedores WHERE categoria = 'Tecnologia');
INSERT INTO proveedores (categoria, calificacion, email, telefono, direccion)
SELECT 'Estacionamiento', 5, 'cochera@madisonsuite.com', '+51 987 654 321', 'Tacna, Peru'
WHERE NOT EXISTS (SELECT 1 FROM proveedores WHERE categoria = 'Estacionamiento');
INSERT INTO proveedores (categoria, calificacion, email, telefono, direccion)
SELECT 'Alimentacion', 5, 'restaurante@madisonsuite.com', '+51 987 654 322', 'Tacna, Peru'
WHERE NOT EXISTS (SELECT 1 FROM proveedores WHERE categoria = 'Alimentacion');
INSERT INTO proveedores (categoria, calificacion, email, telefono, direccion)
SELECT 'Habitaciones', 5, 'habitaciones@madisonsuite.com', '+51 987 654 323', 'Tacna, Peru'
WHERE NOT EXISTS (SELECT 1 FROM proveedores WHERE categoria = 'Habitaciones');

CREATE TABLE IF NOT EXISTS proveedor_historial (
  id INT AUTO_INCREMENT PRIMARY KEY,
  proveedor_id INT NOT NULL,
  fecha DATE NOT NULL,
  servicio VARCHAR(120) NOT NULL,
  ubicacion VARCHAR(120) NOT NULL,
  estado VARCHAR(20) NOT NULL DEFAULT 'pendiente',
  observacion TEXT NULL,
  CONSTRAINT fk_historial_proveedor FOREIGN KEY (proveedor_id) REFERENCES proveedores(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

INSERT INTO proveedor_historial (proveedor_id, fecha, servicio, ubicacion, estado, observacion)
SELECT p.id, '2026-05-01', 'WiFi Gratis', 'Todo el hotel', 'completado', 'Revision general de red y routers principales.'
FROM proveedores p
WHERE p.categoria = 'Tecnologia'
  AND NOT EXISTS (SELECT 1 FROM proveedor_historial ph WHERE ph.proveedor_id = p.id);

INSERT INTO proveedor_historial (proveedor_id, fecha, servicio, ubicacion, estado, observacion)
SELECT p.id, '2026-05-03', 'Cochera Privada', 'Area externa', 'completado', 'Control de capacidad y señalizacion de espacios.'
FROM proveedores p
WHERE p.categoria = 'Estacionamiento'
  AND NOT EXISTS (SELECT 1 FROM proveedor_historial ph WHERE ph.proveedor_id = p.id);

INSERT INTO proveedor_historial (proveedor_id, fecha, servicio, ubicacion, estado, observacion)
SELECT p.id, '2026-05-05', 'Desayuno Buffet', 'Restaurante', 'completado', 'Reposicion completa de insumos para desayuno.'
FROM proveedores p
WHERE p.categoria = 'Alimentacion'
  AND NOT EXISTS (SELECT 1 FROM proveedor_historial ph WHERE ph.proveedor_id = p.id);

INSERT INTO proveedor_historial (proveedor_id, fecha, servicio, ubicacion, estado, observacion)
SELECT p.id, '2026-05-10', 'Suite Hidromasaje', 'Suite 301', 'completado', 'Mantenimiento preventivo de hidromasaje.'
FROM proveedores p
WHERE p.categoria = 'Habitaciones'
  AND NOT EXISTS (SELECT 1 FROM proveedor_historial ph WHERE ph.proveedor_id = p.id);
