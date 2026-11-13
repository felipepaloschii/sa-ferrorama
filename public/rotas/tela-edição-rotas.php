
<?php

require_once '../../infra/verificar-admin.php';
require_once '../../infra/conexao.php';

$mensagem = '';
$trens = [];

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$id || $id < 1) {
    header('Location: tela-home-rotas.php');
    exit;
}

// Busca os trens cadastrados
$consultaTrens = $conexao->query(
    "SELECT id, nome_trem, modelo_trem
     FROM trem
     ORDER BY nome_trem"
);

if ($consultaTrens) {
    $trens = $consultaTrens->fetch_all(MYSQLI_ASSOC);
}

// Busca a rota
$sql = "SELECT * FROM rotas WHERE id_rota = ?";
$stmt = $conexao->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();

$resultado = $stmt->get_result();
$rota = $resultado->fetch_assoc();
$stmt->close();

if (!$rota) {
    header('Location: tela-home-rotas.php');
    exit;
}

// Atualização
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $nome_rota = trim($_POST['nome_rota'] ?? '');
    $id_trem = (int) ($_POST['id_trem'] ?? 0);
    $tempo_estimado = trim($_POST['tempo_estimado'] ?? '');
    $codigo = trim($_POST['codigo'] ?? '');
    $distancia = (float) ($_POST['distancia'] ?? 0);
    $status = $_POST['status'] ?? '';

    $consultaTrem = $conexao->prepare(
        "SELECT modelo_trem FROM trem WHERE id = ?"
    );

    $consultaTrem->bind_param("i", $id_trem);
    $consultaTrem->execute();

    $resultadoTrem = $consultaTrem->get_result();
    $tremSelecionado = $resultadoTrem->fetch_assoc();
    $consultaTrem->close();

    if (
        $nome_rota !== '' &&
        $tremSelecionado &&
        $tempo_estimado !== '' &&
        $codigo !== '' &&
        $distancia > 0 &&
        in_array($status, ['Ativa', 'Inativa'], true)
    ) {

        $modelo_trem = $tremSelecionado['modelo_trem'];

        $sql = "UPDATE rotas SET
                    id_trem = ?,
                    nome_rota = ?,
                    modelo_trem = ?,
                    tempo_estimado = ?,
                    codigo = ?,
                    distancia = ?,
                    status = ?
                WHERE id_rota = ?";

        $atualizacao = $conexao->prepare($sql);

        $atualizacao->bind_param(
            "issssdsi",
            $id_trem,
            $nome_rota,
            $modelo_trem,
            $tempo_estimado,
            $codigo,
            $distancia,
            $status,
            $id
        );

        if ($atualizacao->execute()) {
            $atualizacao->close();
            header('Location: tela-home-rotas.php');
            exit;
        } else {
            $mensagem = 'Erro ao atualizar a rota. Verifique se o código já existe.';
        }

        $atualizacao->close();

        $rota['id_trem'] = $id_trem;
        $rota['nome_rota'] = $nome_rota;
        $rota['modelo_trem'] = $modelo_trem;
        $rota['tempo_estimado'] = $tempo_estimado;
        $rota['codigo'] = $codigo;
        $rota['distancia'] = $distancia;
        $rota['status'] = $status;

    } else {
        $mensagem = 'Preencha todos os campos corretamente e selecione um trem válido.';

        $rota['id_trem'] = $id_trem;
        $rota['nome_rota'] = $nome_rota;
        $rota['tempo_estimado'] = $tempo_estimado;
        $rota['codigo'] = $codigo;
        $rota['distancia'] = $distancia;
        $rota['status'] = $status;
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

    <title>Editar Rotas - PIA Enterprise</title>

    <link rel="stylesheet" href="../../assets/style/style.css">
</head>

<body class="estrutura-dashboard">

    <header class="barra-superior">
        <h1>PIA Enterprise</h1>

        <nav class="navegacao-principal">
            <a href="../../index.php">Dashboard</a>
            <a href="../sensor/tela-home-sensor.php">Sensores</a>
            <a href="../trem/lista_trem.php">Trens</a>
            <a class="ativo" href="tela-home-rotas.php">Rotas</a>

            <?php if (isset($_SESSION['tipo']) && $_SESSION['tipo'] === 'administrador'): ?>
                <a href="../usuario/tela-home-usuario.php">Usuários</a>
            <?php endif; ?>
        </nav>
    </header>

    <main class="conteudo-edicao-trem">

        <h2>Editar rotas</h2>

        <p class="subtitulo-edicao-trem">
            Preencha as informações para editar a rota cadastrada.
        </p>

        <section class="painel-formulario-trem">

            <?php if ($mensagem !== ''): ?>
                <p class="erro-excluir-sensor">
                    <?= e($mensagem) ?>
                </p>
            <?php endif; ?>

            <form method="POST">

                <div class="grade-formulario-trem">

                    <div>

                        <div class="grupo-campo-trem">
                            <label class="rotulo-campo-trem" for="nome_rota">
                                Nome da rota <span class="obrigatorio">*</span>
                            </label>

                            <input
                                class="campo-edicao-trem"
                                type="text"
                                id="nome_rota"
                                name="nome_rota"
                                placeholder="Ex.: Rota Joinville → Barra Velha"
                                value="<?= e($rota['nome_rota']) ?>"
                                required
                            >
                        </div>

                        <div class="grupo-campo-trem">
                            <label class="rotulo-campo-trem" for="tempo_estimado">
                                Tempo estimado <span class="obrigatorio">*</span>
                            </label>

                            <input
                                class="campo-edicao-trem"
                                type="text"
                                id="tempo_estimado"
                                name="tempo_estimado"
                                placeholder="Ex.: 00h45m"
                                value="<?= e($rota['tempo_estimado']) ?>"
                                required
                            >
                        </div>

                        <div class="grupo-campo-trem">
                            <label class="rotulo-campo-trem">
                                Status inicial <span class="obrigatorio">*</span>
                            </label>

                            <div class="opcoes-status-trem">

                                <label class="opcao-status-trem">
                                    <input
                                        type="radio"
                                        name="status"
                                        value="Ativa"
                                        <?= ($rota['status'] === 'Ativa') ? 'checked' : '' ?>
                                        required
                                    >
                                    <span class="marcador-status"></span>
                                    Ativa
                                </label>

                                <label class="opcao-status-trem">
                                    <input
                                        type="radio"
                                        name="status"
                                        value="Inativa"
                                        <?= ($rota['status'] === 'Inativa') ? 'checked' : '' ?>
                                    >
                                    <span class="marcador-status"></span>
                                    Inativa
                                </label>

                            </div>
                        </div>

                    </div>

                    <div>

                        <div class="grupo-campo-trem">
                            <label class="rotulo-campo-trem" for="id_trem">
                                Trem responsável <span class="obrigatorio">*</span>
                            </label>

                            <select
                                id="id_trem"
                                name="id_trem"
                                class="select-usuario"
                                required
                            >
                                <option value="">Selecione um trem...</option>

                                <?php foreach ($trens as $t): ?>
                                    <option
                                        value="<?= (int) $t['id'] ?>"
                                        <?= ((int) ($rota['id_trem'] ?? 0) === (int) $t['id']) ? 'selected' : '' ?>
                                    >
                                        <?= e($t['nome_trem']) ?>
                                        (<?= e($t['modelo_trem']) ?>)
                                    </option>
                                <?php endforeach; ?>

                            </select>
                        </div>

                        <div class="grupo-campo-trem">
                            <label class="rotulo-campo-trem" for="codigo">
                                Código <span class="obrigatorio">*</span>
                            </label>

                            <input
                                class="campo-edicao-trem"
                                type="text"
                                id="codigo"
                                name="codigo"
                                placeholder="Ex.: RT-016"
                                value="<?= e($rota['codigo']) ?>"
                                required
                            >
                        </div>

                        <div class="grupo-campo-trem">
                            <label class="rotulo-campo-trem" for="distancia">
                                Distância (km) <span class="obrigatorio">*</span>
                            </label>

                            <input
                                class="campo-edicao-trem"
                                type="number"
                                id="distancia"
                                name="distancia"
                                placeholder="Ex.: 50 km"
                                step="0.1"
                                min="0.1"
                                value="<?= e($rota['distancia']) ?>"
                                required
                            >
                        </div>

                    </div>

                </div>

                <div class="acoes-edicao-trem">

                    <a
                        href="tela-home-rotas.php"
                        class="botao-cancelar-cadastro"
                    >
                        ← Voltar para rotas
                    </a>

                    <button
                        type="submit"
                        class="botao-salvar-sensor"
                    >
                        Salvar alterações
                    </button>

                </div>

            </form>

        </section>

    </main>

</body>
</html>
