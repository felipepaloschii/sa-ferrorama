create database sa_ferrorama_db;
use sa_ferrorama_db;

create table usuario (
    id_usuario int primary key auto_increment,
    email varchar(100) not null unique,
    senha varchar(100) not null
)

create table sensor (

    id int auto_increment primary key,
    
)

