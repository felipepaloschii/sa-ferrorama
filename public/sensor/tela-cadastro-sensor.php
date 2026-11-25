

<?php

// inclui o arquivo que verifica se o usuario esta conectado e e administrador
require_once '../../infra/verificar-admin.php';

// inclui o arquivo de conexao com o banco de dados
require_once '../../infra/conexao.php';

// cria variaveis vazias pra armazenar mensagens
$mensagem = '';
$erro = '';

// cria um array vazio pra armazenar as rotas encontradas
$rotas = [];

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

    // while repete o comando enquanto existirem registros
    // fetch_assoc transforma cada registro em um array associativo
    while ($rota = $resultadoRotas->fetch_assoc()) {

        // adiciona cada rota encontrada ao array
        $rotas[] = $rota;
    }
}

// verifica se o formulario foi enviado pelo metodo POST
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // recebe o nome do sensor e remove espacos do inicio e do final
    // ?? utiliza uma string vazia caso o campo nao exista ou seja null
    $nome_sensor = trim($_POST['nome_sensor'] ?? '');

    // recebe o tipo do sensor e remove espacos extras
    $tipo_sensor = trim($_POST['tipo_sensor'] ?? '');

    // recebe a localizacao e remove espacos extras
    $localizacao = trim($_POST['localizacao'] ?? '');

    // verifica se o campo da rota foi preenchido
    // se estiver vazio, guarda null pra permitir cadastrar sem rota
    // se tiver valor, filter_var verifica se e um numero inteiro valido
    $id_rota = ($_POST['id_rota'] ?? '') !== ''
        ? filter_var($_POST['id_rota'], FILTER_VALIDATE_INT)
        : null;

    // recebe a unidade de medida e remove espacos extras
    $unidade_medida = trim($_POST['unidade_medida'] ?? '');

    // recebe o limite de alerta
    // (float) transforma o valor em numero decimal
    // ?? utiliza zero caso o campo nao exista ou seja null
    $limite_alerta = (float) ($_POST['limite_alerta'] ?? 0);

    // recebe o status inicial do sensor
    $status_inicial = $_POST['status_inicial'] ?? '';

    // recebe a descricao e remove espacos extras
    $descricao = trim($_POST['descricao'] ?? '');

    // verifica se os campos obrigatorios foram preenchidos corretamente
    // === compara valor e tipo ao mesmo tempo
    // || significa OU, entao qualquer condicao verdadeira gera um erro
    // in_array verifica se o status esta entre os valores permitidos
    // true faz a comparacao considerando tambem o tipo do valor
    if (
        $nome_sensor === '' ||
        $tipo_sensor === '' ||
        $localizacao === '' ||
        $unidade_medida === '' ||
        !in_array($status_inicial, ['Ativo', 'Inativo'], true) ||
        ($id_rota !== null && ($id_rota === false || $id_rota <= 0))
    ) {

        // informa que existem campos obrigatorios vazios ou invalidos
        $erro = 'Preencha todos os campos obrigatórios corretamente.';

    } else {

        // considera a rota valida inicialmente
        $rotaExiste = true;

        // verifica a rota somente se uma foi selecionada
        // !== verifica se o valor e diferente de null
        if ($id_rota !== null) {

            // prepara uma consulta pra verificar se a rota existe
            // SELECT 1 verifica a existencia do registro sem buscar outras colunas
            $check = $conexao->prepare(
                "SELECT 1 FROM rotas WHERE id_rota = ?"
            );

            // associa o id da rota ao ? da consulta
            // i indica que o valor e um numero inteiro
            $check->bind_param("i", $id_rota);

            // executa a consulta no banco de dados
            $check->execute();

            // armazena o resultado da consulta pra permitir verificar a quantidade de registros
            $check->store_result();

            // num_rows informa quantas linhas foram encontradas
            // > 0 verifica se a rota existe
            $rotaExiste = $check->num_rows > 0;

            // encerra a consulta e libera os recursos
            $check->close();
        }

        // verifica se a rota selecionada nao existe
        if (!$rotaExiste) {

            // informa que a rota escolhida nao foi encontrada
            $erro = 'A rota selecionada não existe.';

        } else {

            // cria o comando pra cadastrar um novo sensor
            // INSERT INTO adiciona um registro na tabela sensor
            // VALUES define os valores que serao inseridos
            // cada ? sera substituido pelos valores enviados no bind_param
            $sql = "INSERT INTO sensor
                    (nome_sensor, tipo_sensor, localizacao, id_rota,
                     unidade_medida, limite_alerta, status_inicial, descricao)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

            // prepara a consulta de cadastro
            $stmt = $conexao->prepare($sql);

            // verifica se a consulta foi preparada corretamente
            if ($stmt) {

                // associa os valores aos ? da consulta
                // s representa string
                // i representa numero inteiro
                // d representa numero decimal
                // a ordem precisa acompanhar os campos do INSERT
                $stmt->bind_param(
                    "sssisdss",
                    $nome_sensor,
                    $tipo_sensor,
                    $localizacao,
                    $id_rota,
                    $unidade_medida,
                    $limite_alerta,
                    $status_inicial,
                    $descricao
                );

                // executa o cadastro no banco de dados
                if ($stmt->execute()) {

                    // encerra a consulta e libera os recursos
                    $stmt->close();

                    // redireciona pra listagem depois de cadastrar o sensor
                    header("Location: tela-home-sensor.php");

                    // encerra a execucao depois do redirecionamento
                    exit;

                } else {

                    // informa que nao foi possivel cadastrar o sensor
                    $erro = 'Não foi possível cadastrar o sensor.';
                }

                // encerra a consulta depois da tentativa de cadastro
                $stmt->close();

            } else {

                // informa que ocorreu um erro ao preparar o cadastro
                $erro = 'Erro ao preparar o cadastro do sensor.';
            }
        }
    }
}

?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastrar Sensor</title>
    <link rel="stylesheet" href="../../assets/style/style.css">
</head>

<body>

    <header class="barra-superior">
        <h1>PIA Enterprise</h1>

        <nav class="navegacao-principal">
            <a href="../../index.php">Dashboard</a>
            <a class="ativo" href="tela-home-sensor.php">Sensores</a>
            <a href="../trem/lista_trem.php">Trens</a>
            <a href="../rotas/tela-home-rotas.php">Rotas</a>

            <?php if (isset($_SESSION['tipo']) && $_SESSION['tipo'] === 'administrador'): ?>
                <a href="../usuario/tela-home-usuario.php">Usuários</a>
            <?php endif; ?>
        </nav>
    </header>

    <main class="area-principal cadastro-sensor">

        <section class="cabecalho-cadastro-sensor">

            <div class="informacoes-pagina">
                <h1 class="titulo-pagina">Cadastrar novos sensores</h1>

                <p class="descricao-pagina">
                    Preencha as informações para cadastrar um novo sensor.
                </p>
            </div>

            <button
                class="botao-voltar-sensores"
                type="button"
                onclick="window.location.href='tela-home-sensor.php'">
                Voltar para sensores
            </button>

        </section>

        <?php if ($mensagem !== ''): ?>
            <div class="aviso-cadastro sucesso">
                <?= htmlspecialchars($mensagem) ?>
            </div>
        <?php endif; ?>

        <?php if ($erro !== ''): ?>
            <div class="erro-excluir-sensor">
                <?= htmlspecialchars($erro) ?>
            </div>
        <?php endif; ?>

        <section class="painel-cadastro-sensor">

            <form class="formulario-cadastro-sensor" method="POST">

                <div class="grupo-campo-trem">
                    <label class="rotulo-campo-trem" for="nome_sensor">
                        Nome do Sensor<span class="obrigatorio">*</span>
                    </label>

                    <input
                        type="text"
                        id="nome_sensor"
                        name="nome_sensor"
                        class="campo-edicao-trem"
                        placeholder="Ex.: Sensor de Temp. 01"
                        value="<?= htmlspecialchars($_POST['nome_sensor'] ?? '') ?>"
                        required>
                </div>

                <div class="grade-formulario-trem">

                    <div class="grupo-campo-trem">
                        <label class="rotulo-campo-trem" for="tipo_sensor">
                            Tipo do sensor<span class="obrigatorio">*</span>
                        </label>

                        <input
                            type="text"
                            id="tipo_sensor"
                            name="tipo_sensor"
                            class="campo-edicao-trem"
                            placeholder="Ex.: Temperatura"
                            value="<?= htmlspecialchars($_POST['tipo_sensor'] ?? '') ?>"
                            required>
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
                            placeholder="Ex.: Trecho Industrial"
                            value="<?= htmlspecialchars($_POST['localizacao'] ?? '') ?>"
                            required>
                    </div>

                    <div class="grupo-campo-trem">
                        <label class="rotulo-campo-trem" for="id_rota">
                            Rota associada
                        </label>

                        <select
                            name="id_rota"
                            id="id_rota"
                            class="campo-edicao-trem">

                            <option value=""
                                <?= ($_POST['id_rota'] ?? '') === '' ? 'selected' : '' ?>>
                                Sem rota associada
                            </option>

                            <?php foreach ($rotas as $rota): ?>
                                <option
                                    value="<?= (int) $rota['id_rota'] ?>"
                                    <?= (($_POST['id_rota'] ?? '') == $rota['id_rota']) ? 'selected' : '' ?>>

                                    <?= htmlspecialchars($rota['nome_rota']) ?>
                                    (<?= htmlspecialchars($rota['codigo']) ?>)

                                </option>
                            <?php endforeach; ?>

                        </select>
                    </div>

                    <div class="grupo-campo-trem">
                        <label class="rotulo-campo-trem" for="unidade_medida">
                            Unidade de medida<span class="obrigatorio">*</span>
                        </label>

                        <select
                            name="unidade_medida"
                            id="unidade_medida"
                            class="campo-edicao-trem"
                            required>

                            <option value="">Selecione</option>
                            <option value="°C" <?= ($_POST['unidade_medida'] ?? '') === '°C' ? 'selected' : '' ?>>°C</option>
                            <option value="km/h" <?= ($_POST['unidade_medida'] ?? '') === 'km/h' ? 'selected' : '' ?>>km/h</option>
                            <option value="mm/s" <?= ($_POST['unidade_medida'] ?? '') === 'mm/s' ? 'selected' : '' ?>>mm/s</option>
                            <option value="GPS" <?= ($_POST['unidade_medida'] ?? '') === 'GPS' ? 'selected' : '' ?>>GPS</option>
                            <option value="kWh" <?= ($_POST['unidade_medida'] ?? '') === 'kWh' ? 'selected' : '' ?>>kWh</option>
                            <option value="m" <?= ($_POST['unidade_medida'] ?? '') === 'm' ? 'selected' : '' ?>>m</option>

                        </select>
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
                            placeholder="Ex.: 75.00"
                            value="<?= htmlspecialchars($_POST['limite_alerta'] ?? '') ?>">
                    </div>

                </div>

                <div class="grade-formulario-trem grade-status-descricao">

                    <div class="grupo-campo-trem">
                        <label class="rotulo-campo-trem">
                            Status Inicial<span class="obrigatorio">*</span>
                        </label>

                        <div class="opcoes-status-trem">

                            <label class="opcao-status-trem">
                                <input
                                    type="radio"
                                    name="status_inicial"
                                    value="Ativo"
                                    <?= ($_POST['status_inicial'] ?? 'Ativo') === 'Ativo' ? 'checked' : '' ?>
                                    required>

                                <span class="marcador-status"></span>
                                Ativo
                            </label>

                            <label class="opcao-status-trem">
                                <input
                                    type="radio"
                                    name="status_inicial"
                                    value="Inativo"
                                    <?= ($_POST['status_inicial'] ?? '') === 'Inativo' ? 'checked' : '' ?>>

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
                            name="descricao"
                            id="descricao"
                            class="campo-edicao-trem textarea-descricao-sensor"
                            placeholder="Detalhes adicionais..."><?= htmlspecialchars($_POST['descricao'] ?? '') ?></textarea>
                    </div>

                </div>

                <div class="botoes-cadastro-trem botoes-cadastro-sensor">

                    <button type="submit" class="botao-salvar-trem">
                        Salvar sensor
                    </button>

                    <button
                        type="button"
                        class="botao-cancelar-trem"
                        onclick="window.location.href='tela-home-sensor.php'">
                        Cancelar
                    </button>

                </div>

            </form>

        </section>
    </main>

</body>
</html>
