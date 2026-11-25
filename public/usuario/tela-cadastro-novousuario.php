

<?php

// inclui o arquivo que verifica se o usuario esta conectado e e administrador
require_once '../../infra/verificar-admin.php';

// inclui o arquivo de conexao com o banco de dados
require_once '../../infra/conexao.php';

// cria variaveis vazias pra guardar mensagens
$mensagem = '';
$erro = '';
$tipoErro = '';

// verifica se o formulario foi enviado pelo metodo POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // recebe o email e remove espacos do inicio e do final
    // ?? utiliza uma string vazia caso o campo nao exista ou seja null
    $email = trim($_POST['email'] ?? '');

    // recebe a senha enviada pelo formulario
    $senha = $_POST['senha'] ?? '';

    // recebe o tipo de usuario selecionado
    $tipo = $_POST['tipo'] ?? '';

    // empty verifica se algum campo esta vazio
    // || significa OU, entao basta um campo estar vazio
    if (empty($email) || empty($senha) || empty($tipo)) {

        // informa que todos os campos precisam ser preenchidos
        $erro = 'Preencha todos os campos.';

    // strlen conta os caracteres da senha
    // verifica se a senha possui menos de 8 caracteres
    } elseif (strlen($senha) < 8) {

        // informa que a senha precisa ter no minimo 8 caracteres
        $erro = 'A senha deve ter no mínimo 8 caracteres.';

    } else {

        // consulta o banco pra verificar se o email ja esta cadastrado
        // o ? sera substituido pelo email no bind_param
        $verificar = "SELECT id_usuario FROM usuario WHERE email = ?";

        // prepare prepara a consulta pra receber valores com seguranca
        $stmt = $conexao->prepare($verificar);

        // verifica se a consulta foi preparada corretamente
        if ($stmt) {

            // associa o email ao ? da consulta
            // s indica que o valor enviado e uma string
            $stmt->bind_param("s", $email);

            // executa a consulta no banco de dados
            $stmt->execute();

            // recebe o resultado da consulta
            $resultado = $stmt->get_result();

            // num_rows informa quantos registros foram encontrados
            // > 0 verifica se ja existe um usuario com esse email
            if ($resultado->num_rows > 0) {

                // informa que o email ja esta cadastrado
                $erro = 'Este e-mail já está cadastrado.';

                // identifica o tipo de erro
                $tipoErro = 'duplicado';

            } else {

                // cria o comando pra inserir um novo usuario
                // os ? serao substituidos pelos valores enviados no bind_param
                $sql = "INSERT INTO usuario (email, senha, tipo) VALUES (?, ?, ?)";

                // prepara a consulta de cadastro
                $stmt2 = $conexao->prepare($sql);

                // verifica se a consulta foi preparada corretamente
                if ($stmt2) {

                    // associa email, senha e tipo aos tres ? da consulta
                    // s indica que os tres valores sao strings
                    $stmt2->bind_param(
                        "sss",
                        $email,
                        $senha,
                        $tipo
                    );

                    // executa o cadastro no banco de dados
                    if ($stmt2->execute()) {

                        // redireciona pra tela de usuarios depois do cadastro
                        header('Location: tela-home-usuario.php');

                        // encerra a execucao depois do redirecionamento
                        exit;
                    }

                    // encerra a consulta e libera os recursos utilizados
                    $stmt2->close();

                    // informa que nao foi possivel realizar o cadastro
                    $erro = 'Não foi possível cadastrar o usuário.';

                } else {

                    // informa que ocorreu um erro ao preparar o cadastro
                    $erro = 'Erro ao preparar o cadastro do usuário.';
                }
            }

            // encerra a consulta usada pra verificar o email
            $stmt->close();

        } else {

            // informa que ocorreu um erro ao preparar a verificacao do email
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
                        class="botao-salvar-trem"
                        onclick="window.location.href='tela-home-usuario.php'">

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
