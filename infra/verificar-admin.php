<?php

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

if (
    !isset($_SESSION['id_usuario']) ||
    !isset($_SESSION['tipo']) ||
    $_SESSION['tipo'] !== 'administrador'
) {
    header('Location: ../usuario/tela-login.php');
    exit;
}