
<?php

require_once '../../infra/verificar-admin.php';
require_once '../../infra/conexao.php';

$mensagem = '';
$erro = '';
$tipoErro = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $email = trim($_POST['email'] ?? '');
    $senha = $_POST['senha'] ?? '';
    $tipo = $_POST['tipo'] ?? '';

    if (empty($email) || empty($senha) || empty($tipo)) {

        $erro = 'Preencha todos os campos.';
    } elseif (strlen($senha) < 8) {

        $erro = 'A senha deve ter no mínimo 8 caracteres.';
    } else {

        $verificar = "SELECT id_usuario FROM usuario WHERE email = ?";

        $stmt = $conexao->prepare($verificar);

        if ($stmt) {

            $stmt->bind_param("s", $email);
            $stmt->execute();

            $resultado = $stmt->get_result();

            if ($resultado->num_rows > 0) {

                $erro = 'Este e-mail já está cadastrado.';
                $tipoErro = 'duplicado';

            } else {

                $sql = "INSERT INTO usuario (email, senha, tipo) VALUES (?, ?, ?)";

                $stmt2 = $conexao->prepare($sql);

                if ($stmt2) {

                    $stmt2->bind_param(
                        "sss",
                        $email,
                        $senha,
                        $tipo
                    );

                    try {
                        if ($stmt2->execute()) {
                            $mensagem = 'Novo usuário cadastrado com sucesso!';
                        }
                    } catch (mysqli_sql_exception $e) {
                        if ($e->getCode() === 1062) {
                            $erro = 'Este e-mail já está cadastrado. Informe outro e-mail.';
                            $tipoErro = 'duplicado';
                        } else {
                            $erro = 'Não foi possível cadastrar o usuário.';
                        }
                    }

                    $stmt2->close();

                } else {

                    $erro = 'Erro ao preparar o cadastro do usuário.';
                }
            }

            $stmt->close();

        } else {

            $erro = 'Erro ao verificar o e-mail.';
        }
    }
}

?>

<!DOCTYPE html>

<html lang="pt-br">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0">

    <title>Cadastrar Usuário</title>

    <link
        rel="stylesheet"
        href="../../assets/style/style.css">

</head>

<body>

    <header class="barra-superior">
        <h1>PIA Enterprise</h1>

        <aside class="menu-lateral">
            <nav>
                <a href="../../index.php">Dashboard</a>
                <a href="../sensor/tela-home-sensor.php">Sensores</a>
                <a href="../trem/lista_trem.php">Trens</a>
                <a href="../rotas/tela-home-rotas.php">Rotas</a>

                <?php if (isset($_SESSION['tipo']) && $_SESSION['tipo'] === 'administrador'): ?>
                    <a class="ativo" href="tela-home-usuario.php">Usuários</a>
                <?php endif; ?>
            </nav>
        </aside>
    </header>

    <main class="area-principal">

        <section class="cabecalho-cadastro-sensor">

            <div class="informacoes-pagina">

                <h1 class="titulo-pagina">
                    Cadastrar novo usuário
                </h1>

                <p class="descricao-pagina">
                    Preencha as informações para cadastrar um novo usuário.
                </p>

            </div>

            <button
                class="botao-voltar-sensores"
                onclick="window.location.href='tela-home-usuario.php'">

                🠐 Voltar para usuários

            </button>

        </section>

        <section class="painel-cadastro-sensor">

            <?php if ($mensagem != '') { ?>

                <p style="color: green; font-weight: bold;">
                    <?php echo htmlspecialchars($mensagem); ?>
                </p>

            <?php } ?>

            <?php if ($erro != '') { ?>

                <div class="<?= $tipoErro === 'duplicado' ? 'erro-duplicado' : 'aviso-cadastro falha' ?>">
                    <?php echo htmlspecialchars($erro); ?>
                </div>

            <?php } ?>

            <form
                class="formulario-cadastro-sensor"
                method="POST">

                <div class="linha-campos-formulario">

                    <div class="grupo-nome_sensor-sensor">

                        <label class="nome_sensor-sensor">
                            E-mail
                        </label>

                        <input
                            type="email"
                            name="email"
                            class="input-nome_sensorsensor"
                            value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">

                    </div>

                </div>

                <div class="linha-campos-formulario">

                    <div class="grupo-nome_sensor-sensor">

                        <label class="nome_sensor-sensor">
                            Senha
                        </label>

                        <input
                            type="password"
                            name="senha"
                            class="input-nome_sensorsensor">

                    </div>

                    <div class="grupo-nome_sensor-sensor">

                        <label class="nome_sensor-sensor">
                            Tipo de usuário
                        </label>

                        <select
                            name="tipo"
                            class="select-tipo-sensor">

                            <option value="">
                                Selecione
                            </option>

                            <option value="administrador"
                                <?= ($_POST['tipo'] ?? '') === 'administrador' ? 'selected' : '' ?>>
                                Administrador
                            </option>

                            <option value="funcionario"
                                <?= ($_POST['tipo'] ?? '') === 'funcionario' ? 'selected' : '' ?>>
                                Funcionário
                            </option>

                        </select>

                    </div>

                </div>

                <br>

                <div class="area-botoes-formulario">

                    <button
                        type="submit"
                        class="botao-salvar-trem">

                        Salvar usuário

                    </button>

                    <button
                        type="button"
                        class="botao-cancelar-cadastro"
                        onclick="window.location.href='tela-home-usuario.php'">

                        Cancelar

                    </button>

                </div>

            </form>

        </section>

    </main>

</body>

</html>
