
<?php

session_start();

require_once '../../infra/conexao.php';

$mensagem = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $email = trim($_POST['email'] ?? '');
    $senha = $_POST['senha'] ?? '';

    $sql = "SELECT id_usuario, email, senha, tipo FROM usuario WHERE email = ?";

    $stmt = $conexao->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();

    $resultado = $stmt->get_result();
    $usuario = $resultado->fetch_assoc();

    $senhaValida = false;

    if ($usuario) {

        if (password_verify($senha, $usuario['senha'])) {

            $senhaValida = true;

        } else {

            $infoHash = password_get_info($usuario['senha']);

            if (
                $infoHash['algoName'] === 'unknown' &&
                hash_equals($usuario['senha'], $senha)
            ) {

                $senhaHash = password_hash(
                    $senha,
                    PASSWORD_DEFAULT
                );

                $sqlAtualizar = "UPDATE usuario SET senha = ? WHERE id_usuario = ?";

                $stmtAtualizar = $conexao->prepare($sqlAtualizar);

                $stmtAtualizar->bind_param(
                    "si",
                    $senhaHash,
                    $usuario['id_usuario']
                );

                if ($stmtAtualizar->execute()) {
                    $senhaValida = true;
                }

                $stmtAtualizar->close();
            }
        }
    }

    if ($usuario && $senhaValida) {

        session_regenerate_id(true);

        $_SESSION['id_usuario'] = $usuario['id_usuario'];
        $_SESSION['usuario'] = $usuario['email'];
        $_SESSION['tipo'] = $usuario['tipo'];

        header("Location: ../../index.php");
        exit;

    } else {

        $mensagem = "E-mail ou senha inválidos.";

    }

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

            <div class="toggle" id="toggle">
             <a href="tela-cadastro-adm.php">Ainda não tem acesso? Criar conta de administrador</a>
           
            </div>

        </section>
    </main>

    <script src="script/login.js"></script>
</body>

</html>