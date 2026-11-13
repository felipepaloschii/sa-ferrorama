
<?php
require_once '../../infra/verificar-admin.php';
require_once '../../infra/conexao.php';

$mensagem = '';
$erro = '';
$tipoErro = '';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: tela-home-usuario.php');
    exit;
}

$id = (int) $_GET['id'];

$sql = "SELECT * FROM usuario WHERE id_usuario = ?";
$stmt = $conexao->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$resultado = $stmt->get_result();

if ($resultado->num_rows == 0) {
    header('Location: tela-home-usuario.php');
    exit;
}

$usuario = $resultado->fetch_assoc();
$stmt->close();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email'] ?? '');
    $senha = $_POST['senha'] ?? '';
    $tipo = $_POST['tipo'] ?? '';

    if (empty($email) || empty($tipo)) {
        $erro = 'Preencha o e-mail e o tipo de usuário.';
    } elseif ($senha !== '' && strlen($senha) < 8) {
        $erro = 'A nova senha deve ter no mínimo 8 caracteres.';
    } else {

        if (!empty($senha)) {
            $sql = "UPDATE usuario SET email = ?, senha = ?, tipo = ? WHERE id_usuario = ?";
            $stmt = $conexao->prepare($sql);
            $stmt->bind_param("sssi", $email, $senha, $tipo, $id);
        } else {
            $sql = "UPDATE usuario SET email = ?, tipo = ? WHERE id_usuario = ?";
            $stmt = $conexao->prepare($sql);
            $stmt->bind_param("ssi", $email, $tipo, $id);
        }

        try {
            if ($stmt && $stmt->execute()) {
                header('Location: tela-home-usuario.php');
                exit;
            }
        } catch (mysqli_sql_exception $e) {
            if ($e->getCode() === 1062) {
                $erro = 'Este e-mail já está cadastrado por outro usuário. Informe outro e-mail.';
                $tipoErro = 'duplicado';
            } else {
                $erro = 'Não foi possível atualizar o usuário.';
            }
        }

        if ($stmt) {
            $stmt->close();
        }

        // Mantém os dados digitados no formulário.
        $usuario['email'] = $email;
        $usuario['tipo'] = $tipo;
    }
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
            <p><?php echo htmlspecialchars($mensagem); ?></p>
        <?php endif; ?>

        <?php if ($erro != ''): ?>
            <div class="<?= $tipoErro === 'duplicado' ? 'erro-duplicado' : 'aviso-cadastro falha' ?>">
                <?php echo htmlspecialchars($erro); ?>
            </div>
        <?php endif; ?>

        <section class="painel-cadastro-sensor">

            <form class="formulario-cadastro-sensor" method="POST" action="">

                <div class="linha-campos-formulario">

                    <div class="grupo-nome_sensor-sensor">
                        <label class="nome_sensor-sensor">E-mail *</label>
                    </div>

                    <input
                        type="email"
                        name="email"
                        class="input-nome_sensorsensor"
                        value="<?php echo htmlspecialchars($usuario['email']); ?>"
                        required>

                    <div class="grupo-tipo-sensor">
                        <label class="tipo-sensor">Nova senha</label>
                        <input
                            type="text"
                            name="senha"
                            class="select-tipo-sensor"
                            placeholder="Deixe em branco para manter">
                    </div>

                </div>

                <div class="linha-campos-formulario">

                    <div class="grupo-tipo-sensor">
                        <label class="tipo-sensor">Tipo de usuário *</label>

                        <select name="tipo" class="select-tipo-sensor" required>
                            <option value="">Selecione o tipo</option>

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
</body>

</html>
