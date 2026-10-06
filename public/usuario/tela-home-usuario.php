<?php

include '../../infra/conexao.php';

$quantidade_usuarios = 0;
$usuarios = [];

$sql = "SELECT id_usuario, email, senha FROM usuarios ORDER BY id_usuario DESC";

$resultado = $conexao->query($sql);

if ($resultado) {

while ($usuario = $resultado->fetch_assoc()) {
    $usuarios[] = $usuario;
    $quantidade_usuarios++;
}

}

?>

<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tela de Usuários</title>
    <link rel="stylesheet" href="../../assets/style/style.css">

</head>
<body>

    <header class="barra-superior">
        <h1>PIA Enterprise</h1>

    </header>

    <aside class="menu-lateral">
        <nav>
            <a href="../../index.php">Dashboard</a>
            <a href="tela-home-sensor.php">Sensores</a>
            <a href="../trem/cadastro_trem.php">Trens</a>
            <a href="../rotas/tela-home-rotas.php">Rotas</a>
            <a class="ativo" href="tela-home-usuario.php">Usuários</a>
        </nav>
    </aside>

    <main class="area-principal">
        <section class="cabecalho-sensor">]

        <div>

        <h1 class="titulo-sensor">Usuários</h1>
        <p class="descricao-sensor">Gerencie os usuários cadastrados no sistema</p>
        </div>

        <section class= "conteudo-lista-sensor">
        <div class="topo-lista-sensores">
            <div class="cartao-sensores-ativos">
                <div class ="icone-usuario">

                        <span></span>
                        <span></span>
                        <span></span>
                        
                </div>
</body>
</html>