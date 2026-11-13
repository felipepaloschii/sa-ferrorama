
CREATE DATABASE IF NOT EXISTS sa_ferrorama_db;
USE sa_ferrorama_db;

CREATE TABLE usuario (
    id_usuario INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(150) NOT NULL UNIQUE,
    senha VARCHAR(255) NOT NULL,
    tipo ENUM('administrador', 'funcionario')
        DEFAULT 'funcionario'
);

CREATE TABLE trem (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome_trem VARCHAR(100) NOT NULL,
    modelo_trem VARCHAR(100) NOT NULL,
    localizacao_i VARCHAR(150) NOT NULL,
    codigo VARCHAR(30) NOT NULL UNIQUE,
    capacidade DECIMAL(10,2) NOT NULL,
    ano YEAR NOT NULL,
    status ENUM('operacao', 'parado', 'manutencao', 'inativo')
        DEFAULT 'parado',
    id_usuario INT NULL,

    FOREIGN KEY (id_usuario)
        REFERENCES usuario(id_usuario)
        ON DELETE RESTRICT
        ON UPDATE CASCADE
);

CREATE TABLE rotas (
    id_rota INT AUTO_INCREMENT PRIMARY KEY,
    nome_rota VARCHAR(150) NOT NULL,
    modelo_trem VARCHAR(100) NULL,
    tempo_estimado VARCHAR(60) NOT NULL,
    codigo VARCHAR(30) NOT NULL UNIQUE,
    distancia DECIMAL(10,2) NOT NULL,
    status ENUM('Ativa', 'Inativa') DEFAULT 'Ativa',
    id_trem INT NULL,

    FOREIGN KEY (id_trem)
        REFERENCES trem(id)
        ON DELETE RESTRICT
        ON UPDATE CASCADE
);

CREATE TABLE sensor (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome_sensor VARCHAR(100) NOT NULL,
    tipo_sensor VARCHAR(60) NOT NULL,
    localizacao VARCHAR(150) NOT NULL,
    id_rota INT NOT NULL,
    unidade_medida VARCHAR(20) NOT NULL,
    limite_alerta DECIMAL(10,2) NULL,
    status_inicial ENUM('Ativo', 'Inativo') DEFAULT 'Ativo',
    descricao TEXT NULL,

    FOREIGN KEY (id_rota)
        REFERENCES rotas(id_rota)
        ON DELETE RESTRICT
        ON UPDATE CASCADE
);
