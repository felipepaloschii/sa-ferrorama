
<?php
session_start();

require_once '../../infra/conexao.php';

$mensagem = '';
$email = '';
$chaveTentativas = '';
$tempoBloqueio = 600; // 10 minutos
$maxTentativas = 5;

if (!isset($_SESSION['login_tentativas'])) {
    $_SESSION['login_tentativas'] = [];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $email = trim($_POST['email'] ?? '');
    $senha = $_POST['senha'] ?? '';

    $chaveTentativas = hash('sha256', strtolower($email));

    // Verifica se o usuário está bloqueado
    $tentativas = $_SESSION['login_tentativas'][$chaveTentativas] ?? [
        'quantidade' => 0,
        'bloqueado_ate' => 0
    ];

    if ($tentativas['bloqueado_ate'] > time()) {

        $restante = ceil(
            ($tentativas['bloqueado_ate'] - time()) / 60
        );

        $mensagem = "Acesso bloqueado. Tente novamente em {$restante} minuto(s).";

    } else {

        // Reinicia o contador se o bloqueio já terminou
        if ($tentativas['bloqueado_ate'] > 0) {
            $tentativas = [
                'quantidade' => 0,
                'bloqueado_ate' => 0
            ];
        }

        $sql = "SELECT id_usuario, email, senha, tipo
                FROM usuario
                WHERE email = ?";

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

                // Compatibilidade com senhas antigas sem criptografia
                $infoHash = password_get_info($usuario['senha']);

                if (
                    $infoHash['algoName'] === 'unknown' &&
                    hash_equals($usuario['senha'], $senha)
                ) {

                    $senhaHash = password_hash(
                        $senha,
                        PASSWORD_DEFAULT
                    );

                    $sqlAtualizar = "UPDATE usuario
                                     SET senha = ?
                                     WHERE id_usuario = ?";

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

            // Login correto: zera as tentativas
            unset($_SESSION['login_tentativas'][$chaveTentativas]);

            session_regenerate_id(true);

            $_SESSION['id_usuario'] = $usuario['id_usuario'];
            $_SESSION['usuario'] = $usuario['email'];
            $_SESSION['tipo'] = $usuario['tipo'];

            $stmt->close();

            header("Location: ../../index.php");
            exit;

        } else {

            // Login incorreto: soma uma tentativa
            $tentativas['quantidade']++;

            if ($tentativas['quantidade'] >= $maxTentativas) {

                $tentativas['bloqueado_ate'] = time() + $tempoBloqueio;

                $mensagem = "Você excedeu as 5 tentativas. Acesso bloqueado por 10 minutos.";

            } else {

                $faltam = $maxTentativas - $tentativas['quantidade'];

                $mensagem = "E-mail ou senha inválidos. Você ainda tem {$faltam} tentativa(s).";
            }

            $_SESSION['login_tentativas'][$chaveTentativas] = $tentativas;
        }

        $stmt->close();
    }
}
?>

<!DOCTYPE html>
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

            <form id="form-login" method="POST" action="tela-login.php">

                <h2 id="titulo">Login - PIA Enterprise</h2>

                <div class="conjunto">
                    <label for="email" class="label-form">Email:</label>
                    <input
                        type="email"
                        class="caixa-escrita"
                        name="email"
                        id="email"
                        placeholder="Email"
                        value="<?= htmlspecialchars($email) ?>"
                        required
                    >
                </div>

                <div class="conjunto">
                    <label for="senha" class="label-form">Senha:</label>
                    <input
                        type="password"
                        class="caixa-escrita"
                        name="senha"
                        id="senha"
                        placeholder="Senha"
                        required
                    >
                </div>

                <button id="botao-envio" type="submit">
                    Entrar
                </button>

            </form>

            <?php if (!empty($mensagem)): ?>
                <div id="mensagem-login" class="erro-excluir-sensor " role="alert">
                    <span class="icone-alerta">!</span>
                    <span><?= htmlspecialchars($mensagem) ?></span>
                </div>
            <?php endif; ?>

            <div class="linha"></div>

        </section>
    </main>

    <script src="../../script/login.js"></script>

</body>
</html>
