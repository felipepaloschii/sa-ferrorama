
<?php
require_once '../../infra/verificar-admin.php';
require_once '../../infra/conexao.php';

$mensagem = '';
$erro = '';
$rotas = [];

// Busca as rotas cadastradas para associar ao sensor.
$sqlRotas = "SELECT id_rota, nome_rota, codigo
             FROM rotas
             ORDER BY nome_rota";

$resultadoRotas = $conexao->query($sqlRotas);

if ($resultadoRotas) {
    while ($rota = $resultadoRotas->fetch_assoc()) {
        $rotas[] = $rota;
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nome_sensor = trim($_POST['nome_sensor'] ?? '');
    $tipo_sensor = trim($_POST['tipo_sensor'] ?? '');
    $localizacao = trim($_POST['localizacao'] ?? '');
    $id_rota = filter_input(INPUT_POST, 'id_rota', FILTER_VALIDATE_INT);
    $unidade_medida = trim($_POST['unidade_medida'] ?? '');
    $limite_alerta = (float) ($_POST['limite_alerta'] ?? 0);
    $status_inicial = $_POST['status_inicial'] ?? '';
    $descricao = trim($_POST['descricao'] ?? '');

    if (!$id_rota) {
        $erro = 'Selecione uma rota para o sensor.';
    } else {
        $sql = "INSERT INTO sensor
                (nome_sensor, tipo_sensor, localizacao, id_rota,
                 unidade_medida, limite_alerta, status_inicial, descricao)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = $conexao->prepare($sql);

        if ($stmt) {
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

            if ($stmt->execute()) {
                $mensagem = 'Novo sensor cadastrado com sucesso!';
            } else {
                $erro = 'Não foi possível cadastrar o sensor.';
            }

            $stmt->close();
        } else {
            $erro = 'Erro ao preparar o cadastro do sensor.';
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

        <?php if ($mensagem != ''): ?>
            <div class="aviso-cadastro sucesso">
                <?= htmlspecialchars($mensagem); ?>
            </div>
        <?php endif; ?>

        <?php if ($erro != ''): ?>
            <div class="aviso-cadastro falha">
                <?= htmlspecialchars($erro); ?>
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
                            required>
                    </div>

                    <div class="grupo-campo-trem">
                        <label class="rotulo-campo-trem" for="id_rota">
                            Rota associada<span class="obrigatorio">*</span>
                        </label>

                        <select
                            name="id_rota"
                            id="id_rota"
                            class="campo-edicao-trem"
                            required>

                            <option value="">Selecione uma rota</option>

                            <?php foreach ($rotas as $rota): ?>
                                <option value="<?= (int) $rota['id_rota']; ?>">
                                    <?= htmlspecialchars($rota['nome_rota']); ?>
                                    (<?= htmlspecialchars($rota['codigo']); ?>)
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
                            <option value="°C">°C</option>
                            <option value="km/h">km/h</option>
                            <option value="mm/s">mm/s</option>
                            <option value="GPS">GPS</option>
                            <option value="kWh">kWh</option>
                            <option value="m">m</option>
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
                            placeholder="Ex.: 75.00">
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
                                    checked
                                    required>
                                <span class="marcador-status"></span>
                                Ativo
                            </label>

                            <label class="opcao-status-trem">
                                <input
                                    type="radio"
                                    name="status_inicial"
                                    value="Inativo">
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
                            placeholder="Detalhes adicionais..."></textarea>
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
