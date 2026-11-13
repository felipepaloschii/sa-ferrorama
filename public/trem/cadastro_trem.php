
<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

require_once '../../infra/verificar-admin.php';
require_once '../../infra/conexao.php';

$usuarios = [];
$erro = '';
$tipoErro = '';

$resUsuarios = $conexao->query(
    "SELECT id_usuario, email, tipo FROM usuario ORDER BY email"
);

if ($resUsuarios) {
    $usuarios = $resUsuarios->fetch_all(MYSQLI_ASSOC);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome_trem     = trim($_POST['nome_trem'] ?? '');
    $modelo_trem   = trim($_POST['modelo_trem'] ?? '');
    $localizacao_i = trim($_POST['localizacao_i'] ?? '');
    $codigo        = trim($_POST['codigo'] ?? '');
    $capacidade    = (float) ($_POST['capacidade'] ?? 0);
    $ano           = (int) ($_POST['ano'] ?? 0);
    $status        = $_POST['status'] ?? '';

    $id_usuario = ($_POST['id_usuario'] ?? '') !== ''
        ? (int) $_POST['id_usuario']
        : null;

    if (
        $nome_trem === '' ||
        $modelo_trem === '' ||
        $localizacao_i === '' ||
        $codigo === '' ||
        $capacidade <= 0 ||
        $ano <= 0 ||
        !in_array($status, ['operacao', 'parado', 'manutencao', 'inativo'], true)
    ) {
        $erro = 'Preencha todos os campos obrigatórios corretamente.';
    } else {
        $usuarioExiste = true;

        if ($id_usuario !== null) {
            $check = $conexao->prepare(
                "SELECT 1 FROM usuario WHERE id_usuario = ?"
            );

            $check->bind_param("i", $id_usuario);
            $check->execute();
            $check->store_result();

            $usuarioExiste = $check->num_rows > 0;
            $check->close();
        }

        if (!$usuarioExiste) {
            $erro = 'Selecione um usuário válido da lista.';
        } else {
            $sql = "INSERT INTO trem
                    (nome_trem, modelo_trem, localizacao_i, codigo,
                     capacidade, ano, status, id_usuario)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

            $stmt = $conexao->prepare($sql);

            if ($stmt) {
                $stmt->bind_param(
                    "ssssdssi",
                    $nome_trem,
                    $modelo_trem,
                    $localizacao_i,
                    $codigo,
                    $capacidade,
                    $ano,
                    $status,
                    $id_usuario
                );

                try {
                    if ($stmt->execute()) {
                        header("Location: lista_trem.php");
                        exit;
                    }
                } catch (mysqli_sql_exception $e) {
                    if ($e->getCode() === 1062) {
                        $erro = 'Esse código já está cadastrado em outro trem. Informe um código diferente.';
                        $tipoErro = 'duplicado';
                    } else {
                        $erro = 'Não foi possível cadastrar o trem.';
                    }
                }

                $stmt->close();
            } else {
                $erro = "Erro ao preparar o cadastro.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastrar Trem - PIA Enterprise</title>
    <link rel="stylesheet" href="../../assets/style/style.css">
</head>

<body>

    <header class="barra-superior">
        <h1>PIA Enterprise</h1>

        <aside class="menu-lateral">
            <nav>
                <a href="../../index.php">Dashboard</a>
                <a href="../sensor/tela-home-sensor.php">Sensores</a>
                <a class="ativo" href="lista_trem.php">Trens</a>
                <a href="../rotas/tela-home-rotas.php">Rotas</a>

                <?php if ($_SESSION['tipo'] === 'administrador'): ?>
                    <a href="../usuario/tela-home-usuario.php">Usuários</a>
                <?php endif; ?>
            </nav>
        </aside>
    </header>

    <main class="area-principal cadastro-trem">

        <div class="cabecalho-cadastro-sensor">
            <div class="informacoes-pagina">
                <h2 class="titulo-pagina">Cadastrar novo trem</h2>
                <p class="descricao-pagina">
                    Preencha as informações para cadastrar um novo trem no sistema.
                </p>
            </div>

            <a href="lista_trem.php" class="botao-voltar-trem">
                ← Voltar para trens
            </a>
        </div>

        <?php if ($erro !== ''): ?>
            <div class="<?= $tipoErro === 'duplicado' ? 'erro-duplicado' : 'aviso-cadastro falha' ?>">
                <?= htmlspecialchars($erro) ?>
            </div>
        <?php endif; ?>

        <section class="painel-cadastro-sensor">

            <form method="POST">

                <div class="grade-formulario-trem">

                    <div class="grupo-campo-trem">
                        <label class="rotulo-campo-trem" for="nome_trem">
                            Nome do trem<span class="obrigatorio">*</span>
                        </label>

                        <input
                            type="text"
                            id="nome_trem"
                            name="nome_trem"
                            placeholder="Ex.: Paloschi trem"
                            value="<?= htmlspecialchars($_POST['nome_trem'] ?? '') ?>"
                            required>
                    </div>

                    <div class="grupo-campo-trem">
                        <label class="rotulo-campo-trem" for="modelo_trem">
                            Modelo<span class="obrigatorio">*</span>
                        </label>

                        <input
                            type="text"
                            id="modelo_trem"
                            name="modelo_trem"
                            placeholder="Ex.: Velocidade Trem A"
                            value="<?= htmlspecialchars($_POST['modelo_trem'] ?? '') ?>"
                            required>
                    </div>

                    <div class="grupo-campo-trem">
                        <label class="rotulo-campo-trem" for="localizacao_i">
                            Localização Inicial<span class="obrigatorio">*</span>
                        </label>

                        <input
                            type="text"
                            id="localizacao_i"
                            name="localizacao_i"
                            placeholder="Ex.: Estação Central"
                            value="<?= htmlspecialchars($_POST['localizacao_i'] ?? '') ?>"
                            required>
                    </div>

                    <div class="grupo-campo-trem">
                        <label class="rotulo-campo-trem" for="codigo">
                            Código<span class="obrigatorio">*</span>
                        </label>

                        <input
                            type="text"
                            id="codigo"
                            name="codigo"
                            placeholder="Ex.: TREM-001"
                            value="<?= htmlspecialchars($_POST['codigo'] ?? '') ?>"
                            required>
                    </div>

                    <div class="grupo-campo-trem">
                        <label class="rotulo-campo-trem" for="id_usuario">
                            Usuário responsável
                        </label>

                        <select
                            id="id_usuario"
                            name="id_usuario"
                            class="select-usuario">

                            <option value="">Sem usuário responsável</option>

                            <?php foreach ($usuarios as $u): ?>
                                <option
                                    value="<?= (int) $u['id_usuario'] ?>"
                                    <?= (($_POST['id_usuario'] ?? '') == $u['id_usuario']) ? 'selected' : '' ?>>

                                    <?= htmlspecialchars($u['email']) ?>
                                    (<?= $u['tipo'] === 'administrador' ? 'Admin' : 'Funcionário' ?>)

                                </option>
                            <?php endforeach; ?>

                        </select>
                    </div>

                    <div class="grupo-campo-trem">
                        <span class="rotulo-campo-trem">Status Inicial*</span>

                        <div class="opcoes-status-trem">

                            <label class="opcao-status-trem">
                                <input
                                    type="radio"
                                    name="status"
                                    value="operacao"
                                    <?= ($_POST['status'] ?? '') === 'operacao' ? 'checked' : '' ?>
                                    required>

                                <span class="marcador-status"></span>
                                Em operação
                            </label>

                            <label class="opcao-status-trem">
                                <input
                                    type="radio"
                                    name="status"
                                    value="parado"
                                    <?= ($_POST['status'] ?? '') === 'parado' ? 'checked' : '' ?>>

                                <span class="marcador-status"></span>
                                Parado
                            </label>

                            <label class="opcao-status-trem">
                                <input
                                    type="radio"
                                    name="status"
                                    value="manutencao"
                                    <?= ($_POST['status'] ?? '') === 'manutencao' ? 'checked' : '' ?>>

                                <span class="marcador-status"></span>
                                Manutenção
                            </label>

                            <label class="opcao-status-trem">
                                <input
                                    type="radio"
                                    name="status"
                                    value="inativo"
                                    <?= ($_POST['status'] ?? '') === 'inativo' ? 'checked' : '' ?>>

                                <span class="marcador-status"></span>
                                Inativo
                            </label>

                        </div>
                    </div>

                    <div class="grupo-campo-trem">
                        <label class="rotulo-campo-trem" for="capacidade">
                            Capacidade<span class="obrigatorio">*</span>
                        </label>

                        <input
                            type="number"
                            id="capacidade"
                            name="capacidade"
                            step="any"
                            placeholder="Ex.: 300"
                            value="<?= htmlspecialchars($_POST['capacidade'] ?? '') ?>"
                            required>
                    </div>

                    <div class="grupo-campo-trem">
                        <label class="rotulo-campo-trem" for="ano">
                            Ano<span class="obrigatorio">*</span>
                        </label>

                        <input
                            type="number"
                            id="ano"
                            name="ano"
                            placeholder="Ex.: 2026"
                            value="<?= htmlspecialchars($_POST['ano'] ?? '') ?>"
                            required>
                    </div>

                </div>

                <div class="botoes-cadastro-trem">
                    <button type="submit" class="botao-salvar-trem">
                        Salvar trem
                    </button>

                    <a href="lista_trem.php" class="botao-voltar-trem">
                        Cancelar
                    </a>
                </div>

            </form>

        </section>
    </main>

</body>
</html>
