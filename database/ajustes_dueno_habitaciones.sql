USE hotel_madison_suite;

UPDATE habitaciones
SET aire = 0;

UPDATE habitaciones
SET descripcion = TRIM(REPLACE(REPLACE(REPLACE(REPLACE(descripcion,
    ', aire acondicionado', ''),
    ', con aire acondicionado', ''),
    'aire acondicionado y ', ''),
    'aire acondicionado', ''));

UPDATE habitaciones
SET
    camas = 3,
    capacidad = 3,
    descripcion = 'Habitación cómoda para tres personas, con 3 camas: 1 plaza y 2 camas individuales, baño privado, agua caliente, Internet WiFi, Cable TV, amplia cochera y desayuno tipo buffet.'
WHERE nombre LIKE '%Triple%';
