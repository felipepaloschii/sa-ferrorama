
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

    // recebe o cep e remove espacos do inicio e do final
    $cep = trim($_POST['cep'] ?? '');

    // recebe o tipo de usuario selecionado
    $tipo = $_POST['tipo'] ?? '';

    // verifica se os campos obrigatorios estao vazios
    if (empty($email) || empty($senha) || empty($tipo)) {

        // informa que todos os campos obrigatorios precisam ser preenchidos
        $erro = 'Preencha todos os campos.';

    // verifica se o email possui um formato valido
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

        // informa que o email e invalido
        $erro = 'Informe um e-mail válido.';

    // verifica se a senha possui menos de 8 caracteres
    } elseif (strlen($senha) < 8) {

        // informa que a senha precisa ter no minimo 8 caracteres
        $erro = 'A senha deve ter no mínimo 8 caracteres.';

    // verifica se o cep foi preenchido e possui um formato invalido
    } elseif ($cep !== '' && !preg_match('/^[0-9]{5}-?[0-9]{3}$/', $cep)) {

        // informa que o cep precisa ter 8 numeros
        $erro = 'Informe um CEP válido no formato 00000-000.';

    // verifica se o tipo informado e permitido
    } elseif (!in_array($tipo, ['administrador', 'funcionario'], true)) {

        // informa que o tipo de usuario e invalido
        $erro = 'Selecione um tipo de usuário válido.';

    } else {

        // remove o hifen do cep para padronizar o valor recebido
        $cep = str_replace('-', '', $cep);

        // adiciona o hifen depois dos cinco primeiros numeros
        if ($cep !== '') {
            $cep = substr($cep, 0, 5) . '-' . substr($cep, 5, 3);
        } else {
            // utiliza null caso o cep nao tenha sido preenchido
            $cep = null;
        }

        // consulta o banco pra verificar se o email ja esta cadastrado
        // o ? sera substituido pelo email no bind_param
        $verificar = "SELECT id_usuario FROM usuario WHERE email = ?";

        // prepara a consulta pra receber valores com seguranca
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

            // verifica se o email ja esta cadastrado
            if ($resultado->num_rows > 0) {

                // informa que o email ja esta cadastrado
                $erro = 'Este e-mail já está cadastrado.';

                // identifica o tipo de erro
                $tipoErro = 'duplicado';

            } else {

                // protege a senha utilizando o algoritmo de hash do php
                $senhaHash = password_hash($senha, PASSWORD_DEFAULT);

                // cria o comando pra inserir um novo usuario
                // os ? serao substituidos pelos valores enviados no bind_param
                $sql = "INSERT INTO usuario (email, senha, cep, tipo) VALUES (?, ?, ?, ?)";

                // prepara a consulta de cadastro
                $stmt2 = $conexao->prepare($sql);

                // verifica se a consulta foi preparada corretamente
                if ($stmt2) {

                    // associa email, senha, cep e tipo aos quatro ? da consulta
                    // s indica que todos os valores sao strings
                    $stmt2->bind_param(
                        "ssss",
                        $email,
                        $senhaHash,
                        $cep,
                        $tipo
                    );

                    // executa o cadastro no banco de dados
                    if ($stmt2->execute()) {

                        // encerra a consulta e libera os recursos utilizados
                        $stmt2->close();

                        // encerra a consulta de verificacao
                        $stmt->close();

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
                    <?php echo htmlspecialchars($mensagem, ENT_QUOTES, 'UTF-8'); ?>
                </p>

            <?php } ?>

            <?php if ($erro != '') { ?>

                <div class="<?= $tipoErro === 'duplicado' ? 'erro-duplicado' : 'aviso-cadastro falha' ?>">
                    <?php echo htmlspecialchars($erro, ENT_QUOTES, 'UTF-8'); ?>
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
                            value="<?= htmlspecialchars($_POST['email'] ?? '', ENT_QUOTES, 'UTF-8') ?>">

                    </div>

                    <div class="grupo-nome_sensor-sensor">

                        <label class="nome_sensor-sensor">
                            CEP
                        </label>

                        <input
                            type="text"
                            name="cep"
                            class="input-nome_sensorsensor"
                            maxlength="9"
                            placeholder="00000-000"
                            value="<?= htmlspecialchars($_POST['cep'] ?? '', ENT_QUOTES, 'UTF-8') ?>">

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
