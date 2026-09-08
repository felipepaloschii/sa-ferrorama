create database sa_ferrorama_db;
use sa_ferrorama_db;

create table usuario (
    id_usuario int primary key auto_increment,
    email varchar(100) not null unique,
    senha varchar(100) not null
);

create table sensor (

    id int primary key auto_increment,  
    nome_sensor varchar (100)  not null,
    tipo_sensor varchar (100) not null,
    localizacao varchar (150) not null,
    unidade_medida varchar (20) not null,
    limite_alerta decimal (10,2) not null,
    status_inicial text, 
    descricao text

);

