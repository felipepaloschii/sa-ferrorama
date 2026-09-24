<?php

include '../../infra/conexao.php';

$id = $_GET['id'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $id_trem = $_POST['id_trem'];
    $nome_rota = $_POST['nome_rota'];
    $codigo = $_POST['codigo'];
    $distancia = $_POST['distancia'];
    $tempo_estimado = $_POST['tempo_estimado'];
    $capacidade_rota = $_POST['capacidade_rota'];

    $sql = "UPDATE rotas SET id_trem=?, nome_rota=?, codigo=?, distancia=?, tempo_estimado=?, capacidade_rota=? WHERE id_rota=?";

    $stmt = $conexao->prepare($sql);
    $stmt->bind_param("isssddi", $id_trem, $nome_rota, $codigo, $distancia, $tempo_estimado, $capacidade_rota, $id);

    if ($stmt->execute() === TRUE) {

        header("Location: tela-home-rotas.php");
        exit;

    } else {

        echo "Erro ao atualizar rota.";

    }
}

?>