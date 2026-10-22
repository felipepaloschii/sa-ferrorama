<?php

include '../../infra/conexao.php';

$mensagem = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $email = trim($_POST['email']);
    $senha = $_POST['senha'];

    $sql = "SELECT id_usuario FROM usuario WHERE email = ?";
    $stmt = $conexao->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();

    $resultado = $stmt->get_result();

    if ($resultado->num_rows > 0) {

        $mensagem = "Este e-mail já está cadastrado!";

    } else {

        $senhaHash = password_hash($senha, PASSWORD_DEFAULT);

        $sql = "INSERT INTO usuario (email, senha, tipo)
                VALUES (?, ?, 'administrador')";

        $stmt2 = $conexao->prepare($sql);
        $stmt2->bind_param("ss", $email, $senhaHash);

        if ($stmt2->execute()) {

            header("Location: tela-login.php");
            exit;

        } else {

            $mensagem = "Erro ao cadastrar administrador.";
        }

        $stmt2->close();
    }

    $stmt->close();
}

?>



<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro - PIA Enterprise</title>
    <link rel="stylesheet" href="../../assets/style/style.css">
</head>

<body id="login-corpo">

    <main id="login-area">
        <section class="caixa-login">

            <form id="form-login" method = "POST">
                <h2 id="titulo">Cadastro - PIA Enterprise</h2>

                <div class="conjunto">
                    <label for="email" class="label-form">Email:</label>
                    <input type="email" class="caixa-escrita"name="email" id="email" placeholder="Email" required>
                </div>

                <div class="conjunto">
                    <label for="senha" class="label-form">Senha:</label>
                    <input type="password" class="caixa-escrita" name="senha" id="senha" placeholder="Senha" required>
                </div>

                <button id="botao-envio" type="submit">
                    Cadastrar
                </button>
            </form>

            <div id="mensagem">
    <?php if (!empty($mensagem)) { ?>
        <p "><?php echo htmlspecialchars($mensagem); ?></p>
    <?php } ?>
</div>
            <div class="linha"></div>

            <a href="tela-login.php">Já tem uma conta? Faça login</a>
          

        </section>
    </main>

    <script src="script/login.js"></script>
</body>

</html>