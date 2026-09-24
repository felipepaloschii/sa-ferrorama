<?php

include '../../infra/conexao.php';

$id = $_GET['id'];

$sql = "DELETE FROM rotas WHERE id_rota = ?";

$stmt = $conexao->prepare($sql);

$stmt->bind_param("i", $id);

$stmt->execute();

header("Location: tela-home-rotas.php");

?>