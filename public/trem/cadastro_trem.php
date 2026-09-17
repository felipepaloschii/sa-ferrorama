<?php

include '../../infra/conexao.php';

$mensagem = '';
$erro = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nome_trem = $_POST['nome_trem'];
    $modelo_trem = $_POST['modelo_trem'];
    $localizacao_i = $_POST['localizacao_i'];
    $codigo = $_POST['codigo'];
    $capacidade = $_POST['capacidade'];
    $ano = $_POST['ano'];
    $status = $_POST['status'];

    $sql = "INSERT INTO trem (nome_trem, modelo_trem, localizacao_i, codigo, capacidade, ano, status) VALUES (?, ?, ?, ?, ?, ?, ?)";

    $stmt = $conexao->prepare($sql);
