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
    
    CREATE TABLE IF NOT EXISTS trem (
        id INT AUTO_INCREMENT PRIMARY KEY,
        nome_trem VARCHAR(100) NOT NULL,
        modelo_trem VARCHAR(100) NOT NULL,
        localizacao_i VARCHAR(150) NOT NULL,
        codigo VARCHAR(30) NOT NULL UNIQUE,
        capacidade DECIMAL(10,2) NOT NULL,
        ano VARCHAR(4) NOT NULL,
        status ENUM('operacao', 'parado', 'manutencao', 'inativo') NOT NULL DEFAULT 'parado'
    );

    create table if not exists rotas (
        id_rota int primary key auto_increment,
        id_trem int not null,
        nome_rota varchar(100) not null,
        codigo varchar(100) not null,
        distancia decimal(10,2) not null,
        tempo_estimado decimal(10,2) not null,
        capacidade_rota decimal(10,2) not null,
    );
