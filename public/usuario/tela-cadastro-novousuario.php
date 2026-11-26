<?php
ob_start();
mysqli_report(MYSQLI_REPORT_OFF);

// inclui o arquivo que verifica se o usuario esta conectado e e administrador
require_once '../../infra/verificar-admin.php';

// inclui o arquivo de conexao com o banco de dados
require_once '../../infra/conexao.php';

$mensagem = '';
$erro = '';
$tipoErro = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $email = trim($_POST['email'] ?? '');
    $senha = $_POST['senha'] ?? '';
    $cep = preg_replace('/\D/', '', $_POST['cep'] ?? '');
    $logradouro = trim($_POST['logradouro'] ?? '');
    $bairro = trim($_POST['bairro'] ?? '');
    $cidade = trim($_POST['cidade'] ?? '');
    $uf = strtoupper(trim($_POST['uf'] ?? ''));
    $tipo = $_POST['tipo'] ?? '';

    if (empty($email) || empty($senha) || empty($tipo)) {

        $erro = 'Preencha todos os campos obrigatórios.';

    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

        $erro = 'Informe um e-mail válido.';

    } elseif (strlen($senha) < 8) {

        $erro = 'A senha deve ter no mínimo 8 caracteres.';

    } elseif ($cep !== '' && !preg_match('/^[0-9]{8}$/', $cep)) {

        $erro = 'Informe um CEP válido no formato 00000-000.';

    } elseif (!in_array($tipo, ['administrador', 'funcionario'], true)) {

        $erro = 'Selecione um tipo de usuário válido.';

    } elseif ($cep !== '' && ($logradouro === '' || $cidade === '' || $uf === '')) {

        $erro = 'Consulte um CEP válido antes de cadastrar o usuário.';

    } else {

        if ($cep !== '') {
            $cep = substr($cep, 0, 5) . '-' . substr($cep, 5, 3);
        } else {
            $cep = null;
            $logradouro = null;
            $bairro = null;
            $cidade = null;
            $uf = null;
        }

        $stmt = $conexao->prepare("SELECT id_usuario FROM usuario WHERE email = ?");

        if ($stmt) {

            $stmt->bind_param("s", $email);
            $stmt->execute();
            $resultado = $stmt->get_result();

            if ($resultado->num_rows > 0) {

                $erro = 'Este e-mail já está cadastrado.';
                $tipoErro = 'duplicado';

            } else {

                $senhaHash = password_hash($senha, PASSWORD_DEFAULT);

                $sql = "INSERT INTO usuario
                        (email, senha, cep, logradouro, bairro, cidade, uf, tipo)
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

                $stmt2 = $conexao->prepare($sql);

                if ($stmt2) {

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

                    if ($stmt2->execute()) {

                        $stmt2->close();
                        $stmt->close();

                        header('Location: tela-home-usuario.php');
                        exit;

                    } else {

                        $erro = 'Não foi possível cadastrar o usuário: ' . $stmt2->error;
                    }

                    $stmt2->close();

                } else {

                    $erro = 'Erro ao preparar o cadastro do usuário: ' . $conexao->error;
                }
            }

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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Cadastrar Usuário</title>

    <link rel="stylesheet" href="../../assets/style/style.css">

    <style>
        .aviso-cadastro.falha {
            margin: 16px 0;
            padding: 12px 16px;
            color: #b91c1c;
            background: #fee2e2;
            border: 1px solid #fecaca;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
        }
    </style>

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
                <h1 class="titulo-pagina">Cadastrar novo usuário</h1>
                <p class="descricao-pagina">Preencha as informações para cadastrar um novo usuário.</p>
            </div>

            <button
                class="botao-voltar-sensores"
                onclick="window.location.href='tela-home-usuario.php'">
                🠐 Voltar para usuários
            </button>

        </section>

        <section class="painel-cadastro-sensor">

            <?php if ($erro != '') { ?>
                <div class="<?= $tipoErro === 'duplicado' ? 'erro-duplicado' : 'aviso-cadastro falha' ?>">
                    <?php echo htmlspecialchars($erro, ENT_QUOTES, 'UTF-8'); ?>
                </div>
            <?php } ?>

            <form id="form-cep" class="formulario-cadastro-sensor" method="POST" action="">

                <div class="linha-campos-formulario">

                    <div class="grupo-nome_sensor-sensor">
                        <label class="nome_sensor-sensor">E-mail</label>
                        <input
                            type="email"
                            name="email"
                            class="input-nome_sensorsensor"
                            required
                            value="<?= htmlspecialchars($_POST['email'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                    </div>

                    <div class="grupo-nome_sensor-sensor">
                        <label class="nome_sensor-sensor">CEP</label>
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
                        <label class="nome_sensor-sensor">Logradouro</label>
                        <input
                            type="text"
                            id="logradouro"
                            name="logradouro"
                            class="input-nome_sensorsensor"
                            readonly
                            value="<?= htmlspecialchars($_POST['logradouro'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                    </div>

                    <div class="grupo-nome_sensor-sensor">
                        <label class="nome_sensor-sensor">Bairro</label>
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
                        <label class="nome_sensor-sensor">Cidade</label>
                        <input
                            type="text"
                            id="cidade"
                            name="cidade"
                            class="input-nome_sensorsensor"
                            readonly
                            value="<?= htmlspecialchars($_POST['cidade'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                    </div>

                    <div class="grupo-nome_sensor-sensor">
                        <label class="nome_sensor-sensor">Estado</label>
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
                        <label class="nome_sensor-sensor">Senha</label>
                        <input
                            type="password"
                            name="senha"
                            class="input-nome_sensorsensor"
                            required>
                    </div>

                    <div class="grupo-nome_sensor-sensor">
                        <label class="nome_sensor-sensor">Tipo de usuário</label>
                        <select name="tipo" class="select-tipo-sensor" required>
                            <option value="">Selecione</option>
                            <option value="administrador" <?= ($_POST['tipo'] ?? '') === 'administrador' ? 'selected' : '' ?>>Administrador</option>
                            <option value="funcionario" <?= ($_POST['tipo'] ?? '') === 'funcionario' ? 'selected' : '' ?>>Funcionário</option>
                        </select>
                    </div>

                </div>

                <br>

                <div class="area-botoes-formulario">

                    <button type="submit" class="botao-salvar-trem">
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

    <script>
    (function () {
        var campoCep = document.getElementById("cep");
        var mensagem = document.getElementById("cep-mensagem");
        var logradouro = document.getElementById("logradouro");
        var bairro = document.getElementById("bairro");
        var cidade = document.getElementById("cidade");
        var uf = document.getElementById("uf");

        function limpar() {
            logradouro.value = "";
            bairro.value = "";
            cidade.value = "";
            uf.value = "";
        }

        function buscar() {
            var cep = campoCep.value.replace(/\D/g, "").slice(0, 8);

            campoCep.value = cep.length > 5 ? cep.slice(0, 5) + "-" + cep.slice(5) : cep;

            if (cep.length < 8) {
                limpar();
                mensagem.textContent = "";
                return;
            }

            mensagem.textContent = "Consultando CEP...";

            fetch("https://viacep.com.br/ws/" + cep + "/json/")
                .then(function (r) { return r.json(); })
                .then(function (dados) {
                    if (campoCep.value.replace(/\D/g, "") !== cep) {
                        return;
                    }
                    if (dados.erro) {
                        limpar();
                        mensagem.textContent = "CEP não encontrado.";
                        return;
                    }
                    logradouro.value = dados.logradouro || "";
                    bairro.value = dados.bairro || "";
                    cidade.value = dados.localidade || "";
                    uf.value = dados.uf || "";
                    mensagem.textContent = "Endereço preenchido.";
                })
                .catch(function () {
                    limpar();
                    mensagem.textContent = "Não foi possível consultar o CEP.";
                });
        }

        campoCep.addEventListener("input", buscar);

        // se a pagina abrir com CEP completo e sem endereco, busca sozinho
        if (campoCep.value.replace(/\D/g, "").length === 8 && logradouro.value === "") {
            buscar();
        }
    })();
    </script>

</body>

</html>