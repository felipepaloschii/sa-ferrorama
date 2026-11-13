
<?php
require_once '../../infra/verificar-admin.php';
require_once '../../infra/conexao.php';

$erro = '';

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$id || $id < 1) {
    header('Location: tela-home-sensor.php');
    exit;
}

// Busca o sensor
$stmt = $conexao->prepare(
    "SELECT sensor.*, rotas.nome_rota, rotas.codigo AS codigo_rota
     FROM sensor
     LEFT JOIN rotas ON sensor.id_rota = rotas.id_rota
     WHERE sensor.id = ?"
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

// Confirmação da exclusão
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $stmt = $conexao->prepare(
        "DELETE FROM sensor WHERE id = ?"
    );

    if ($stmt) {
        $stmt->bind_param("i", $id);

        if ($stmt->execute() && $stmt->affected_rows > 0) {
            $stmt->close();

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
            ← Voltar para sensores
        </a>

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
                <?php if (!empty($sensor['nome_rota'])): ?>
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
