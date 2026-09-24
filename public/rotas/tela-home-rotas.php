<?php

include'../../infra/conexao.php';

$rotas = [];

$sql = "SELECT * FROM rotas ORDER BY id_rota DESC";

$resultado = $conexao->query($sql);

if ($resultado) {

    while ($rota = $resultado->fetch_assoc()) {

        $rotas[] = $rota;

    }
}


?>


<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tela de Sensor</title>
    <link rel="stylesheet" href="../../assets/style/style.css">

</head>
<body>
   <body>
    <header class="barra-superior" >
        <h1>PIA Enterprise</h1>
        

    </header>

    <aside class="menu-lateral">
        <nav>
            <a href="../../index.php">Dashboard</a>
            <a href="tela-home-sensor.php">Sensores</a>
            <a href="../trem/cadastro_trem.php">Trens</a>
            <a class="ativo" href="tela-home-rotas.php">Rotas</a>
        </nav>
    </aside>

    <main class="area-principal">
        <section class="cabecalho-sensor">
