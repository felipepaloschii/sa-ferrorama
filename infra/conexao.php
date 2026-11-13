<?php

$conexao = new mysqli(
    "localhost",
    "root",
    "",
    "sa_ferrorama_db",
    
);

if ($conexao->connect_error) {
    die("Erro ao conectar ao banco de dados: " . $conexao->connect_error);
}

$conexao->set_charset("utf8mb4");

?>