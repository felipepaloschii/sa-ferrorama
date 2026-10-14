<?php

include '../../infra/conexao.php';

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

            <a href="../usuario/tela-home-usuario.php">
                Usuários
            </a>
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
