<?php
include '../../infra/conexao.php';

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
$erro = '';
$trem = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
}

if (!$id || $id < 1) {
    header('Location: lista_trem.php');
    exit;
}

$consulta = mysqli_prepare($conexao, 'SELECT nome_trem, modelo_trem, localizacao_i, codigo, capacidade, ano, status FROM trem WHERE id = ?');
mysqli_stmt_bind_param($consulta, 'i', $id);
mysqli_stmt_execute($consulta);
mysqli_stmt_bind_result($consulta, $nome, $modelo, $localizacao, $codigo, $capacidade, $ano, $status);

if (mysqli_stmt_fetch($consulta)) {
    $trem = [
        'nome' => $nome,
        'modelo' => $modelo,
        'localizacao' => $localizacao,
        'codigo' => $codigo,
        'capacidade' => $capacidade,
        'ano' => $ano,
        'status' => $status,
    ];
}

mysqli_stmt_close($consulta);

if (!$trem) {
    header('Location: lista_trem.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $exclusao = mysqli_prepare($conexao, 'DELETE FROM trem WHERE id = ?');
    mysqli_stmt_bind_param($exclusao, 'i', $id);

    if (mysqli_stmt_execute($exclusao)) {
        mysqli_stmt_close($exclusao);
        header('Location: lista_trem.php');
        exit;
    }

    $erro = 'Não foi possível excluir este trem. Verifique se existem rotas vinculadas a ele.';
    mysqli_stmt_close($exclusao);
}

function e($valor)
{
    return htmlspecialchars((string) $valor, ENT_QUOTES, 'UTF-8');
}

$rotulosStatus = [
    'operacao' => 'Em operação',
    'parado' => 'Parado',
    'manutencao' => 'Manutenção',
    'inativo' => 'Inativo',
];
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Excluir trem</title>
    <link rel="stylesheet" href="../../assets/style/style.css">
</head>

<body>
    <div class="estrutura-dashboard">
        <header class="barra-superior">
            <h1>PIA Enterprise</h1>
        </header>

        <aside class="menu-lateral">
            <nav>
                <a href="../../index.php">Dashboard</a>
                <a href="../sensor/tela-home-sensor.php">Sensores</a>
                <a class="ativo" href="lista_trem.php">Trens</a>
                <a href="#">Rotas</a>
            </nav>
        </aside>

        <main class="conteudo conteudo-excluir-trem">
            <h2>Excluir trem</h2>
            <p class="subtitulo-excluir-trem">Confirme a exclusão do trem relacionado</p>

            <section class="painel-excluir-sensor">
                <div class="icone-excluir-sensor" aria-hidden="true">🗑</div>
                <h2>Tem certeza que deseja excluir este trem?</h2>
                <p>Essa ação não poderá ser desfeita.</p>

                <div class="informacoes-sensor-excluir">
                    <span>Nome: <?= e($trem['nome']) ?></span>
                    <span>Modelo: <?= e($trem['modelo']) ?></span>
                    <span>Localização: <?= e($trem['localizacao']) ?></span>
                    <span>Código: <?= e($trem['codigo']) ?></span>
                    <span>Capacidade: <?= e($trem['capacidade']) ?></span>
                    <span>Ano: <?= e($trem['ano']) ?></span>
                    <span>Status: <?= e($rotulosStatus[$trem['status']] ?? $trem['status']) ?></span>
                </div>

                <?php if ($erro !== ''): ?>
                    <p class="erro-excluir-sensor"><?= e($erro) ?></p>
                <?php endif; ?>

                <div class="botoes-excluir-sensor">
                    <form method="post" action="exclusao_trem.php">
                        <input type="hidden" name="id" value="<?= e($id) ?>">
                        <button class="botao-confirmar-exclusao" type="submit">Excluir trem</button>
                    </form>
                    <button type="button"
                        onclick="window.location.href='exclusao_trem.php?id=<?= (int) $trem['id'] ?>'">
                        Excluir
                    </button>
                </div>
            </section>
        </main>
    </div>
</body>

</html>