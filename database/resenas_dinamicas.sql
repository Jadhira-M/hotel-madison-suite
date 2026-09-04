CREATE DATABASE IF NOT EXISTS hotel_madison_suite
  DEFAULT CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE hotel_madison_suite;

CREATE TABLE IF NOT EXISTS resenas (
  id INT AUTO_INCREMENT PRIMARY KEY,
  usuario_id INT NULL,
  nombre VARCHAR(120) NOT NULL,
  correo VARCHAR(120) NULL,
  pais VARCHAR(80) NULL DEFAULT 'Perú',
  tipo_viaje VARCHAR(80) NULL,
  habitacion VARCHAR(120) NULL,
  noches INT NOT NULL DEFAULT 1,
  puntuacion DECIMAL(3,1) NOT NULL DEFAULT 10.0,
  titulo VARCHAR(120) NULL,
  comentario TEXT NOT NULL,
  lo_mejor VARCHAR(180) NULL,
  mejorar VARCHAR(180) NULL,
  personal INT NOT NULL DEFAULT 8,
  instalaciones INT NOT NULL DEFAULT 8,
  limpieza INT NOT NULL DEFAULT 8,
  confort INT NOT NULL DEFAULT 8,
  calidad_precio INT NOT NULL DEFAULT 8,
  ubicacion INT NOT NULL DEFAULT 8,
  wifi INT NOT NULL DEFAULT 8,
  estado VARCHAR(20) NOT NULL DEFAULT 'publicado',
  creado_en TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_resenas_estado (estado),
  INDEX idx_resenas_puntuacion (puntuacion),
  CONSTRAINT fk_resenas_usuario FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO resenas
(nombre, correo, pais, tipo_viaje, habitacion, noches, puntuacion, titulo, comentario, lo_mejor, mejorar, personal, instalaciones, limpieza, confort, calidad_precio, ubicacion, wifi, estado)
SELECT 'María G.', 'maria@example.com', 'Perú', 'Viaje familiar', 'Habitación Familiar', 2, 9.0, 'Excelente',
       'El desayuno y la habitación estaban muy confortables. La atención fue cordial desde la llegada.',
       'La atención del personal', 'Sin comentarios', 9, 8, 9, 9, 8, 8, 8, 'publicado'
WHERE NOT EXISTS (SELECT 1 FROM resenas WHERE correo = 'maria@example.com');

INSERT INTO resenas
(nombre, correo, pais, tipo_viaje, habitacion, noches, puntuacion, titulo, comentario, lo_mejor, mejorar, personal, instalaciones, limpieza, confort, calidad_precio, ubicacion, wifi, estado)
SELECT 'Carlos R.', 'carlos@example.com', 'Chile', 'Estadía de trabajo', 'Habitación Doble', 1, 8.0, 'Muy bueno',
       'Me gustó que cuentan con cochera y que el personal siempre estuvo atento.',
       'La cochera y la ubicación', 'Mayor variedad en desayuno', 9, 8, 8, 8, 8, 8, 8, 'publicado'
WHERE NOT EXISTS (SELECT 1 FROM resenas WHERE correo = 'carlos@example.com');

INSERT INTO resenas
(nombre, correo, pais, tipo_viaje, habitacion, noches, puntuacion, titulo, comentario, lo_mejor, mejorar, personal, instalaciones, limpieza, confort, calidad_precio, ubicacion, wifi, estado)
SELECT 'Ana T.', 'ana@example.com', 'Perú', 'Fin de semana', 'Suite Familiar', 3, 8.2, 'Muy bueno',
       'Muy buen desayuno, estacionamiento cómodo y un ambiente tranquilo para descansar.',
       'Ambiente tranquilo', 'Sin comentarios', 8, 8, 9, 8, 9, 7, 8, 'publicado'
WHERE NOT EXISTS (SELECT 1 FROM resenas WHERE correo = 'ana@example.com');

INSERT INTO resenas
(nombre, correo, pais, tipo_viaje, habitacion, noches, puntuacion, titulo, comentario, lo_mejor, mejorar, personal, instalaciones, limpieza, confort, calidad_precio, ubicacion, wifi, estado)
SELECT 'Oscar', 'oscar@example.com', 'Chile', 'En familia', 'Habitación Familiar con baño privado', 5, 7.0, 'Bien',
       'La ubicación fue conveniente para movernos por Tacna.',
       'La ubicación', 'Sin comentarios', 7, 7, 8, 7, 7, 9, 7, 'publicado'
WHERE NOT EXISTS (SELECT 1 FROM resenas WHERE correo = 'oscar@example.com');

INSERT INTO resenas
(nombre, correo, pais, tipo_viaje, habitacion, noches, puntuacion, titulo, comentario, lo_mejor, mejorar, personal, instalaciones, limpieza, confort, calidad_precio, ubicacion, wifi, estado)
SELECT 'Walter', 'walter@example.com', 'Uruguay', 'En familia', 'Habitación Familiar', 2, 5.0, 'Normal',
       'La tranquilidad y la amabilidad del personal estuvieron dentro de lo esperado.',
       'La tranquilidad y la amabilidad del personal', 'Estaba dentro de lo esperado', 6, 5, 6, 5, 5, 7, 5, 'publicado'
WHERE NOT EXISTS (SELECT 1 FROM resenas WHERE correo = 'walter@example.com');
