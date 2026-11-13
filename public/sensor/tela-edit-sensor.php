
<?php
require_once '../../infra/verificar-admin.php';
require_once '../../infra/conexao.php';

$mensagem = '';
$erro = '';
$rotas = [];

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$id || $id < 1) {
    header('Location: tela-home-sensor.php');
    exit;
}

// Busca as rotas disponíveis
$sqlRotas = "SELECT id_rota, nome_rota, codigo
             FROM rotas
             ORDER BY nome_rota";

$resultadoRotas = $conexao->query($sqlRotas);

if ($resultadoRotas) {
    $rotas = $resultadoRotas->fetch_all(MYSQLI_ASSOC);
}

// Busca o sensor
$stmt = $conexao->prepare(
    "SELECT * FROM sensor WHERE id = ?"
);

$stmt->bind_param("i", $id);
$stmt->execute();

$resultado = $stmt->get_result();
$sensor = $resultado->fetch_assoc();

$stmt->close();

if (!$sensor) {
    header('Location: tela-home-sensor.php');
    exit;
}

// Atualização
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $nome_sensor = trim($_POST['nome_sensor'] ?? '');
    $tipo_sensor = trim($_POST['tipo_sensor'] ?? '');
    $localizacao = trim($_POST['localizacao'] ?? '');
    $id_rota = filter_var(
        $_POST['id_rota'] ?? '',
        FILTER_VALIDATE_INT
    );
    $unidade_medida = trim($_POST['unidade_medida'] ?? '');
    $limite_alerta = $_POST['limite_alerta'] ?? '';
    $status_inicial = $_POST['status_inicial'] ?? '';
    $descricao = trim($_POST['descricao'] ?? '');

    $tiposPermitidos = [
        'temperatura',
        'velocidade',
        'vibração',
        'localização'
    ];

    $rotaExiste = false;

    if ($id_rota && $id_rota > 0) {
        $consultaRota = $conexao->prepare(
            "SELECT id_rota FROM rotas WHERE id_rota = ?"
        );

        $consultaRota->bind_param("i", $id_rota);
        $consultaRota->execute();

        $resultadoRota = $consultaRota->get_result();
        $rotaExiste = $resultadoRota->num_rows > 0;

        $consultaRota->close();
    }

    if (
        $nome_sensor === '' ||
        !in_array($tipo_sensor, $tiposPermitidos, true) ||
        $localizacao === '' ||
        !$rotaExiste ||
        $unidade_medida === '' ||
        !is_numeric($limite_alerta) ||
        !in_array($status_inicial, ['Ativo', 'Inativo'], true)
    ) {
        $erro = 'Preencha todos os campos corretamente e selecione uma rota válida.';
    } else {

        $limite_alerta = (float) $limite_alerta;

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

        $stmt = $conexao->prepare($sql);

        if ($stmt) {
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

            if ($stmt->execute()) {
                header('Location: tela-home-sensor.php');
                exit;
            } else {
                $erro = 'Não foi possível atualizar o sensor.';
            }

            $stmt->close();
        } else {
            $erro = 'Erro ao preparar a atualização do sensor.';
        }
    }

    // Mantém os valores preenchidos caso ocorra algum erro
    $sensor['nome_sensor'] = $nome_sensor;
    $sensor['tipo_sensor'] = $tipo_sensor;
    $sensor['localizacao'] = $localizacao;
    $sensor['id_rota'] = $id_rota;
    $sensor['unidade_medida'] = $unidade_medida;
    $sensor['limite_alerta'] = $limite_alerta;
    $sensor['status_inicial'] = $status_inicial;
    $sensor['descricao'] = $descricao;
}

function e($valor)
{
    return htmlspecialchars((string) ($valor ?? ''), ENT_QUOTES, 'UTF-8');
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
            ← Voltar para sensores
        </a>

    </section>

    <?php if ($erro !== ''): ?>
        <div class="aviso-cadastro falha">
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
                        Rota associada<span class="obrigatorio">*</span>
                    </label>

                    <select
                        id="id_rota"
                        name="id_rota"
                        class="campo-edicao-trem"
                        required>

                        <option value="">Selecione uma rota</option>

                        <?php foreach ($rotas as $rota): ?>
                            <option
                                value="<?= (int) $rota['id_rota'] ?>"
                                <?= ((int) $sensor['id_rota'] === (int) $rota['id_rota']) ? 'selected' : '' ?>>

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
