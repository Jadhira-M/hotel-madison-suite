CREATE DATABASE IF NOT EXISTS hotel_madison_suite
  DEFAULT CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE hotel_madison_suite;

CREATE TABLE IF NOT EXISTS habitacion_imagenes (
  id INT AUTO_INCREMENT PRIMARY KEY,
  habitacion_id INT NOT NULL,
  imagen VARCHAR(255) NOT NULL,
  orden INT NOT NULL DEFAULT 1,
  creado_en TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_habitacion_imagenes_habitacion (habitacion_id),
  CONSTRAINT fk_habitacion_imagenes_habitacion
    FOREIGN KEY (habitacion_id) REFERENCES habitaciones(id)
    ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
