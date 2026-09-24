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

// Busca os dados atuais para preencher o formulário.
$consulta = mysqli_prepare($conexao, 'SELECT nome_trem, modelo_trem, localizacao_i, codigo, capacidade, ano, status FROM trem WHERE id = ?');
mysqli_stmt_bind_param($consulta, 'i', $id);
mysqli_stmt_execute($consulta);
mysqli_stmt_bind_result($consulta, $nome, $modelo, $localizacao, $codigo, $capacidade, $ano, $status);

if (mysqli_stmt_fetch($consulta)) {
    $trem = [
        'nome_trem' => $nome,
        'modelo_trem' => $modelo,
        'localizacao_i' => $localizacao,
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
    $trem['nome_trem'] = trim($_POST['nome_trem'] ?? '');
    $trem['modelo_trem'] = trim($_POST['modelo_trem'] ?? '');
    $trem['localizacao_i'] = trim($_POST['localizacao_i'] ?? '');
    $trem['codigo'] = trim($_POST['codigo'] ?? '');
    $trem['capacidade'] = trim($_POST['capacidade'] ?? '');
    $trem['ano'] = trim($_POST['ano'] ?? '');
    $trem['status'] = $_POST['status'] ?? '';

    $statusValidos = ['operacao', 'parado', 'manutencao', 'inativo'];

    if (
        $trem['nome_trem'] === '' ||
        $trem['modelo_trem'] === '' ||
        $trem['localizacao_i'] === '' ||
        $trem['codigo'] === '' ||
        $trem['capacidade'] === '' ||
        $trem['ano'] === '' ||
        !is_numeric($trem['capacidade']) ||
        !preg_match('/^\d{4}$/', $trem['ano']) ||
        !in_array($trem['status'], $statusValidos, true)
    ) {
        $erro = 'Preencha todos os campos com valores válidos.';
    } else {
        $atualizacao = mysqli_prepare($conexao, 'UPDATE trem SET nome_trem = ?, modelo_trem = ?, localizacao_i = ?, codigo = ?, capacidade = ?, ano = ?, status = ? WHERE id = ?');
        mysqli_stmt_bind_param(
            $atualizacao,
            'ssssdssi',
            $trem['nome_trem'],
            $trem['modelo_trem'],
            $trem['localizacao_i'],
            $trem['codigo'],
            $trem['capacidade'],
            $trem['ano'],
            $trem['status'],
            $id
        );

        if (mysqli_stmt_execute($atualizacao)) {
            mysqli_stmt_close($atualizacao);
            header('Location: lista_trem.php');
            exit;
        }

        if (mysqli_errno($conexao) === 1062) {
            $erro = 'Esse código já está sendo usado por outro trem.';
        } else {
            $erro = 'Não foi possível salvar as alterações. Tente novamente.';
        }
        mysqli_stmt_close($atualizacao);
    }
}

function e($valor)
{
    return htmlspecialchars((string) $valor, ENT_QUOTES, 'UTF-8');
}
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar trem</title>
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

        <main class="conteudo conteudo-edicao-trem">
            <h2>Editar trem</h2>
            <p class="subtitulo-edicao-trem">Preencha as informações para editar o trem cadastrado</p>

            <section class="painel-formulario-trem">
                <?php if ($erro !== ''): ?>
                    <p class="aviso-edicao-trem"><?= e($erro) ?></p>
                <?php endif; ?>

                <form method="post" action="edicao_trem.php?id=<?= e($id) ?>">
                    <input type="hidden" name="id" value="<?= e($id) ?>">

                    <div class="grade-formulario-trem">
                        <div class="grupo-campo-trem">
                            <label class="rotulo-campo-trem" for="nome_trem">Nome do trem<span class="obrigatorio">*</span></label>
                            <input class="campo-edicao-trem" type="text" id="nome_trem" name="nome_trem" maxlength="100" value="<?= e($trem['nome_trem']) ?>" required>
                        </div>

                        <div class="grupo-campo-trem">
                            <label class="rotulo-campo-trem" for="modelo_trem">Modelo<span class="obrigatorio">*</span></label>
                            <input class="campo-edicao-trem" type="text" id="modelo_trem" name="modelo_trem" maxlength="100" value="<?= e($trem['modelo_trem']) ?>" required>
                        </div>

                        <div class="grupo-campo-trem">
                            <label class="rotulo-campo-trem" for="localizacao_i">Localização inicial<span class="obrigatorio">*</span></label>
                            <input class="campo-edicao-trem" type="text" id="localizacao_i" name="localizacao_i" maxlength="150" value="<?= e($trem['localizacao_i']) ?>" required>
                        </div>

                        <div class="grupo-campo-trem">
                            <label class="rotulo-campo-trem" for="codigo">Código<span class="obrigatorio">*</span></label>
                            <input class="campo-edicao-trem" type="text" id="codigo" name="codigo" maxlength="30" value="<?= e($trem['codigo']) ?>" required>
                        </div>

                        <div class="grupo-campo-trem opcoes-status-trem">
                            <span class="rotulo-campo-trem">Status inicial<span class="obrigatorio">*</span></span>
                            <label class="opcao-status-trem">
                                <input type="radio" name="status" value="operacao" <?= $trem['status'] === 'operacao' ? 'checked' : '' ?> required>
                                <span class="marcador-status"></span>Em operação
                            </label>
                            <label class="opcao-status-trem">
                                <input type="radio" name="status" value="parado" <?= $trem['status'] === 'parado' ? 'checked' : '' ?>>
                                <span class="marcador-status"></span>Parado
                            </label>
                            <label class="opcao-status-trem">
                                <input type="radio" name="status" value="manutencao" <?= $trem['status'] === 'manutencao' ? 'checked' : '' ?>>
                                <span class="marcador-status"></span>Manutenção
                            </label>
                            <label class="opcao-status-trem">
                                <input type="radio" name="status" value="inativo" <?= $trem['status'] === 'inativo' ? 'checked' : '' ?>>
                                <span class="marcador-status"></span>Inativo
                            </label>
                        </div>

                        <div class="grupo-campo-trem">
                            <label class="rotulo-campo-trem" for="capacidade">Capacidade<span class="obrigatorio">*</span></label>
                            <input class="campo-edicao-trem" type="number" id="capacidade" name="capacidade" min="0" step="0.01" value="<?= e($trem['capacidade']) ?>" required>
                        </div>

                        <div class="grupo-campo-trem campo-ano-trem">
                            <label class="rotulo-campo-trem" for="ano">Ano<span class="obrigatorio">*</span></label>
                            <input class="campo-edicao-trem" type="text" id="ano" name="ano" maxlength="4" pattern="[0-9]{4}" value="<?= e($trem['ano']) ?>" required>
                        </div>
                    </div>

                    <div class="acoes-edicao-trem">
                        <button class="botao-edicao-claro" type="submit">Salvar alterações</button>
                        <a class="botao-edicao-claro" href="lista_trem.php">← Voltar para trens</a>
                    </div>
                </form>
            </section>
        </main>
    </div>
</body>

</html>