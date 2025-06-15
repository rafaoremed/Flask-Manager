-- Crear base de datos
DROP DATABASE IF EXISTS lab;

CREATE DATABASE lab;

USE lab;

-- Eliminar tablas si existen
DROP TABLE IF EXISTS analisis;
DROP TABLE IF EXISTS muestras;
DROP TABLE IF EXISTS clientes;
DROP TABLE IF EXISTS recuperaciones;
DROP TABLE IF EXISTS laboratorios;

-- Tabla de laboratorios
CREATE TABLE laboratorios (
    id CHAR(36) PRIMARY KEY,
    nombre VARCHAR(50) NOT NULL,
    email VARCHAR(50) NOT NULL UNIQUE,
    pass VARCHAR(255) NOT NULL,
    codigo_2fa VARCHAR(6),
    expiracion_2fa DATETIME
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

CREATE TABLE recuperaciones (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_laboratorio CHAR(36) NOT NULL,
    token VARCHAR(64) NOT NULL,
    expiracion DATETIME NOT NULL,
    usado BOOLEAN DEFAULT FALSE,
    FOREIGN KEY (id_laboratorio) REFERENCES laboratorios (id) ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

-- Tabla de clientes
CREATE TABLE clientes (
    id_laboratorio CHAR(36),
    id CHAR(36) PRIMARY KEY,
    nombre VARCHAR(50) NOT NULL,
    email VARCHAR(50) NOT NULL,
    fecha_alta DATETIME NOT NULL DEFAULT NOW(),
    CONSTRAINT fk_clientes_lab_id FOREIGN KEY (id_laboratorio) REFERENCES laboratorios (id) ON UPDATE CASCADE ON DELETE CASCADE,
    UNIQUE (id_laboratorio, email)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

-- Tabla de muestras
CREATE TABLE muestras (
    id_cliente CHAR(36),
    id CHAR(36) PRIMARY KEY,
    numero CHAR(10),
    fecha DATE,
    direccion VARCHAR(50) NOT NULL,
    tipo_analisis ENUM('TOTAL', 'FQ', 'MICRO') NOT NULL,
    enviado BOOLEAN DEFAULT FALSE,
    CONSTRAINT fk_muestras_cli_id FOREIGN KEY (id_cliente) REFERENCES clientes (id) ON UPDATE CASCADE ON DELETE CASCADE,
    UNIQUE (id_cliente, numero)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

-- Tabla de análisis
CREATE TABLE analisis (
    id_muestra CHAR(36) PRIMARY KEY,
    coliformes TINYINT UNSIGNED,
    e_coli TINYINT UNSIGNED,
    pH DECIMAL(3, 1),
    turbidez INT UNSIGNED,
    color TINYINT UNSIGNED,
    conductividad FLOAT UNSIGNED,
    dureza SMALLINT UNSIGNED,
    cloro DECIMAL(3, 2),
    fecha_analisis DATE ,
    completada BOOLEAN DEFAULT FALSE,
    incidencias BOOLEAN DEFAULT FALSE,
    CONSTRAINT fk_analisis_mue_id FOREIGN KEY (id_muestra) REFERENCES muestras (id) ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

-- 1) Insertar laboratorio
INSERT INTO
    laboratorios (id, nombre, email, pass)
VALUES (
        '2876763b-0648-484c-a2c7-0f428fd51d16', -- UUID fijo
        'Bright Falls Lab',
        'rafa.test.php.1@gmail.com',
        '$2b$12$48ZLB.ae/D1jrtzeKppMzuXu.ZtDh4.K5v8GmeVO7FZH7g20deKjK' -- bcrypt de 'FManager@123'
    );

-- 2) Insertar clientes (todos asociados al laboratorio anterior)
INSERT INTO
    clientes (
        id,
        id_laboratorio,
        nombre,
        email,
        fecha_alta
    )
VALUES (
        '1afdd452-843c-4303-9e65-b46f59a76916', -- UUID para Ana Torres
        '2876763b-0648-484c-a2c7-0f428fd51d16',
        'Ana Torres',
        'ana.torres@email.com',
        NOW()
    ),
    (
        '1974ac94-1493-467b-95a9-614e72dfb4c9', -- UUID para Carlos Méndez
        '2876763b-0648-484c-a2c7-0f428fd51d16',
        'Carlos Méndez',
        'carlos.mendez@email.com',
        NOW()
    ),
    (
        'afcf43d2-cf72-469a-a7ff-ae5d69d24559', -- UUID para Jesus Encinas
        '2876763b-0648-484c-a2c7-0f428fd51d16',
        'Jesus Encinas',
        'jencinas35@email.com',
        NOW()
    ),
    (
        '44946325-0013-42f8-958f-5f2e7cf5cd6c', -- UUID para Marta Blanco
        '2876763b-0648-484c-a2c7-0f428fd51d16',
        'Marta Blanco',
        'mblanco55@email.com',
        NOW()
    ),
    (
        '61966f82-c06c-4cb2-b490-5b5a3d712eb0', -- UUID para Pilar García
        '2876763b-0648-484c-a2c7-0f428fd51d16',
        'Pilar García',
        'pilarghtr86@email.com',
        NOW()
    ),
    (
        '445909bf-efc3-422f-ae86-73650bcfc371', -- UUID para Ayto. San García
        '2876763b-0648-484c-a2c7-0f428fd51d16',
        'Ayto. San García',
        'aytosg@administracion.com',
        NOW()
    ),
    (
        '61de0eb6-5abe-4903-bbd4-310e84c6aab5', -- UUID para Ayto. Villablanca
        '2876763b-0648-484c-a2c7-0f428fd51d16',
        'Ayto. Villablanca',
        'aytovb@administracion.com',
        NOW()
    ),
    (
        'c8149f4b-29d9-46f0-89f6-9b9258ef83da', -- UUID para Ayto. Carbonero
        '2876763b-0648-484c-a2c7-0f428fd51d16',
        'Ayto. Carbonero',
        'aytocrbnr@administracion.com',
        NOW()
    ),
    (
        '5aa92fef-e3c5-4399-85d4-2532c877b531', -- UUID para Ayto. Valsain
        '2876763b-0648-484c-a2c7-0f428fd51d16',
        'Ayto. Valsain',
        'aytolgvsn@administracion.com',
        NOW()
    ),
    (
        '16b50c7c-6357-4579-8481-eeb6da203162', -- UUID para Ayto. Real Sitio de San Ildefonso
        '2876763b-0648-484c-a2c7-0f428fd51d16',
        'Ayto. Real Sitio de San Ildefonso',
        'aytorslg@administracion.com',
        NOW()
    );

-- 3) Insertar muestras (solo para los dos primeros clientes)
INSERT INTO
    muestras (
        id,
        id_cliente,
        numero,
        fecha,
        direccion,
        tipo_analisis,
        enviado
    )
VALUES (
        '102106b0-4758-4dc6-9858-b34b4760fe32', -- UUID para muestra 2506/00001
        '1afdd452-843c-4303-9e65-b46f59a76916', -- Ana Torres
        '2506/00001',
        '2025-06-02',
        'Av. Siempre Viva 123',
        'TOTAL',
        FALSE
    ),
    (
        'ea161443-22d3-4439-84fa-fbd66ebdc302', -- UUID para muestra 2506/00002
        '1afdd452-843c-4303-9e65-b46f59a76916', -- Ana Torres
        '2506/00002',
        '2025-06-02',
        'Calle Luna 45',
        'FQ',
        FALSE
    ),
    (
        '2c6b58f9-c72a-42f8-911f-6501a9e4e75e', -- UUID para muestra 2506/00003
        '1974ac94-1493-467b-95a9-614e72dfb4c9', -- Carlos Méndez
        '2506/00003',
        '2025-06-02',
        'Calle Sol 89',
        'MICRO',
        FALSE
    ),
    (
        '00223ad1-aa37-4b2f-8a93-183b5141e2d6', -- UUID para muestra 2506/00004
        '1974ac94-1493-467b-95a9-614e72dfb4c9', -- Carlos Méndez
        '2506/00004',
        '2025-06-02',
        'Calle Estrella 12',
        'TOTAL',
        FALSE
    );

-- 4) Insertar análisis correspondientes a cada muestra
INSERT INTO
    analisis (
        id_muestra,
        coliformes,
        e_coli,
        pH,
        turbidez,
        color,
        conductividad,
        dureza,
        cloro,
        fecha_analisis,
        completada,
        incidencias
    )
VALUES (
        '102106b0-4758-4dc6-9858-b34b4760fe32', -- para la muestra 2506/00001
        5,
        0,
        7.1,
        1,
        2,
        120.5,
        80,
        0.25,
        '2025-06-02',
        TRUE,
        TRUE
    ),
    (
        'ea161443-22d3-4439-84fa-fbd66ebdc302', -- para la muestra 2506/00002
        NULL,
        NULL,
        6.8,
        2,
        3,
        150.0,
        90,
        0.30,
        '2025-06-02',
        TRUE,
        FALSE
    ),
    (
        '2c6b58f9-c72a-42f8-911f-6501a9e4e75e', -- para la muestra 2506/00003
        25,
        5,
        6.5,
        NULL,
        NULL,
        NULL,
        NULL,
        0.10,
        '2025-06-02',
        TRUE,
        TRUE
    ),
    (
        '00223ad1-aa37-4b2f-8a93-183b5141e2d6', -- para la muestra 2506/00004
        NULL,
        NULL,
        7.4,
        1,
        2,
        110.0,
        85,
        0.20,
        '2025-06-02',
        FALSE,
        FALSE
    );