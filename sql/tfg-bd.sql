drop database if exists lab;
create database lab;

use lab;

/* Muestras simples, sin relaciones y para probar el auto-increment custom */
/*
drop table if exists muestrastest;
create table muestrastest(
    id_muestra char(10) primary key,
    fecha date 
);
*/

/* Diseño inicial de la base de datos */
/* Usuarios de la aplicación */
drop table if exists usuarios;
drop table if exists clientes;
drop table if exists muestras;
drop table if exists usuclimue;
drop table if exists analisis;

-- Tabla de Usuarios con UUID
create table usuarios(
    id char(36) primary key,  -- Usamos UUID en formato CHAR(36)
    nombre varchar(50) not null,
    email varchar(50) not null unique,
    pass varchar(50) not null
);

-- Tabla de Clientes con UUID
create table clientes(
    id char(36) primary key,  -- Usamos UUID en formato CHAR(36)
    nombre varchar(50) not null,
    email varchar(50) not null unique,
    fecha_alta datetime not null
);

-- Tabla de Muestras
create table muestras(
    id char(10) primary key,  -- ID con formato 'yymm/nnnnn', siendo n el número de muestra
    fecha datetime not null,
    direccion varchar(50) not null,
    cloro decimal(3, 2) not null,
    tipo_analisis enum('TOTAL', 'FQ', 'MICRO') not null,
    enviado boolean default false,
    anotaciones text
);

-- Relación Usuarios-Clientes-Muestras con UUIDs
create table usuclimue(
    id_usuario char(36),
    id_cliente char(36),
    id_muestra char(10),
    fecha_creacion datetime default current_timestamp,
    primary key (id_usuario, id_cliente, id_muestra),
    constraint fk_usuclimue_usu_id foreign key (id_usuario) references usuarios(id) on update cascade on delete cascade,
    constraint fk_usuclimue_cli_id foreign key (id_cliente) references clientes(id) on update cascade on delete cascade,
    constraint fk_usuclimue_mue_id foreign key (id_muestra) references muestras(id) on update cascade on delete cascade
);

-- Tabla de Análisis (unificada) con UUIDs
create table analisis(
    id_muestra char(10) primary key,  
    coliformes tinyint unsigned,
    e_coli tinyint unsigned,
    pH decimal(2,1),
    turbidez int unsigned,
    color tinyint unsigned,
    conductividad float unsigned,
    dureza smallint unsigned,
    cloro decimal(3,2),
    completada boolean default false,
    constraint fk_analisis_mue_id foreign key (id_muestra) references muestras(id) on update cascade on delete cascade
);
