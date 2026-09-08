<?php

$host = "localhost";
$user = "root";
$password = "";
$database = "sa_ferrorama_db";

$conexao = new mysqli($host, $user, $password, $database);

if ($conexao->connect_error) {
    die("Falha na conexão: " . $conexao->connect_error);
}

?>