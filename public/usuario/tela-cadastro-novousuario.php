
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
    $email = trim($_POST['email'] ?? '');

    // recebe a senha enviada pelo formulario
    $senha = $_POST['senha'] ?? '';

    // recebe o cep e remove caracteres que nao sejam numeros
    $cep = preg_replace('/\D/', '', $_POST['cep'] ?? '');

    // recebe os dados de endereco enviados pelo formulario
    $logradouro = trim($_POST['logradouro'] ?? '');
    $bairro = trim($_POST['bairro'] ?? '');
    $cidade = trim($_POST['cidade'] ?? '');
    $uf = strtoupper(trim($_POST['uf'] ?? ''));

    // recebe o tipo de usuario selecionado
    $tipo = $_POST['tipo'] ?? '';

    // verifica se os campos obrigatorios estao vazios
    if (empty($email) || empty($senha) || empty($tipo)) {

        $erro = 'Preencha todos os campos obrigatórios.';

    // verifica se o email possui um formato valido
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

        $erro = 'Informe um e-mail válido.';

    // verifica se a senha possui menos de 8 caracteres
    } elseif (strlen($senha) < 8) {

        $erro = 'A senha deve ter no mínimo 8 caracteres.';

    // verifica se o cep possui um formato invalido
    } elseif ($cep !== '' && !preg_match('/^[0-9]{8}$/', $cep)) {

        $erro = 'Informe um CEP válido no formato 00000-000.';

    // verifica se o tipo informado e permitido
    } elseif (!in_array($tipo, ['administrador', 'funcionario'], true)) {

        $erro = 'Selecione um tipo de usuário válido.';

    // verifica se o cep foi informado mas o endereco nao foi encontrado
    } elseif ($cep !== '' && ($logradouro === '' || $cidade === '' || $uf === '')) {

        $erro = 'Consulte um CEP válido antes de cadastrar o usuário.';

    } else {

        // formata o cep com hifen
        if ($cep !== '') {

            $cep = substr($cep, 0, 5) . '-' . substr($cep, 5, 3);

        } else {

            // utiliza null caso o cep nao tenha sido preenchido
            $cep = null;
            $logradouro = null;
            $bairro = null;
            $cidade = null;
            $uf = null;
        }

        // consulta o banco pra verificar se o email ja esta cadastrado
        $verificar = "SELECT id_usuario FROM usuario WHERE email = ?";

        // prepara a consulta pra receber valores com seguranca
        $stmt = $conexao->prepare($verificar);

        // verifica se a consulta foi preparada corretamente
        if ($stmt) {

            // associa o email ao ? da consulta
            $stmt->bind_param("s", $email);

            // executa a consulta no banco de dados
            $stmt->execute();

            // recebe o resultado da consulta
            $resultado = $stmt->get_result();

            // verifica se o email ja esta cadastrado
            if ($resultado->num_rows > 0) {

                $erro = 'Este e-mail já está cadastrado.';
                $tipoErro = 'duplicado';

            } else {

                // protege a senha utilizando o algoritmo de hash do php
                $senhaHash = password_hash($senha, PASSWORD_DEFAULT);

                // cria o comando pra inserir um novo usuario
                $sql = "INSERT INTO usuario
                        (email, senha, cep, logradouro, bairro, cidade, uf, tipo)
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

                // prepara a consulta de cadastro
                $stmt2 = $conexao->prepare($sql);

                // verifica se a consulta foi preparada corretamente
                if ($stmt2) {

                    // associa os oito valores aos oito ? da consulta
                    $stmt2->bind_param(
                        "ssssssss",
                        $email,
                        $senhaHash,
                        $cep,
                        $logradouro,
                        $bairro,
                        $cidade,
                        $uf,
                        $tipo
                    );

                    // executa o cadastro no banco de dados
                    if ($stmt2->execute()) {

                        // encerra as consultas e libera os recursos utilizados
                        $stmt2->close();
                        $stmt->close();

                        // redireciona pra tela de usuarios depois do cadastro
                        header('Location: tela-home-usuario.php');

                        // encerra a execucao depois do redirecionamento
                        exit;

                    } else {

                        // informa o erro ocorrido durante o cadastro
                        $erro = 'Não foi possível cadastrar o usuário: ' . $stmt2->error;

                    }

                    // encerra a consulta e libera os recursos utilizados
                    $stmt2->close();

                } else {

                    $erro = 'Erro ao preparar o cadastro do usuário: ' . $conexao->error;
                }
            }

            // encerra a consulta usada pra verificar o email
            $stmt->close();

        } else {

            $erro = 'Erro ao verificar o e-mail: ' . $conexao->error;
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
                id="form-cep"
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
                            required
                            value="<?= htmlspecialchars($_POST['email'] ?? '', ENT_QUOTES, 'UTF-8') ?>">

                    </div>

                    <div class="grupo-nome_sensor-sensor">

                        <label class="nome_sensor-sensor">
                            CEP
                        </label>

                        <input
                            type="text"
                            id="cep"
                            name="cep"
                            class="input-nome_sensorsensor"
                            maxlength="9"
                            placeholder="00000-000"
                            value="<?= htmlspecialchars($_POST['cep'] ?? '', ENT_QUOTES, 'UTF-8') ?>">

                        <p id="cep-mensagem"></p>

                    </div>

                </div>

                <div class="linha-campos-formulario">

                    <div class="grupo-nome_sensor-sensor">

                        <label class="nome_sensor-sensor">
                            Logradouro
                        </label>

                        <input
                            type="text"
                            id="logradouro"
                            name="logradouro"
                            class="input-nome_sensorsensor"
                            readonly
                            value="<?= htmlspecialchars($_POST['logradouro'] ?? '', ENT_QUOTES, 'UTF-8') ?>">

                    </div>

                    <div class="grupo-nome_sensor-sensor">

                        <label class="nome_sensor-sensor">
                            Bairro
                        </label>

                        <input
                            type="text"
                            id="bairro"
                            name="bairro"
                            class="input-nome_sensorsensor"
                            readonly
                            value="<?= htmlspecialchars($_POST['bairro'] ?? '', ENT_QUOTES, 'UTF-8') ?>">

                    </div>

                </div>

                <div class="linha-campos-formulario">

                    <div class="grupo-nome_sensor-sensor">

                        <label class="nome_sensor-sensor">
                            Cidade
                        </label>

                        <input
                            type="text"
                            id="cidade"
                            name="cidade"
                            class="input-nome_sensorsensor"
                            readonly
                            value="<?= htmlspecialchars($_POST['cidade'] ?? '', ENT_QUOTES, 'UTF-8') ?>">

                    </div>

                    <div class="grupo-nome_sensor-sensor">

                        <label class="nome_sensor-sensor">
                            Estado
                        </label>

                        <input
                            type="text"
                            id="uf"
                            name="uf"
                            class="input-nome_sensorsensor"
                            readonly
                            value="<?= htmlspecialchars($_POST['uf'] ?? '', ENT_QUOTES, 'UTF-8') ?>">

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
                            class="input-nome_sensorsensor"
                            required>

                    </div>

                    <div class="grupo-nome_sensor-sensor">

                        <label class="nome_sensor-sensor">
                            Tipo de usuário
                        </label>

                        <select
                            name="tipo"
                            class="select-tipo-sensor"
                            required>

                            <option value="">Selecione</option>

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

    <script src="../../script/cep.js"></script>

</body>

</html>
