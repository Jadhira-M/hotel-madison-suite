CREATE TABLE IF NOT EXISTS galeria (
  id INT AUTO_INCREMENT PRIMARY KEY,
  titulo VARCHAR(120) NOT NULL,
  imagen VARCHAR(255) NOT NULL,
  orden INT NOT NULL DEFAULT 0,
  estado VARCHAR(20) NOT NULL DEFAULT 'activo',
  creado_en TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO galeria (titulo, imagen, orden, estado)
SELECT 'Fachada Madison Suite', 'assets/img/hotel.jpg', 1, 'activo'
WHERE NOT EXISTS (SELECT 1 FROM galeria WHERE imagen = 'assets/img/hotel.jpg');

INSERT INTO galeria (titulo, imagen, orden, estado)
SELECT 'Desayuno', 'assets/img/servicios/desayuno.jpg', 2, 'activo'
WHERE NOT EXISTS (SELECT 1 FROM galeria WHERE imagen = 'assets/img/servicios/desayuno.jpg');

INSERT INTO galeria (titulo, imagen, orden, estado)
SELECT 'Restaurante', 'assets/img/servicios/restaurante.jpg', 3, 'activo'
WHERE NOT EXISTS (SELECT 1 FROM galeria WHERE imagen = 'assets/img/servicios/restaurante.jpg');

INSERT INTO galeria (titulo, imagen, orden, estado)
SELECT 'Recepción', 'assets/img/recepcion.jpg', 4, 'activo'
WHERE NOT EXISTS (SELECT 1 FROM galeria WHERE imagen = 'assets/img/recepcion.jpg');

INSERT INTO galeria (titulo, imagen, orden, estado)
SELECT 'Habitación doble', 'assets/img/habitaciones/doble.jpg', 5, 'activo'
WHERE NOT EXISTS (SELECT 1 FROM galeria WHERE imagen = 'assets/img/habitaciones/doble.jpg');

INSERT INTO galeria (titulo, imagen, orden, estado)
SELECT 'Habitación familiar', 'assets/img/habitaciones/familiar plus.jpeg', 6, 'activo'
WHERE NOT EXISTS (SELECT 1 FROM galeria WHERE imagen = 'assets/img/habitaciones/familiar plus.jpeg');

INSERT INTO galeria (titulo, imagen, orden, estado)
SELECT 'Cochera', 'assets/img/servicios/estacionamiento.jpg', 7, 'activo'
WHERE NOT EXISTS (SELECT 1 FROM galeria WHERE imagen = 'assets/img/servicios/estacionamiento.jpg');

INSERT INTO galeria (titulo, imagen, orden, estado)
SELECT 'Ambientes del hotel', 'assets/img/servicios/piscina.jpg', 8, 'activo'
WHERE NOT EXISTS (SELECT 1 FROM galeria WHERE imagen = 'assets/img/servicios/piscina.jpg');
