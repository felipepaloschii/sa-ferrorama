<?php

require_once '../../infra/verificar-admin.php';
require_once '../../infra/conexao.php';

$mensagem = '';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: tela-home-rotas.php');
    exit;
}

$id = (int) $_GET['id'];

$sql = "SELECT * FROM rotas WHERE id_rota = ?";
$stmt = $conexao->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();

$resultado = $stmt->get_result();
$rota = $resultado->fetch_assoc();

if (!$rota) {
    header('Location: tela-home-rotas.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $nome_rota = trim($_POST['nome_rota']);
    $modelo_trem = trim($_POST['modelo_trem']);
    $tempo_estimado = trim($_POST['tempo_estimado']);
    $codigo = trim($_POST['codigo']);
    $distancia = (float) $_POST['distancia'];
    $status = $_POST['status'];

    if (
        $nome_rota != '' &&
        $modelo_trem != '' &&
        $tempo_estimado != '' &&
        $codigo != '' &&
        $distancia > 0 &&
        in_array($status, ['Ativa', 'Inativa'])
    ) {

        $sql = "UPDATE rotas
                SET nome_rota = ?,
                    modelo_trem = ?,
                    tempo_estimado = ?,
                    codigo = ?,
                    distancia = ?,
                    status = ?
                WHERE id_rota = ?";

                  $stmt = $conexao->prepare($sql);

        $stmt->bind_param(
            "ssssdsi",
            $nome_rota,
            $modelo_trem,
            $tempo_estimado,
            $codigo,
            $distancia,
            $status,
            $id
        );

        if ($stmt->execute()) {
            header('Location: tela-home-rotas.php');
            exit;
        } else {
            $mensagem = 'Erro ao atualizar a rota.';
        }

        } else {
        $mensagem = 'Preencha todos os campos corretamente.';
    }

    $rota['nome_rota'] = $nome_rota;
    $rota['modelo_trem'] = $modelo_trem;
    $rota['tempo_estimado'] = $tempo_estimado;
    $rota['codigo'] = $codigo;
    $rota['distancia'] = $distancia;
    $rota['status'] = $status;
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
    </header>

    <aside class="menu-lateral">
        <nav>
            <a href="../../index.php">Dashboard</a>

            <a href="../sensor/tela-home-sensor.php">
                Sensores
            </a>

            <a href="../trem/lista_trem.php">
                Trens
            </a>

            <a class="ativo" href="tela-home-rotas.php">
                Rotas
            </a>

          <?php if (isset($_SESSION['tipo']) && $_SESSION['tipo'] === 'administrador'): ?>

            <a href="../usuario/tela-home-usuario.php">Usuários</a>

            <?php endif; ?>
        </nav>
    </aside>
    <main class="conteudo-edicao-trem">

        <h2>Editar rotas</h2>

        <p class="subtitulo-edicao-trem">
            Preencha as informações para editar a rota cadastrada
        </p>

        <section class="painel-formulario-trem">

            <?php if ($mensagem != ''): ?>
                <p class="aviso-edicao-trem">
                    <?= htmlspecialchars($mensagem) ?>
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
                                value="<?= htmlspecialchars($rota['nome_rota'] ?? '') ?>"
                                required
                            >
                        </div>

                        <div class="grupo-campo-trem">
                            <label class="rotulo-campo-trem" for="tempo_estimado">
                                Tempo Estimado <span class="obrigatorio">*</span>
                            </label>

                            <input
                                class="campo-edicao-trem"
                                type="text"
                                id="tempo_estimado"
                                name="tempo_estimado"
                                placeholder="Ex.: 00h45m"
                                value="<?= htmlspecialchars($rota['tempo_estimado'] ?? '') ?>"
                                required
                            >
                        </div>

                        <div class="grupo-campo-trem">
                            <label class="rotulo-campo-trem">
                                Status Inicial <span class="obrigatorio">*</span>
                            </label>

                            <div class="opcoes-status-trem">

                                <label class="opcao-status-trem">
                                    <input
                                        type="radio"
                                        name="status"
                                        value="Ativa"
                                        <?= (($rota['status'] ?? '') == 'Ativa') ? 'checked' : '' ?>
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
                                        <?= (($rota['status'] ?? '') == 'Inativa') ? 'checked' : '' ?>
                                    >
                                    <span class="marcador-status"></span>
                                    Inativa
                                </label>

                            </div>
                        </div>

                    </div>

                    <div>

                        <div class="grupo-campo-trem">
                            <label class="rotulo-campo-trem" for="modelo_trem">
                                Trem <span class="obrigatorio">*</span>
                            </label>

                            <input
                                class="campo-edicao-trem"
                                type="text"
                                id="modelo_trem"
                                name="modelo_trem"
                                placeholder="Ex.: Trem X-1947"
                                value="<?= htmlspecialchars($rota['modelo_trem'] ?? '') ?>"
                                required
                            >
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
                                value="<?= htmlspecialchars($rota['codigo'] ?? '') ?>"
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
                                value="<?= htmlspecialchars($rota['distancia'] ?? '') ?>"
                                required
                            >
                        </div>

                    </div>

                </div>

                <div class="acoes-edicao-trem">

                    <a
                        href="tela-home-rotas.php"
                        class="botao-edicao-claro"
                    >
                        ← Voltar para Rotas
                    </a>

                    <button
                        type="submit"
                        class="botao-edicao-escuro"
                    >
                        Salvar
                    </button>

                </div>

            </form>

        </section>

    </main>

</body>
</html>
