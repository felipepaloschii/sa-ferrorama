
<?php

// inclui o arquivo que verifica se o usuario esta conectado e e administrador
require_once '../../infra/verificar-admin.php';

// inclui o arquivo de conexao com o banco de dados
require_once '../../infra/conexao.php';

// cria uma variavel vazia pra armazenar mensagens de erro
$erro = '';

// filter_input recebe e valida um dado enviado pelo formulario ou pela URL
// INPUT_GET indica que o dado sera recebido pela URL
// FILTER_VALIDATE_INT verifica se o valor e um numero inteiro valido
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

// verifica se o id nao existe ou se e menor que 1
if (!$id || $id < 1) {

    // redireciona pra listagem de sensores caso o id seja invalido
    header('Location: tela-home-sensor.php');

    // encerra a execucao depois do redirecionamento
    exit;
}

// prepara a consulta pra buscar o sensor e os dados da rota associada
$stmt = $conexao->prepare(
    "SELECT sensor.*, rotas.nome_rota, rotas.codigo AS codigo_rota
     FROM sensor
     LEFT JOIN rotas ON sensor.id_rota = rotas.id_rota
     WHERE sensor.id = ?"
);

// associa o id ao ? da consulta
// i indica que o valor enviado e um numero inteiro
$stmt->bind_param("i", $id);

// executa a consulta no banco de dados
$stmt->execute();

// get_result recebe os registros encontrados pela consulta
$resultado = $stmt->get_result();

// fetch_assoc transforma o registro encontrado em um array associativo
$sensor = $resultado->fetch_assoc();

// close encerra a consulta e libera os recursos utilizados
$stmt->close();

// verifica se o sensor nao foi encontrado
if (!$sensor) {

    // volta pra listagem caso o sensor nao exista
    header('Location: tela-home-sensor.php');

    // encerra a execucao
    exit;
}

// verifica se o formulario de confirmacao foi enviado pelo metodo POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // prepara uma nova consulta pra verificar se o sensor ainda existe e possui rota
    $check = $conexao->prepare(
        "SELECT id_rota FROM sensor WHERE id = ?"
    );

    // associa o id do sensor ao ? da consulta
    $check->bind_param("i", $id);

    // executa a verificacao no banco
    $check->execute();

    // recebe o resultado da verificacao
    $resultadoCheck = $check->get_result();

    // transforma o resultado em um array associativo
    $sensorAtual = $resultadoCheck->fetch_assoc();

    // encerra a consulta de verificacao
    $check->close();

    // verifica se o sensor nao foi encontrado
    if (!$sensorAtual) {

        // volta pra listagem caso o sensor nao exista mais
        header('Location: tela-home-sensor.php');
        exit;

    // verifica se o sensor possui uma rota associada
    // !== compara se o valor e diferente de null
    } elseif ($sensorAtual['id_rota'] !== null) {

        // impede a exclusao porque o sensor esta relacionado a uma rota
        $erro = 'Não é possível excluir este sensor, pois ele está associado a uma rota.';

    } else {

        // prepara o comando pra excluir o sensor pelo id
        $stmt = $conexao->prepare(
            "DELETE FROM sensor WHERE id = ?"
        );

        // verifica se o comando foi preparado corretamente
        if ($stmt) {

            // associa o id ao ? da consulta
            $stmt->bind_param("i", $id);

            // execute executa a exclusao no banco
            // affected_rows informa quantas linhas foram alteradas
            // > 0 verifica se pelo menos um registro foi excluido
            if ($stmt->execute() && $stmt->affected_rows > 0) {

                // encerra a consulta e libera os recursos
                $stmt->close();

                // redireciona pra listagem depois da exclusao
                header('Location: tela-home-sensor.php');

                // encerra a execucao
                exit;

            } else {

                // informa que nao foi possivel excluir o sensor
                $erro = 'Não foi possível excluir o sensor.';
            }

            // encerra a consulta depois da tentativa de exclusao
            $stmt->close();

        } else {

            // informa que ocorreu um erro ao preparar a exclusao
            $erro = 'Erro ao preparar a exclusão do sensor.';
        }
    }
}

?>


<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Excluir Sensor</title>
    <link rel="stylesheet" href="../../assets/style/style.css">
</head>

<body>

<header class="barra-superior">
    <h1>PIA Enterprise</h1>

    <aside class="menu-lateral">
        <nav>
            <a href="../../index.php">Dashboard</a>
            <a class="ativo" href="tela-home-sensor.php">Sensores</a>
            <a href="../trem/lista_trem.php">Trens</a>
            <a href="../rotas/tela-home-rotas.php">Rotas</a>

            <?php if ($_SESSION['tipo'] === 'administrador'): ?>
                <a href="../usuario/tela-home-usuario.php">Usuários</a>
            <?php endif; ?>
        </nav>
    </aside>
</header>

<main class="area-principal">

    <section class="cabecalho-cadastro-sensor">

        <div class="informacoes-pagina">
            <h1 class="titulo-pagina">Excluir sensor</h1>

            <p class="descricao-sensores">
                Confirme a exclusão do sensor selecionado.
            </p>
        </div>

        <a href="tela-home-sensor.php" class="botao-voltar-sensores">
            Voltar para sensores
        </a>

    </section>

    <section class="painel-excluir-sensor">

    

        <h2>
            Tem certeza que deseja excluir este sensor?
        </h2>

        <p>
            Essa ação não poderá ser desfeita.
        </p>

        <div class="informacoes-sensor-excluir">

            <span>
                Nome: <?= e($sensor['nome_sensor']) ?>
            </span>
            <br>

            <span>
                Tipo: <?= e($sensor['tipo_sensor']) ?>
            </span>
            <br>

            <span>
                Localização: <?= e($sensor['localizacao']) ?>
            </span>
            <br>

            <span>
                Rota associada:

                <?php if (!empty($sensor['id_rota'])): ?>
                    <?= e($sensor['nome_rota']) ?>
                    (<?= e($sensor['codigo_rota']) ?>)
                <?php else: ?>
                    Sem rota associada
                <?php endif; ?>
            </span>
            <br>

            <span>
                Status: <?= e($sensor['status_inicial']) ?>
            </span>

        </div>

        <?php if ($erro !== ''): ?>
            <p class="erro-excluir-sensor">
                <?= e($erro) ?>
            </p>
        <?php endif; ?>

        <form method="POST" class="botoes-excluir-sensor">

            <button type="submit" class="botao-confirmar-exclusao">
                Excluir sensor
            </button>

            <a href="tela-home-sensor.php" class="botao-cancelar-cadastro">
                Cancelar
            </a>

        </form>

    </section>

</main>

</body>
</html>
