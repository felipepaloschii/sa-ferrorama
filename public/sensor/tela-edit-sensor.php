

<?php

// inclui o arquivo que verifica se o usuario esta conectado e e administrador
require_once '../../infra/verificar-admin.php';

// inclui o arquivo de conexao com o banco de dados
require_once '../../infra/conexao.php';

// cria variaveis vazias pra armazenar mensagens
$mensagem = '';
$erro = '';

// cria um array vazio pra armazenar as rotas disponiveis
$rotas = [];

// filter_input recebe e valida um dado enviado pela URL
// INPUT_GET indica que o dado vem pela URL
// FILTER_VALIDATE_INT verifica se o valor e um numero inteiro valido
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

// verifica se o id nao existe ou se e menor que 1
if (!$id || $id < 1) {

    // redireciona pra listagem de sensores caso o id seja invalido
    header('Location: tela-home-sensor.php');

    // encerra a execucao depois do redirecionamento
    exit;
}

// cria a consulta pra buscar as rotas cadastradas
// SELECT escolhe as colunas que serao consultadas
// ORDER BY organiza as rotas pelo nome em ordem alfabetica
$sqlRotas = "SELECT id_rota, nome_rota, codigo
             FROM rotas
             ORDER BY nome_rota";

// query executa a consulta no banco de dados
$resultadoRotas = $conexao->query($sqlRotas);

// verifica se a consulta foi executada corretamente
if ($resultadoRotas) {

    // fetch_all busca todos os registros encontrados
    // MYSQLI_ASSOC transforma cada registro em um array associativo
    $rotas = $resultadoRotas->fetch_all(MYSQLI_ASSOC);
}

// prepara a consulta pra buscar o sensor pelo id
// o ? sera substituido pelo valor enviado no bind_param
$stmt = $conexao->prepare(
    "SELECT * FROM sensor WHERE id = ?"
);

// associa o id ao ? da consulta
// i indica que o valor e um numero inteiro
$stmt->bind_param("i", $id);

// executa a consulta no banco de dados
$stmt->execute();

// recebe os registros encontrados pela consulta
$resultado = $stmt->get_result();

// transforma o registro encontrado em um array associativo
$sensor = $resultado->fetch_assoc();

// encerra a consulta e libera os recursos utilizados
$stmt->close();

// verifica se o sensor nao foi encontrado
if (!$sensor) {

    // volta pra listagem caso o sensor nao exista
    header('Location: tela-home-sensor.php');

    // encerra a execucao
    exit;
}

// verifica se o formulario foi enviado pelo metodo POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // recebe o nome do sensor e remove espacos do inicio e do final
    // ?? utiliza uma string vazia caso o campo nao exista ou seja null
    $nome_sensor = trim($_POST['nome_sensor'] ?? '');

    // recebe o tipo do sensor e remove espacos extras
    $tipo_sensor = trim($_POST['tipo_sensor'] ?? '');

    // recebe a localizacao e remove espacos extras
    $localizacao = trim($_POST['localizacao'] ?? '');

    // verifica se o campo da rota foi preenchido
    // se estiver vazio, guarda null pra permitir sensor sem rota
    // se tiver valor, filter_var verifica se e um numero inteiro valido
    $id_rota = ($_POST['id_rota'] ?? '') !== ''
        ? filter_var($_POST['id_rota'], FILTER_VALIDATE_INT)
        : null;

    // recebe a unidade de medida e remove espacos extras
    $unidade_medida = trim($_POST['unidade_medida'] ?? '');

    // recebe o limite de alerta informado no formulario
    $limite_alerta = $_POST['limite_alerta'] ?? '';

    // recebe o status inicial do sensor
    $status_inicial = $_POST['status_inicial'] ?? '';

    // recebe a descricao e remove espacos extras
    $descricao = trim($_POST['descricao'] ?? '');

    // cria um array com os tipos de sensor permitidos no cadastro
    $tiposPermitidos = [
        'temperatura',
        'velocidade',
        'vibração',
        'localização'
    ];

    // considera a rota valida inicialmente
    $rotaExiste = true;

    // verifica a rota somente quando uma foi selecionada
    // !== compara se o valor e diferente de null
    if ($id_rota !== null) {

        // verifica se o id da rota e invalido ou menor ou igual a zero
        if ($id_rota === false || $id_rota <= 0) {

            // informa que a rota selecionada nao e valida
            $rotaExiste = false;

        } else {

            // prepara uma consulta pra verificar se a rota existe no banco
            $consultaRota = $conexao->prepare(
                "SELECT id_rota FROM rotas WHERE id_rota = ?"
            );

            // verifica se a consulta foi preparada corretamente
            if ($consultaRota) {

                // associa o id da rota ao ? da consulta
                // i indica que o valor e um numero inteiro
                $consultaRota->bind_param("i", $id_rota);

                // executa a consulta no banco de dados
                $consultaRota->execute();

                // recebe os registros encontrados
                $resultadoRota = $consultaRota->get_result();

                // num_rows informa quantas linhas foram encontradas
                // > 0 verifica se a rota existe
                $rotaExiste = $resultadoRota->num_rows > 0;

                // encerra a consulta e libera os recursos
                $consultaRota->close();

            } else {

                // considera a rota invalida caso a consulta falhe
                $rotaExiste = false;
            }
        }
    }

    // verifica se os campos obrigatorios foram preenchidos corretamente
    // || significa OU, entao qualquer condicao verdadeira gera um erro
    // in_array verifica se o tipo e o status estao nas listas permitidas
    // o true faz a comparacao considerando tambem o tipo do valor
    // is_numeric verifica se o limite informado e um numero
    if (
        $nome_sensor === '' ||
        !in_array($tipo_sensor, $tiposPermitidos, true) ||
        $localizacao === '' ||
        !$rotaExiste ||
        $unidade_medida === '' ||
        !is_numeric($limite_alerta) ||
        !in_array($status_inicial, ['Ativo', 'Inativo'], true)
    ) {

        // informa que existem campos vazios ou preenchidos incorretamente
        $erro = 'Preencha todos os campos obrigatórios corretamente.';

    } else {

        // (float) transforma o limite de alerta em um numero decimal
        $limite_alerta = (float) $limite_alerta;

        // cria o comando pra atualizar os dados do sensor
        // cada ? sera substituido pelos valores enviados no bind_param
        $sql = "UPDATE sensor SET
                    nome_sensor = ?,
                    tipo_sensor = ?,
                    localizacao = ?,
                    id_rota = ?,
                    unidade_medida = ?,
                    limite_alerta = ?,
                    status_inicial = ?,
                    descricao = ?
                WHERE id = ?";

        // prepara a consulta de atualizacao
        $stmt = $conexao->prepare($sql);

        // verifica se a consulta foi preparada corretamente
        if ($stmt) {

            // associa os valores aos ? da consulta
            // s representa string
            // i representa numero inteiro
            // d representa numero decimal
            // a ordem precisa acompanhar os campos do UPDATE
            $stmt->bind_param(
                "sssisdssi",
                $nome_sensor,
                $tipo_sensor,
                $localizacao,
                $id_rota,
                $unidade_medida,
                $limite_alerta,
                $status_inicial,
                $descricao,
                $id
            );

            // executa a atualizacao no banco de dados
            if ($stmt->execute()) {

                // encerra a consulta e libera os recursos
                $stmt->close();

                // redireciona pra listagem depois de atualizar o sensor
                header('Location: tela-home-sensor.php');

                // encerra a execucao
                exit;

            } else {

                // informa que nao foi possivel atualizar o sensor
                $erro = 'Não foi possível atualizar o sensor.';
            }

            // encerra a consulta depois da tentativa de atualizacao
            $stmt->close();

        } else {

            // informa que ocorreu um erro ao preparar a atualizacao
            $erro = 'Erro ao preparar a atualização do sensor.';
        }
    }

    // mantem os valores preenchidos no formulario caso ocorra algum erro
    $sensor['nome_sensor'] = $nome_sensor;
    $sensor['tipo_sensor'] = $tipo_sensor;
    $sensor['localizacao'] = $localizacao;
    $sensor['id_rota'] = $id_rota;
    $sensor['unidade_medida'] = $unidade_medida;
    $sensor['limite_alerta'] = $limite_alerta;
    $sensor['status_inicial'] = $status_inicial;
    $sensor['descricao'] = $descricao;
}

?>


<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Sensor</title>
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
            <h1 class="titulo-pagina">Editar sensor</h1>

            <p class="descricao-pagina">
                Altere as informações do sensor cadastrado.
            </p>
        </div>

        <a href="tela-home-sensor.php" class="botao-voltar-sensores">
            Voltar para sensores
        </a>

    </section>

    <?php if ($erro !== ''): ?>
        <div class="erro-excluir-sensor">
            <?= e($erro) ?>
        </div>
    <?php endif; ?>

    <section class="painel-cadastro-sensor">

        <form class="formulario-cadastro-sensor" method="POST">

            <div class="grade-formulario-trem">

                <div class="grupo-campo-trem">
                    <label class="rotulo-campo-trem" for="nome_sensor">
                        Nome do sensor<span class="obrigatorio">*</span>
                    </label>

                    <input
                        type="text"
                        id="nome_sensor"
                        name="nome_sensor"
                        class="campo-edicao-trem"
                        value="<?= e($sensor['nome_sensor']) ?>"
                        required>
                </div>

                <div class="grupo-campo-trem">
                    <label class="rotulo-campo-trem" for="tipo_sensor">
                        Tipo do sensor<span class="obrigatorio">*</span>
                    </label>

                    <select
                        id="tipo_sensor"
                        name="tipo_sensor"
                        class="campo-edicao-trem"
                        required>

                        <option value="">Selecione</option>

                        <?php
                        $tipos = [
                            'temperatura' => 'Temperatura',
                            'velocidade' => 'Velocidade',
                            'vibração' => 'Vibração',
                            'localização' => 'Localização'
                        ];
                        ?>

                        <?php foreach ($tipos as $valor => $nome): ?>
                            <option
                                value="<?= e($valor) ?>"
                                <?= $sensor['tipo_sensor'] === $valor ? 'selected' : '' ?>>
                                <?= e($nome) ?>
                            </option>
                        <?php endforeach; ?>

                    </select>
                </div>

                <div class="grupo-campo-trem">
                    <label class="rotulo-campo-trem" for="localizacao">
                        Localização<span class="obrigatorio">*</span>
                    </label>

                    <input
                        type="text"
                        id="localizacao"
                        name="localizacao"
                        class="campo-edicao-trem"
                        value="<?= e($sensor['localizacao']) ?>"
                        required>
                </div>

                <div class="grupo-campo-trem">
                    <label class="rotulo-campo-trem" for="id_rota">
                        Rota associada
                    </label>

                    <select
                        id="id_rota"
                        name="id_rota"
                        class="campo-edicao-trem">

                        <option value=""
                            <?= empty($sensor['id_rota']) ? 'selected' : '' ?>>
                            Sem rota associada
                        </option>

                        <?php foreach ($rotas as $rota): ?>
                            <option
                                value="<?= (int) $rota['id_rota'] ?>"
                                <?= ((string) ($sensor['id_rota'] ?? '') === (string) $rota['id_rota']) ? 'selected' : '' ?>>

                                <?= e($rota['nome_rota']) ?>
                                (<?= e($rota['codigo']) ?>)

                            </option>
                        <?php endforeach; ?>

                    </select>
                </div>

                <div class="grupo-campo-trem">
                    <label class="rotulo-campo-trem" for="unidade_medida">
                        Unidade de medida<span class="obrigatorio">*</span>
                    </label>

                    <input
                        type="text"
                        id="unidade_medida"
                        name="unidade_medida"
                        class="campo-edicao-trem"
                        value="<?= e($sensor['unidade_medida']) ?>"
                        required>
                </div>

                <div class="grupo-campo-trem">
                    <label class="rotulo-campo-trem" for="limite_alerta">
                        Limite de alerta
                    </label>

                    <input
                        type="number"
                        step="0.01"
                        id="limite_alerta"
                        name="limite_alerta"
                        class="campo-edicao-trem"
                        value="<?= e($sensor['limite_alerta']) ?>"
                        required>
                </div>

            </div>

            <div class="grade-formulario-trem grade-status-descricao">

                <div class="grupo-campo-trem">
                    <label class="rotulo-campo-trem">
                        Status inicial<span class="obrigatorio">*</span>
                    </label>

                    <div class="opcoes-status-trem">

                        <label class="opcao-status-trem">
                            <input
                                type="radio"
                                name="status_inicial"
                                value="Ativo"
                                <?= $sensor['status_inicial'] === 'Ativo' ? 'checked' : '' ?>
                                required>

                            <span class="marcador-status"></span>
                            Ativo
                        </label>

                        <label class="opcao-status-trem">
                            <input
                                type="radio"
                                name="status_inicial"
                                value="Inativo"
                                <?= $sensor['status_inicial'] === 'Inativo' ? 'checked' : '' ?>>

                            <span class="marcador-status"></span>
                            Inativo
                        </label>

                    </div>
                </div>

                <div class="grupo-campo-trem">
                    <label class="rotulo-campo-trem" for="descricao">
                        Descrição
                    </label>

                    <textarea
                        id="descricao"
                        name="descricao"
                        class="campo-edicao-trem textarea-descricao-sensor"><?= e($sensor['descricao']) ?></textarea>
                </div>

            </div>

            <div class="botoes-cadastro-trem botoes-cadastro-sensor">

                <button type="submit" class="botao-salvar-trem">
                    Salvar alterações
                </button>

                <a href="tela-home-sensor.php" class="botao-cancelar-trem">
                    Cancelar
                </a>

            </div>

        </form>

    </section>

</main>

</body>
</html>
