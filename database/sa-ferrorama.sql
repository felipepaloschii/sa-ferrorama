-- Remove o banco de dados anterior, caso exista
DROP DATABASE IF EXISTS sa_ferrorama_db;

-- Cria o banco de dados
CREATE DATABASE sa_ferrorama_db
CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;

-- Seleciona o banco de dados
USE sa_ferrorama_db;


-- Tabela de usuários
CREATE TABLE usuario (
    id_usuario INT PRIMARY KEY AUTO_INCREMENT,
    email VARCHAR(100) NOT NULL UNIQUE,
    senha VARCHAR(100) NOT NULL,
    cep VARCHAR(9) NULL,
    logradouro VARCHAR(150) NULL,
    bairro VARCHAR(100) NULL,
    cidade VARCHAR(100) NULL,
    uf CHAR(2) NULL,
    tipo ENUM('administrador', 'funcionario') NOT NULL DEFAULT 'funcionario'
);


-- Tabela de trens
CREATE TABLE trem (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nome_trem VARCHAR(100) NOT NULL,
    modelo_trem VARCHAR(100) NOT NULL,
    localizacao_i VARCHAR(150) NOT NULL,
    codigo VARCHAR(50) NOT NULL UNIQUE,
    capacidade DECIMAL(10,2) NOT NULL,
    ano INT NOT NULL,
    status ENUM('operacao', 'parado', 'manutencao', 'inativo') NOT NULL DEFAULT 'operacao',
    id_usuario INT NULL,

    CONSTRAINT fk_trem_usuario
        FOREIGN KEY (id_usuario)
        REFERENCES usuario(id_usuario)
        ON DELETE RESTRICT
        ON UPDATE CASCADE
);


-- Tabela de rotas
CREATE TABLE rotas (
    id_rota INT PRIMARY KEY AUTO_INCREMENT,
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


-- Tabela de sensores
CREATE TABLE sensor (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nome_sensor VARCHAR(100) NOT NULL,
    tipo_sensor VARCHAR(100) NOT NULL,
    localizacao VARCHAR(150) NOT NULL,
    unidade_medida VARCHAR(50) NOT NULL,
    limite_alerta DECIMAL(10,2) NOT NULL,
    status_inicial VARCHAR(50) NOT NULL,
    descricao TEXT NULL,
    id_rota INT NULL,

    CONSTRAINT fk_sensor_rota
        FOREIGN KEY (id_rota)
        REFERENCES rotas(id_rota)
        ON DELETE RESTRICT
        ON UPDATE CASCADE
);