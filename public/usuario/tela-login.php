

<?php

// inicia a sessao pra conseguir guardar os dados do usuario durante a navegacao
session_start();

// require_once inclui o arquivo de conexao com o banco apenas uma vez
// ../../ volta duas pastas pra localizar o arquivo conexao.php
require_once '../../infra/conexao.php';

// cria uma variavel vazia pra guardar mensagens de erro do login
$mensagem = '';

// $_SERVER guarda informacoes sobre a requisicao feita pelo navegador
// REQUEST_METHOD informa qual metodo foi utilizado
// == compara se o metodo recebido e igual a POST
// POST acontece quando o usuario envia os dados pelo formulario
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // $_POST recebe os dados enviados pelo formulario
    // ?? '' define uma string vazia caso o campo nao exista ou seja null
    // trim remove espacos do inicio e do final do email
    $email = trim($_POST['email'] ?? '');

    // recebe a senha enviada pelo formulario
    // ?? '' evita erro caso o campo nao tenha sido enviado
    $senha = $_POST['senha'] ?? '';

    // cria a consulta SQL pra buscar o usuario pelo email
    // o ? funciona como um marcador que sera preenchido pelo bind_param
    $sql = "SELECT id_usuario, email, senha, tipo FROM usuario WHERE email = ?";

    // prepare prepara a consulta SQL pra receber valores com seguranca
    $stmt = $conexao->prepare($sql);

    // bind_param associa o email ao ? da consulta
    // s indica que o valor enviado e uma string
    $stmt->bind_param("s", $email);

    // execute executa a consulta depois de associar os valores
    $stmt->execute();

    // get_result recebe o resultado da consulta executada
    $resultado = $stmt->get_result();

    // fetch_assoc pega a primeira linha do resultado e transforma em um array associativo
    // assim os dados podem ser acessados pelo nome das colunas
    $usuario = $resultado->fetch_assoc();

    // cria uma variavel booleana com false pra indicar que a senha ainda nao foi validada
    $senhaValida = false;

    // verifica se a consulta encontrou algum usuario com o email informado
    // if executa o bloco somente quando a condicao e verdadeira
    if ($usuario) {

        // password_verify compara a senha digitada com o hash salvo no banco
        // nao e necessario descriptografar a senha, pois a funcao faz a verificacao
        if (password_verify($senha, $usuario['senha'])) {

            // se a senha corresponder ao hash, altera a variavel pra true
            $senhaValida = true;

        } else {

            // password_get_info verifica as informacoes do hash armazenado
            // algoName informa qual algoritmo foi utilizado
            $infoHash = password_get_info($usuario['senha']);

            // verifica se a senha armazenada ainda esta em texto puro
            // === compara valor e tipo
            // && significa E, entao as duas condicoes precisam ser verdadeiras
            // hash_equals compara os textos de forma segura
            if (
                $infoHash['algoName'] === 'unknown' &&
                hash_equals($usuario['senha'], $senha)
            ) {

                // password_hash transforma a senha em um hash seguro
                // PASSWORD_DEFAULT utiliza o algoritmo padrao recomendado pelo PHP
                $senhaHash = password_hash(
                    $senha,
                    PASSWORD_DEFAULT
                );

                // cria uma consulta pra atualizar a senha antiga pelo novo hash
                $sqlAtualizar = "UPDATE usuario SET senha = ? WHERE id_usuario = ?";

                // prepare prepara a consulta pra receber os valores com seguranca
                $stmtAtualizar = $conexao->prepare($sqlAtualizar);

                // bind_param associa os valores aos dois marcadores da consulta
                // s representa a senha como string e i representa o id como inteiro
                $stmtAtualizar->bind_param(
                    "si",
                    $senhaHash,
                    $usuario['id_usuario']
                );

                // execute executa a atualizacao no banco
                // se a atualizacao funcionar, a senha passa a ser considerada valida
                if ($stmtAtualizar->execute()) {
                    $senhaValida = true;
                }

                // close encerra a consulta preparada e libera os recursos utilizados
                $stmtAtualizar->close();
            }
        }
    }

    // verifica se o usuario foi encontrado e se a senha foi validada
    // && exige que as duas condicoes sejam verdadeiras
    if ($usuario && $senhaValida) {

        // session_regenerate_id cria um novo identificador pra sessao
        // true apaga o identificador antigo, ajudando a proteger contra ataques de fixacao de sessao
        session_regenerate_id(true);

        // guarda o id do usuario na sessao pra identificar quem esta conectado
        $_SESSION['id_usuario'] = $usuario['id_usuario'];

        // guarda o email na sessao pra utilizar durante a navegacao
        $_SESSION['usuario'] = $usuario['email'];

        // guarda o tipo de usuario pra controlar permissoes de administrador e funcionario
        $_SESSION['tipo'] = $usuario['tipo'];

        // header redireciona o usuario pro dashboard depois do login
        header("Location: ../../index.php");

        // exit encerra o PHP pra impedir que o restante do codigo seja executado
        exit;

    } else {

        // se o usuario nao existir ou a senha estiver incorreta, guarda uma mensagem de erro
        $mensagem = "E-mail ou senha inválidos.";

    }

    // close encerra a consulta principal e libera os recursos utilizados
    $stmt->close();
}

?>


<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - PIA Enterprise</title>
    <link rel="stylesheet" href="../../assets/style/style.css">

    
</head>

<body id="login-corpo">

    <main id="login-area">
        <section class="caixa-login">

            <form id="form-login" method = "POST">
                <h2 id="titulo">Login - PIA Enterprise</h2>

                <div class="conjunto">
                    <label for="email" class="label-form">Email:</label>
                    <input type="email" class="caixa-escrita" name="email" id="email" placeholder="Email" required>
                </div>

                <div class="conjunto">
                    <label for="senha" class="label-form">Senha:</label>
                    <input type="password" class="caixa-escrita" name="senha" id="senha" placeholder="Senha" required>
                </div>

                <button id="botao-envio" type="submit">
                    Entrar
                </button>
            </form>

            
            <div id="mensagem">
               <?php echo htmlspecialchars($mensagem); ?>
            </div>

            <div class="linha"></div>

       

        </section>
    </main>

</body>

</html>