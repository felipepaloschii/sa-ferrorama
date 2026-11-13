
DROP DATABASE IF EXISTS sa_ferrorama_db;

CREATE DATABASE sa_ferrorama_db
CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;

USE sa_ferrorama_db;

CREATE TABLE usuario (
    id_usuario INT PRIMARY KEY AUTO_INCREMENT,
    email VARCHAR(100) NOT NULL UNIQUE,
    senha VARCHAR(100) NOT NULL,
    tipo ENUM('administrador', 'funcionario')
        NOT NULL DEFAULT 'funcionario'
);

CREATE TABLE trem (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome_trem VARCHAR(100) NOT NULL,
    modelo_trem VARCHAR(100) NOT NULL,
    localizacao_i VARCHAR(150) NOT NULL,
    codigo VARCHAR(30) NOT NULL UNIQUE,
    capacidade DECIMAL(10,2) NOT NULL,
    ano VARCHAR(4) NOT NULL,
    status ENUM('operacao', 'parado', 'manutencao', 'inativo')
        NOT NULL DEFAULT 'parado',
    id_usuario INT NULL,

    CONSTRAINT fk_trem_usuario
        FOREIGN KEY (id_usuario)
        REFERENCES usuario(id_usuario)
        ON DELETE SET NULL
        ON UPDATE CASCADE
);

CREATE TABLE rotas (
    id_rota INT AUTO_INCREMENT PRIMARY KEY,
    nome_rota VARCHAR(100) NOT NULL,
    modelo_trem VARCHAR(100) NOT NULL,
    tempo_estimado VARCHAR(50) NOT NULL,
    codigo VARCHAR(50) NOT NULL UNIQUE,
    distancia DECIMAL(10,2) NOT NULL,
    status ENUM('Ativa', 'Inativa') NOT NULL DEFAULT 'Ativa',
    id_trem INT NULL,

    CONSTRAINT fk_rota_trem
        FOREIGN KEY (id_trem)
        REFERENCES trem(id)
        ON DELETE RESTRICT
        ON UPDATE CASCADE
);

CREATE TABLE sensor (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nome_sensor VARCHAR(100) NOT NULL,
    tipo_sensor VARCHAR(100) NOT NULL,
    localizacao VARCHAR(150) NOT NULL,
    id_rota INT NULL,
    unidade_medida VARCHAR(20) NOT NULL,
    limite_alerta DECIMAL(10,2) NOT NULL,
    status_inicial TEXT,
    descricao TEXT,

    CONSTRAINT fk_sensor_rota
        FOREIGN KEY (id_rota)
        REFERENCES rotas(id_rota)
        ON DELETE RESTRICT
        ON UPDATE CASCADE
);
