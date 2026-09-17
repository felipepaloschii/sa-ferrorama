<?php

include '../../infra/conexao.php';

$mensagem = '';
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

    $nome_sensor = $_POST['nome_sensor'];
    $tipo_sensor = $_POST['tipo_sensor'];
    $localizacao = $_POST['localizacao'];
    $unidade_medida = $_POST['unidade_medida'];
    $limite_alerta = (float) $_POST['limite_alerta'];
    $status_inicial = $_POST['status_inicial'];
    $descricao = $_POST['descricao'];

    $sql = "UPDATE sensor SET
            nome_sensor = ?,
            tipo_sensor = ?,
            localizacao = ?,
            unidade_medida = ?,
            limite_alerta = ?,
            status_inicial = ?,
            descricao = ?
            WHERE id = ?";

    $stmt = $conexao->prepare($sql);

    if ($stmt) {

        $stmt->bind_param(
            "ssssdssi",
            $nome_sensor,
            $tipo_sensor,
            $localizacao,
            $unidade_medida,
            $limite_alerta,
            $status_inicial,
            $descricao,
            $id
        );

        if ($stmt->execute()) {

            $mensagem = 'Sensor atualizado com sucesso!';

            $sensor['nome_sensor'] = $nome_sensor;
            $sensor['tipo_sensor'] = $tipo_sensor;
            $sensor['localizacao'] = $localizacao;
            $sensor['unidade_medida'] = $unidade_medida;
            $sensor['limite_alerta'] = $limite_alerta;
            $sensor['status_inicial'] = $status_inicial;
            $sensor['descricao'] = $descricao;

        } else {

            $erro = 'Não foi possível atualizar o sensor.';

        }

        $stmt->close();

    } else {

        $erro = 'Erro ao preparar a atualização do sensor.';

    }
}
?>

<html lang="pt-br">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Editar Sensor</title>

    <link rel="stylesheet" href="../../assets/style/style.css">

</head>


<body>


    <header class ="barra-superior">

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
                    Editar os sensores
                </h1>

                <p class="descricao-pagina">
                    Preencha as informações para editar um sensor existente no sistema.
                </p>

            </div>


            <button
                class="botao-voltar-sensores"
                onclick="window.location.href='tela-home-sensor.php'">

                ← Voltar para sensores

            </button>


        </section>


        <?php if ($mensagem != ''): ?>

            <p>
                <?php echo $mensagem; ?>
            </p>

        <?php endif; ?>


        <?php if ($erro != ''): ?>

            <p>
                <?php echo $erro; ?>
            </p>

        <?php endif; ?>


        <section class="painel-cadastro-sensor">


            <form
                class="formulario-cadastro-sensor"
                method="POST"
                action="">


                <div class="linha-campos-formulario">


                    <div class="grupo-nome_sensor-sensor">

                        <label class="nome_sensor-sensor">
                            Nome do Sensor *
                        </label>

                    </div>


                    <input
                        type="text"
                        name="nome_sensor"
                        class="input-nome_sensorsensor"
                        value="<?php echo htmlspecialchars($sensor['nome_sensor']); ?>"
                        required>


                    <div class="grupo-tipo-sensor">

                        <label class="tipo-sensor">
                            Tipo de sensor *
                        </label>

                        <input
                            type="text"
                            name="tipo_sensor"
                            class="select-tipo-sensor"
                            value="<?php echo htmlspecialchars($sensor['tipo_sensor']); ?>"
                            required>

                    </div>


                </div>


                <div class="linha-campos-formulario">


                    <div class="grupo-localizacao-sensor">

                        <label class="localizacao-sensor">
                            Localização *
                        </label>

                        <input
                            type="text"
                            name="localizacao"
                            class="input-localizacao-sensor"
                            value="<?php echo htmlspecialchars($sensor['localizacao']); ?>"
                            required>

                    </div>


                    <div class="grupo-unidade-medida">

                        <label class="unidade-medida">
                            Unidade de medida *
                        </label>

                        <input
                            type="text"
                            name="unidade_medida"
                            class="select-unidade-medida"
                            value="<?php echo htmlspecialchars($sensor['unidade_medida']); ?>"
                            required>

                    </div>


                    <div class="grupo-limite-alerta">

                        <label class="limite-alerta">
                            Limite de Alerta
                        </label>

                        <input
                            type="number"
                            step="0.01"
                            name="limite_alerta"
                            class="input-limite-alerta"
                            value="<?php echo htmlspecialchars($sensor['limite_alerta']); ?>">

                    </div>


                </div>


                <div class="linha-status-e-descricao">


                    <div class="grupo-status-sensor">


                        <label class="status-sensor">
                            Status Inicial *
                        </label>


                        <div class="opcoes-status-sensor">


                            <label class="opcao-status-ativo">

                                <input
                                    type="radio"
                                    name="status_inicial"
                                    value="Ativo"
                                    <?php echo $sensor['status_inicial'] == 'Ativo' ? 'checked' : ''; ?>>

                                Ativo

                            </label>


                            <label class="opcao-status-inativo">

                                <input
                                    type="radio"
                                    name="status_inicial"
                                    value="Inativo"
                                    <?php echo $sensor['status_inicial'] == 'Inativo' ? 'checked' : ''; ?>>

                                Inativo

                            </label>


                        </div>


                    </div>


                    <div class="grupo-descricao-sensor">


                        <label class="descricao-sensor">
                            Descrição
                        </label>


                        <textarea
                            name="descricao"
                            class="textarea-descricao-sensor"><?php echo htmlspecialchars($sensor['descricao']); ?></textarea>


                    </div>


                </div>


                <div class="area-botoes-formulario">


                    <button
                        type="submit"
                        class="botao-salvar-sensor">

                        Salvar alterações

                    </button>


                    <button
                        type="button"
                        class="botao-cancelar-cadastro"
                        onclick="window.location.href='tela-home-sensor.php'">

                        Cancelar

                    </button>


                </div>


            </form>


        </section>


    </main>


</body>

</html>