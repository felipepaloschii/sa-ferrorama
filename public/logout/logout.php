
<?php

// inicia a sessao pra conseguir acessar os dados do usuario conectado
session_start();

// session_unset remove todas as variaveis armazenadas na sessao
session_unset();

// session_destroy encerra a sessao atual e remove seus dados
session_destroy();

// header redireciona o usuario pra pagina inicial depois de sair da conta
header("Location: ../landing.php");

// exit encerra a execucao do codigo depois do redirecionamento
exit;

?>
