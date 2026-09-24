<?php

include'../../infra/conexao.php';

$mensagem = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $id_trem = $_POST['id_trem'];
    $nome_rota = $_POST['nome_rota'];
    $codigo = $_POST['codigo'];
    $distancia = $_POST['distancia'];
    $tempo_estimado = $_POST['tempo_estimado'];
    $capacidade_rota = $_POST['capacidade_rota'];

    $sql = "INSERT INTO rotas (id_trem, nome_rota, codigo, distancia, tempo_estimado, capacidade_rota) VALUES (?, ?, ?, ?, ?, ?)";

    $stmt = $conexao->prepare($sql);
    $stmt->bind_param("isssdd", $id_trem, $nome_rota, $codigo, $distancia, $tempo_estimado, $capacidade_rota);

    if ($stmt->execute() === TRUE) {

        header("Location: tela-home-rotas.php");
        exit;

    } else {

        $mensagem = "Erro ao cadastrar rota.";

    }
}

?><html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastrar rota</title>
    
    <link
        rel="stylesheet"
        href="../../assets/style/style.css"
    >
</head>

<body id="pagina-rota">

    <header class="barra-superior">

        <h1>PIA Enterprise</h1>

        <div class="acoes-superiores">

            <div class="aba-usuario">
                <a href="../usuario/tela-cadastro-user.php">
                    Meu perfil
                </a>

                <a href="../logout/logout.php">
                    Sair
                </a>

            </div>

        </div>

    </header>

    <aside class="menu-lateral">

        <nav>

            <a href="../../index.php">
                Dashboard
            </a>

            <a href="../sensor/tela-home-sensor.php">
                Sensores
            </a>

            <a href="../trem/lista_trem.php">
                Trens
            </a>

            <a
                class="ativo"
                href="tela-home-rotas.php"
            >
                Rotas
            </a>

        </nav>

    </aside>












































</html>