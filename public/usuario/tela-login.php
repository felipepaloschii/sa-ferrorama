<?php 

include '../../infra/conexao.php';

$mensagem = '';



if ($_SERVER["REQUEST_METHOD"]  == "POST") {
    $email = $_POST['email'];
    $senha = $_POST['senha'];

    $sql = "SELECT * FROM usuario WHERE email = '$email' AND senha = '$senha'";

    $resultado = $conexao->query($sql);

    if ($resultado->num_rows > 0) {
        session_start();
        $_SESSION['usuario'] = $email;
        header("Location: ../../index.php");
        exit;
    } else {
        $mensagem = "Usuário ou senha inválidos.";
    }
}

?>


<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - FJL Tech</title>
    <link rel="stylesheet" href="../../assets/style/style.css">
</head>

<body id="login-corpo">

    <main id="login-area">
        <section class="caixa-login">

            <form id="form-login" method = "POST">
                <h2 id="titulo">Login - Administrador</h2>

                <div class="conjunto">
                    <label for="email" class="label-form">Usuario:</label>
                    <input type="email" class="caixa-escrita" name="email" id="email" placeholder="Usuario" required>
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
                <?php echo $mensagem; ?>
            </div>

            <div class="linha"></div>

            <div class="toggle" id="toggle">
             <a href="tela-cadastro-usuario.php">Não tem conta? Cadastre-se</a>
           
            </div>

        </section>
    </main>

    <script src="script/login.js"></script>
</body>

</html>