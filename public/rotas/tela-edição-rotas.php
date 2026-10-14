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
