drop database if exists lab;
create database lab;

use lab;

/* Muestras simples, sin relaciones y para probar el auto-increment custom */
/*
drop table if exists muestrastest;
create table muestrastest(
    id_muestra char(8) primary key,
    fecha date 
);
*/

/* Diseño inicial de la base de datos */
/* Usuarios de la aplicación */
drop table if exists usuarios;
create table usuarios(
    id int unsigned primary key auto_increment,
    nombre varchar(50) not null,
    email varchar(50) not null unique,
    pass varchar(50) not null
);

/* Clientes solicitantes de muestras */
drop table if exists clientes;
create table clientes(
    id int unsigned primary key auto_increment,
    nombre varchar(50) not null,
    email varchar(50) not null unique,
    pass varchar(50) not null
);

/* Muestras solicitadas */
drop table if exists muestras;
create table muestras(
    id char(8) primary key,
    fecha date not null,
    direccion varchar(50) not null,
    cloro decimal(3, 2) not null,
    tipo_analisis enum ["TOTAL", "FQ", "MICRO"],
    enviado boolean default false,
    anotaciones varchar(250)
);

/* Relación Usuarios-Clientes-Muestras */
drop table if exists usuclimue;
create table usuclimue(
    id_usuario int unsigned,
    id_cliente int unsigned,
    id_muestra char(8),
    primary key (id_usuario, id_cliente, id_muestra),
    constraint fk_usuclimue_usu_id foreign key (id_usuario) references usuarios(id) on update cascade on delete cascade,
    constraint fk_usuclimue_cli_id foreign key (id_cliente) references clientes(id) on update cascade on delete cascade,
    constraint fk_usuclimue_mue_id foreign key (id_muestra) references muestras(id) on update cascade on delete cascade
);

/* Tablas de análisis */
drop table if exists analisismicro;
create table analisismicro(
    id_muestra char(8) primary key,
    coliformes tinyint unsigned,
    e-coli tinyint unsigned,
    lista boolean default false,
    constraint fk_analisismicro_mue_id foreign key (id_muestra) references mustras(id) on update cascade on delete cascade
);

drop table if exists analisisfq;
create table analisisfq(
    id_muestra char(8) primary key,
    pH decimal(2, 1),
    turbidez int unsigned,
    color tinyint unsigned,
    conductividad float unsigned,
    dureza smallint unsigned,
    cloro decimal(3, 2),
    lista boolean default false,
    constraint fk_analisisfq_mue_id foreign key (id_muestra) references mustras(id) on update cascade on delete cascade
);

drop table if exists analisistotal;
create table analisistotal(
    id_muestra char(8) primary key,
    coliformes tinyint unsigned,
    e-coli tinyint unsigned,
    pH decimal(2, 1),
    turbidez int unsigned,
    color tinyint unsigned,
    conductividad float unsigned,
    dureza smallint unsigned,
    cloro decimal(3, 2),
    lista boolean default false,
    constraint fk_analisistotal_mue_id foreign key (id_muestra) references mustras(id) on update cascade on delete cascade
);