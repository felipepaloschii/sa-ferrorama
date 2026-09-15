CREATE DATABASE sa_ferrorama_db;
USE sa_ferrorama_db;

CREATE TABLE usuario (
    id_usuario int primary key auto_increment,
    email varchar(100) not null unique,
    senha varchar(100) not null
);

CREATE TABLE sensor (

    id int primary key auto_increment,  
    nome_sensor varchar (100)  not null,
    tipo_sensor varchar (100) not null,
    localizacao varchar (150) not null,
    unidade_medida varchar (20) not null,
    limite_alerta decimal (10,2) not null,
    status_inicial text, 
    descricao text

);

CREATE TABLE IF NOT EXISTS trens (
    id INT AUTO_INCREMENT PRIMARY KEY,
    codigo VARCHAR(30) NOT NULL UNIQUE,
    modelo VARCHAR(100) NOT NULL,
    status ENUM('operacao', 'parado', 'manutencao', 'inativo') NOT NULL DEFAULT 'parado',
    cadastrado_em DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) 

