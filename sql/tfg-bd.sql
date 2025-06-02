-- Crear base de datos
DROP DATABASE IF EXISTS lab;
CREATE DATABASE lab;
USE lab;

-- Eliminar tablas si existen
DROP TABLE IF EXISTS analisis;
DROP TABLE IF EXISTS usuclimue;
DROP TABLE IF EXISTS muestras;
DROP TABLE IF EXISTS clientes;
DROP TABLE IF EXISTS laboratorios;

-- Tabla de laboratorios
CREATE TABLE laboratorios (
    id CHAR(36) PRIMARY KEY,
    nombre VARCHAR(50) NOT NULL,
    email VARCHAR(50) NOT NULL UNIQUE,
    pass VARCHAR(255) NOT NULL,
    codigo_2fa VARCHAR(6),
    expiracion_2fa DATETIME
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla de clientes
CREATE TABLE clientes (
    id_laboratorio CHAR(36),
    id CHAR(36) PRIMARY KEY,
    nombre VARCHAR(50) NOT NULL,
    email VARCHAR(50) NOT NULL UNIQUE,
    fecha_alta DATETIME NOT NULL DEFAULT NOW(),
    CONSTRAINT fk_clientes_lab_id FOREIGN KEY (id_laboratorio) REFERENCES laboratorios(id) ON UPDATE CASCADE ON DELETE CASCADE,
    UNIQUE(id_laboratorio, email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla de muestras 
CREATE TABLE muestras (
    id_cliente CHAR(36),
    id CHAR(36) PRIMARY KEY,
    numero CHAR(10),
    fecha DATE NOT NULL DEFAULT CURRENT_DATE,
    direccion VARCHAR(50) NOT NULL,
    tipo_analisis ENUM('TOTAL', 'FQ', 'MICRO') NOT NULL,
    enviado BOOLEAN DEFAULT FALSE,
    CONSTRAINT fk_muestras_cli_id FOREIGN KEY (id_cliente) REFERENCES clientes(id) ON UPDATE CASCADE ON DELETE CASCADE,
    UNIQUE (id_cliente, numero)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla de análisis
CREATE TABLE analisis (
    id_muestra CHAR(36) PRIMARY KEY,
    coliformes TINYINT UNSIGNED,
    e_coli TINYINT UNSIGNED,
    pH DECIMAL(2,1),
    turbidez INT UNSIGNED,
    color TINYINT UNSIGNED,
    conductividad FLOAT UNSIGNED,
    dureza SMALLINT UNSIGNED,
    cloro DECIMAL(3,2),
    fecha_analisis DATE DEFAULT CURRENT_DATE,
    completada BOOLEAN DEFAULT FALSE,
    incidencias BOOLEAN DEFAULT FALSE,
    CONSTRAINT fk_analisis_mue_id FOREIGN KEY (id_muestra) REFERENCES muestras(id) ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



/* Inserciones */
/* 
-- Insertar laboratorio
INSERT INTO laboratorios (id, nombre, email, pass)
VALUES (
    UUID(),
    'Bright Falls Lab',
    'rafa.test.php.1@gmail.com',
    'admin123'
);

-- Obtener ID del laboratorio
SET @lab_id = (SELECT id FROM laboratorios WHERE email = 'rafa.test.php.1@gmail.com');

-- Insertar clientes
INSERT INTO clientes (id, id_laboratorio, nombre, email)
VALUES 
(UUID(), @lab_id, 'Ana Torres', 'ana.torres@email.com'),
(UUID(), @lab_id, 'Carlos Méndez', 'carlos.mendez@email.com');

-- Obtener IDs de los clientes
SET @cli1_id = (SELECT id FROM clientes WHERE email = 'ana.torres@email.com');
SET @cli2_id = (SELECT id FROM clientes WHERE email = 'carlos.mendez@email.com');

-- Insertar muestras para cliente 1 (sin anotaciones)
INSERT INTO muestras (id_cliente, id, direccion, tipo_analisis)
VALUES 
(@cli1_id, '2404/00001', 'Av. Siempre Viva 123', 'TOTAL'),
(@cli1_id, '2404/00002', 'Calle Luna 45', 'FQ');

-- Insertar muestras para cliente 2 (sin anotaciones)
INSERT INTO muestras (id_cliente, id, direccion, tipo_analisis)
VALUES 
(@cli2_id, '2404/00003', 'Calle Sol 89', 'MICRO'),
(@cli2_id, '2404/00004', 'Calle Estrella 12', 'TOTAL');

-- Insertar análisis
INSERT INTO analisis (id_muestra, coliformes, e_coli, pH, turbidez, color, conductividad, dureza, cloro, completada)
VALUES 
('2404/00001', 5, 0, 7.1, 1, 2, 120.5, 80, 0.25, true),
('2404/00002', 12, 1, 6.8, 2, 3, 150.0, 90, 0.30, true),
('2404/00003', 25, 5, 6.5, 3, 5, 98.2, 70, 0.15, true),
('2404/00004', 8, 0, 7.4, 1, 2, 110.0, 85, 0.20, true);
 */

/* Selects */
/*
SELECT 
    m.id AS muestra_id,
    c.nombre AS cliente,
    c.email,
    l.nombre AS laboratorio,
    l.email AS lab_email,
    a.pH,
    a.coliformes,
    a.e_coli,
    a.cloro
FROM muestras m
JOIN clientes c ON m.id_cliente = c.id
JOIN laboratorios l ON c.id_usuario = l.id
JOIN analisis a ON m.id = a.id_muestra;
*/

-- Clientes
