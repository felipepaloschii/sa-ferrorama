<?php
ob_start();
mysqli_report(MYSQLI_REPORT_OFF);

// inclui o arquivo que verifica se o usuario esta conectado e e administrador
require_once '../../infra/verificar-admin.php';

// inclui o arquivo de conexao com o banco de dados
require_once '../../infra/conexao.php';

// cria variaveis vazias pra guardar mensagens
$mensagem = '';
$erro = '';
$tipoErro = '';

// verifica se o id existe na url e se e numerico
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {

    // volta pra listagem se o id for invalido
    header('Location: tela-home-usuario.php');
    exit;
}

// converte o id para numero inteiro
$id = (int) $_GET['id'];

// cria a consulta pra buscar os dados do usuario
$sql = "SELECT * FROM usuario WHERE id_usuario = ?";

// prepara a consulta pra receber o id com seguranca
$stmt = $conexao->prepare($sql);

// verifica se a consulta foi preparada corretamente
if (!$stmt) {
    die('Erro ao preparar a consulta do usuário.');
}

// associa o id ao ? da consulta
$stmt->bind_param("i", $id);

// executa a consulta no banco de dados
$stmt->execute();

// recebe o resultado da consulta
$resultado = $stmt->get_result();

// verifica se o usuario foi encontrado
if ($resultado->num_rows == 0) {

    $stmt->close();

    // volta pra listagem se o usuario nao existir
    header('Location: tela-home-usuario.php');
    exit;
}

// guarda os dados encontrados em um array
$usuario = $resultado->fetch_assoc();

// encerra a consulta
$stmt->close();

// verifica se o formulario foi enviado pelo metodo POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // recebe o email e remove espacos do inicio e do final
    $email = trim($_POST['email'] ?? '');

    // recebe a nova senha
    $senha = $_POST['senha'] ?? '';

    // recebe o cep e remove espacos do inicio e do final
    $cep = trim($_POST['cep'] ?? '');

    // recebe os dados de endereco preenchidos pelo cep
    $logradouro = trim($_POST['logradouro'] ?? '');
    $bairro = trim($_POST['bairro'] ?? '');
    $cidade = trim($_POST['cidade'] ?? '');
    $uf = strtoupper(trim($_POST['uf'] ?? ''));

    // recebe o tipo de usuario selecionado
    $tipo = $_POST['tipo'] ?? '';

    // verifica se o email e o tipo estao vazios
    if (empty($email) || empty($tipo)) {

        $erro = 'Preencha o e-mail e o tipo de usuário.';

    // verifica se o email possui um formato valido
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

        $erro = 'Informe um e-mail válido.';

    // verifica se a nova senha possui menos de 8 caracteres
    } elseif ($senha !== '' && strlen($senha) < 8) {

        $erro = 'A nova senha deve ter no mínimo 8 caracteres.';

    // verifica se o cep foi preenchido e possui um formato invalido
    } elseif ($cep !== '' && !preg_match('/^[0-9]{5}-?[0-9]{3}$/', $cep)) {

        $erro = 'Informe um CEP válido no formato 00000-000.';

    // verifica se o tipo informado e permitido
    } elseif (!in_array($tipo, ['administrador', 'funcionario'], true)) {

        $erro = 'Selecione um tipo de usuário válido.';

    // verifica se o cep foi informado mas o endereco nao foi encontrado
    } elseif ($cep !== '' && ($logradouro === '' || $cidade === '' || $uf === '')) {

        $erro = 'Consulte um CEP válido antes de salvar o usuário.';

    } else {

        // remove o hifen do cep para padronizar o valor recebido
        $cep = str_replace('-', '', $cep);

        if ($cep !== '') {

            // adiciona o hifen depois dos cinco primeiros numeros
            $cep = substr($cep, 0, 5) . '-' . substr($cep, 5, 3);

        } else {

            // utiliza null caso o cep nao tenha sido preenchido
            $cep = null;
            $logradouro = null;
            $bairro = null;
            $cidade = null;
            $uf = null;
        }

        // verifica se o email ja pertence a outro usuario
        $verificar = "SELECT id_usuario FROM usuario WHERE email = ? AND id_usuario != ?";

        $stmt = $conexao->prepare($verificar);

        if ($stmt) {

            $stmt->bind_param("si", $email, $id);
            $stmt->execute();
            $resultadoEmail = $stmt->get_result();

            // verifica se o email ja esta sendo utilizado
            if ($resultadoEmail->num_rows > 0) {

                $erro = 'Este e-mail já está cadastrado.';
                $tipoErro = 'duplicado';
            }

            $stmt->close();

        } else {

            $erro = 'Erro ao verificar o e-mail.';
        }

        // continua somente se nao houver erro
        if ($erro === '') {

            // verifica se o campo de senha foi preenchido
            if ($senha !== '') {

                // protege a nova senha utilizando hash
                $senhaHash = password_hash($senha, PASSWORD_DEFAULT);

                // atualiza todos os dados incluindo a senha
                $sql = "UPDATE usuario
                        SET email = ?, senha = ?, cep = ?, logradouro = ?, bairro = ?, cidade = ?, uf = ?, tipo = ?
                        WHERE id_usuario = ?";

                $stmt = $conexao->prepare($sql);

                if ($stmt) {

                    $stmt->bind_param(
                        "ssssssssi",
                        $email,
                        $senhaHash,
                        $cep,
                        $logradouro,
                        $bairro,
                        $cidade,
                        $uf,
                        $tipo,
                        $id
                    );
                }

            } else {

                // atualiza sem alterar a senha atual
                $sql = "UPDATE usuario
                        SET email = ?, cep = ?, logradouro = ?, bairro = ?, cidade = ?, uf = ?, tipo = ?
                        WHERE id_usuario = ?";

                $stmt = $conexao->prepare($sql);

                if ($stmt) {

                    $stmt->bind_param(
                        "sssssssi",
                        $email,
                        $cep,
                        $logradouro,
                        $bairro,
                        $cidade,
                        $uf,
                        $tipo,
                        $id
                    );
                }
            }

            // verifica se a consulta foi preparada e executada corretamente
            if (isset($stmt) && $stmt && $stmt->execute()) {

                $stmt->close();

                // redireciona pra listagem depois de salvar as alteracoes
                header('Location: tela-home-usuario.php');
                exit;
            }

            // encerra a consulta caso ela ainda esteja aberta
            if (isset($stmt) && $stmt) {
                $stmt->close();
            }

            // mostra uma mensagem caso nao consiga atualizar o usuario
            if ($erro === '') {
                $erro = 'Não foi possível atualizar o usuário: ' . $conexao->error;
            }
        }
    }

    // mantem os dados digitados no formulario caso ocorra algum erro
    $usuario['email'] = $email;
    $usuario['cep'] = $cep;
    $usuario['logradouro'] = $logradouro;
    $usuario['bairro'] = $bairro;
    $usuario['cidade'] = $cidade;
    $usuario['uf'] = $uf;
    $usuario['tipo'] = $tipo;
}

?>
<!DOCTYPE html>
<html lang="pt-br">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Editar Usuário</title>

    <link rel="stylesheet" href="../../assets/style/style.css">

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

                <h1 class="titulo-pagina">Editar usuário</h1>

                <p class="descricao-pagina">
                    Preencha as informações para editar um usuário existente no sistema.
                </p>

            </div>

            <button
                class="botao-voltar-sensores"
                onclick="window.location.href='tela-home-usuario.php'">

                Voltar para usuários

            </button>

        </section>

        <?php if ($mensagem != ''): ?>

            <p>
                <?php echo htmlspecialchars($mensagem, ENT_QUOTES, 'UTF-8'); ?>
            </p>

        <?php endif; ?>

        <?php if ($erro != ''): ?>

            <div class="<?= $tipoErro === 'duplicado' ? 'erro-duplicado' : 'aviso-cadastro falha' ?>">

                <?php echo htmlspecialchars($erro, ENT_QUOTES, 'UTF-8'); ?>

            </div>

        <?php endif; ?>

        <section class="painel-cadastro-sensor">

            <form class="formulario-cadastro-sensor" method="POST" action="">

                <div class="linha-campos-formulario">

                    <div class="grupo-nome_sensor-sensor">

                        <label class="nome_sensor-sensor">
                            E-mail *
                        </label>

                        <input
                            type="email"
                            name="email"
                            class="input-nome_sensorsensor"
                            value="<?= htmlspecialchars($usuario['email'], ENT_QUOTES, 'UTF-8') ?>"
                            required>

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
                            value="<?= htmlspecialchars($usuario['cep'] ?? '', ENT_QUOTES, 'UTF-8') ?>">

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
                            value="<?= htmlspecialchars($usuario['logradouro'] ?? '', ENT_QUOTES, 'UTF-8') ?>">

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
                            value="<?= htmlspecialchars($usuario['bairro'] ?? '', ENT_QUOTES, 'UTF-8') ?>">

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
                            value="<?= htmlspecialchars($usuario['cidade'] ?? '', ENT_QUOTES, 'UTF-8') ?>">

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
                            value="<?= htmlspecialchars($usuario['uf'] ?? '', ENT_QUOTES, 'UTF-8') ?>">

                    </div>

                </div>

                <div class="linha-campos-formulario">

                    <div class="grupo-tipo-sensor">

                        <label class="tipo-sensor">
                            Nova senha
                        </label>

                        <input
                            type="password"
                            name="senha"
                            class="select-tipo-sensor"
                            placeholder="Deixe em branco para manter">

                    </div>

                    <div class="grupo-tipo-sensor">

                        <label class="tipo-sensor">
                            Tipo de usuário *
                        </label>

                        <select name="tipo" class="select-tipo-sensor" required>

                            <option value="">
                                Selecione o tipo
                            </option>

                            <option
                                value="administrador"
                                <?php if ($usuario['tipo'] == 'administrador') echo 'selected'; ?>>

                                Administrador

                            </option>

                            <option
                                value="funcionario"
                                <?php if ($usuario['tipo'] == 'funcionario') echo 'selected'; ?>>

                                Funcionário

                            </option>

                        </select>

                    </div>

                </div>

                <br>

                <div class="area-botoes-formulario">

                    <button type="submit" class="botao-salvar-sensor">
                        Salvar alterações
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

    <script src="../../script/cep.js?v=3"></script>

</body>

</html>