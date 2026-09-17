<?php

include '../../infra/conexao.php';

$erro = '';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: tela-home-sensor.php');
    exit;
}

$id = (int) $_GET['id'];

$sql = "SELECT * FROM sensor WHERE id = ?";

$stmt = $conexao->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();

$resultado = $stmt->get_result();

if ($resultado->num_rows == 0) {
    header('Location: tela-home-sensor.php');
    exit;
}

$sensor = $resultado->fetch_assoc();

$stmt->close();


if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $sql = "DELETE FROM sensor WHERE id = ?";

    $stmt = $conexao->prepare($sql);

    if ($stmt) {

        $stmt->bind_param("i", $id);

        if ($stmt->execute()) {

            header('Location: tela-home-sensor.php');
            exit;

        } else {

            $erro = 'Não foi possível excluir o sensor.';

        }

        $stmt->close();

    } else {

        $erro = 'Erro ao preparar a exclusão do sensor.';

    }
}

?>

<html lang="pt-br">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Excluir Sensor</title>

    <link rel="stylesheet" href="../../assets/style/style.css">

</head>

<body>

    <header class="barra-superior">

        <h1>PIA Enterprise</h1>

        <div class="acoes-superiores">

            <span class="ponto-online"></span>

            <span class="texto-online">
                Online
            </span>

            <span class="icone-sino"></span>

            <span class="icone-usuario"></span>

        </div>

    </header>


    <aside class="menu-lateral">

        <nav>

            <a href="../../index.php">
                Dashboard
            </a>

            <a class="ativo" href="tela-home-sensor.php">
                Sensores
            </a>

            <a href="../../public/trem/cadastro_trem.php">
                Trens
            </a>

            <a href="#">
                Rotas
            </a>

        </nav>

    </aside>


    <main class="area-principal">

        <section class="cabecalho-cadastro-sensor">

            <div class="informacoes-pagina">

                <h1 class="titulo-pagina">
                    Excluir sensor
                </h1>

                <p class="descricao-pagina">
                    Confirme a exclusão do sensor selecionado.
                </p>

            </div>


            <button
                class="botao-voltar-sensores"
                onclick="window.location.href='tela-home-sensor.php'">

                ← Voltar para sensores

            </button>

        </section>


        <section class="painel-excluir-sensor">

            <div class="icone-excluir-sensor">
                🗑
            </div>


            <h2>
                Tem certeza que deseja excluir este sensor?
            </h2>


            <p>
                Essa ação não poderá ser desfeita.
            </p>


            <div class="informacoes-sensor-excluir">

                <span>
                    Nome: <?php echo htmlspecialchars($sensor['nome_sensor']); ?>
                </span>

                <span>
                    Tipo: <?php echo htmlspecialchars($sensor['tipo_sensor']); ?>
                </span>

                <span>
                    Localização: <?php echo htmlspecialchars($sensor['localizacao']); ?>
                </span>

                <span>
                    Status: <?php echo htmlspecialchars($sensor['status_inicial']); ?>
                </span>

            </div>


            <?php if ($erro != ''): ?>

                <p class="erro-excluir-sensor">
                    <?php echo $erro; ?>
                </p>

            <?php endif; ?>


            <form method="POST" class="botoes-excluir-sensor">

                <button
                    type="submit"
                    class="botao-confirmar-exclusao">

                    Excluir Sensor

                </button>


                <button
                    type="button"
                    class="botao-cancelar-cadastro"
                    onclick="window.location.href='tela-home-sensor.php'">

                    Cancelar

                </button>

            </form>

        </section>

    </main>

</body>

</html>