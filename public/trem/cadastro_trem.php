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

     if ($stmt) {


    $stmt->bind_param(
        "ssssdss",
        $nome_trem,
        $modelo_trem,
        $localizacao_i,
        $codigo,
        $capacidade,
        $ano,
        $status
    );
    
       if ($stmt->execute()) {
    $mensagem = 'Novo trem cadastrado com sucesso!';

   } else {
    $erro = 'Não foi possível cadastrar o trem';
   }

   $stmt->close();
    } else {
        $erro = 'Erro ao preparar o cadastro do trem.';
    }
}

?>

<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastrar Trem</title>
    <link rel="stylesheet" href="../../assets/style/style.css">
</head>

<body>
    <header class="barra-superior">
        <h1>PIA Enterprise</h1>