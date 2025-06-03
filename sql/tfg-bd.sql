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

CREATE TABLE recuperaciones (
  id INT AUTO_INCREMENT PRIMARY KEY,
  id_laboratorio CHAR(36) NOT NULL,
  token VARCHAR(64) NOT NULL,
  expiracion DATETIME NOT NULL,
  usado BOOLEAN DEFAULT FALSE,
  FOREIGN KEY (id_laboratorio) REFERENCES laboratorios(id) ON UPDATE CASCADE ON DELETE CASCADE
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

