<?php

include '../../infra/conexao.php';

$mensagem = '';
$erro = '';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: cadastro_trem.php');
    exit;
}