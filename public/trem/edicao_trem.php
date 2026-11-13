
<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

require_once '../../infra/verificar-admin.php';
require_once '../../infra/conexao.php';

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
$erro = '';
$tipoErro = '';

// Recupera o ID enviado pelo formulário
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
}

if (!$id || $id < 1) {
    header('Location: lista_trem.php');
    exit;
}

// Busca os usuários disponíveis
$usuarios = [];

$resultadoUsuarios = $conexao->query(
    "SELECT id_usuario, email, tipo FROM usuario ORDER BY email"
);

if ($resultadoUsuarios) {
    $usuarios = $resultadoUsuarios->fetch_all(MYSQLI_ASSOC);
}

// Busca os dados do trem
$consulta = $conexao->prepare(
    "SELECT nome_trem, modelo_trem, localizacao_i, codigo,
            capacidade, ano, status, id_usuario
     FROM trem
     WHERE id = ?"
);

$consulta->bind_param("i", $id);
$consulta->execute();

$resultado = $consulta->get_result();
$trem = $resultado->fetch_assoc();

$consulta->close();

if (!$trem) {
    header('Location: lista_trem.php');
    exit;
}

// Atualiza os dados
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $trem['nome_trem'] = trim($_POST['nome_trem'] ?? '');
    $trem['modelo_trem'] = trim($_POST['modelo_trem'] ?? '');
    $trem['localizacao_i'] = trim($_POST['localizacao_i'] ?? '');
    $trem['codigo'] = trim($_POST['codigo'] ?? '');
    $trem['capacidade'] = trim($_POST['capacidade'] ?? '');
    $trem['ano'] = trim($_POST['ano'] ?? '');
    $trem['status'] = $_POST['status'] ?? '';

    // Permite deixar o trem sem usuário associado
    $usuarioSelecionado = trim($_POST['id_usuario'] ?? '');

    if ($usuarioSelecionado === '') {
        $trem['id_usuario'] = null;
    } else {
        $trem['id_usuario'] = filter_var(
            $usuarioSelecionado,
            FILTER_VALIDATE_INT
        );
    }

    $statusValidos = ['operacao', 'parado', 'manutencao', 'inativo'];

    if (
        $trem['nome_trem'] === '' ||
        $trem['modelo_trem'] === '' ||
        $trem['localizacao_i'] === '' ||
        $trem['codigo'] === '' ||
        $trem['capacidade'] === '' ||
        $trem['ano'] === '' ||
        !is_numeric($trem['capacidade']) ||
        !preg_match('/^\d{4}$/', $trem['ano']) ||
        !in_array($trem['status'], $statusValidos, true) ||
        ($usuarioSelecionado !== '' && $trem['id_usuario'] === false)
    ) {
        $erro = 'Preencha todos os campos com valores válidos.';
        $tipoErro = 'normal';
    } else {
        $usuarioExiste = true;

        // Confere o usuário somente se algum foi selecionado
        if ($trem['id_usuario'] !== null) {
            $verifica = $conexao->prepare(
                "SELECT id_usuario FROM usuario WHERE id_usuario = ?"
            );

            $verifica->bind_param("i", $trem['id_usuario']);
            $verifica->execute();
            $verifica->store_result();

            $usuarioExiste = $verifica->num_rows > 0;
            $verifica->close();
        }

        if (!$usuarioExiste) {
            $erro = 'Selecione um usuário responsável válido.';
            $tipoErro = 'normal';
        } else {
            $sql = "UPDATE trem SET
                        nome_trem = ?,
                        modelo_trem = ?,
                        localizacao_i = ?,
                        codigo = ?,
                        capacidade = ?,
                        ano = ?,
                        status = ?,
                        id_usuario = ?
                    WHERE id = ?";

            $atualizacao = $conexao->prepare($sql);

            $atualizacao->bind_param(
                "ssssdssii",
                $trem['nome_trem'],
                $trem['modelo_trem'],
                $trem['localizacao_i'],
                $trem['codigo'],
                $trem['capacidade'],
                $trem['ano'],
                $trem['status'],
                $trem['id_usuario'],
                $id
            );

            try {
                if ($atualizacao->execute()) {
                    $atualizacao->close();
                    header('Location: lista_trem.php');
                    exit;
                }

                $erro = 'Não foi possível salvar as alterações. Tente novamente.';
                $tipoErro = 'normal';

            } catch (mysqli_sql_exception $e) {
                if ($e->getCode() === 1062) {
                    $erro = 'Esse código já está cadastrado em outro trem. Informe um código diferente.';
                    $tipoErro = 'duplicado';
                } else {
                    $erro = 'Não foi possível salvar as alterações. Tente novamente.';
                    $tipoErro = 'normal';
                }
            }

            $atualizacao->close();
        }
    }
}

function e($valor)
{
    return htmlspecialchars((string) $valor, ENT_QUOTES, 'UTF-8');
}
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar trem</title>
    <link rel="stylesheet" href="../../assets/style/style.css">
</head>

<body>
    <div class="estrutura-dashboard">

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

        <main class="conteudo conteudo-edicao-trem">

            <h2>Editar trem</h2>

            <p class="subtitulo-edicao-trem">
                Preencha as informações para editar o trem cadastrado.
            </p>

            <section class="painel-formulario-trem">

                <?php if ($erro !== ''): ?>
                    <p class="<?= $tipoErro === 'duplicado' ? 'erro-duplicado' : 'aviso-edicao-trem' ?>">
                        <?= e($erro) ?>
                    </p>
                <?php endif; ?>

                <form method="post" action="edicao_trem.php?id=<?= e($id) ?>">

                    <input type="hidden" name="id" value="<?= e($id) ?>">

                    <div class="grade-formulario-trem">

                        <div class="grupo-campo-trem">
                            <label class="rotulo-campo-trem" for="nome_trem">
                                Nome do trem<span class="obrigatorio">*</span>
                            </label>

                            <input class="campo-edicao-trem" type="text"
                                id="nome_trem" name="nome_trem" maxlength="100"
                                value="<?= e($trem['nome_trem']) ?>" required>
                        </div>

                        <div class="grupo-campo-trem">
                            <label class="rotulo-campo-trem" for="modelo_trem">
                                Modelo<span class="obrigatorio">*</span>
                            </label>

                            <input class="campo-edicao-trem" type="text"
                                id="modelo_trem" name="modelo_trem" maxlength="100"
                                value="<?= e($trem['modelo_trem']) ?>" required>
                        </div>

                        <div class="grupo-campo-trem">
                            <label class="rotulo-campo-trem" for="localizacao_i">
                                Localização inicial<span class="obrigatorio">*</span>
                            </label>

                            <input class="campo-edicao-trem" type="text"
                                id="localizacao_i" name="localizacao_i" maxlength="150"
                                value="<?= e($trem['localizacao_i']) ?>" required>
                        </div>

                        <div class="grupo-campo-trem">
                            <label class="rotulo-campo-trem" for="codigo">
                                Código<span class="obrigatorio">*</span>
                            </label>

                            <input class="campo-edicao-trem" type="text"
                                id="codigo" name="codigo" maxlength="30"
                                value="<?= e($trem['codigo']) ?>" required>
                        </div>

                        <div class="grupo-campo-trem">
                            <label class="rotulo-campo-trem" for="id_usuario">
                                Usuário responsável
                            </label>

                            <select id="id_usuario" name="id_usuario"
                                class="select-usuario">

                                <option value=""
                                    <?= $trem['id_usuario'] === null ? 'selected' : '' ?>>
                                    Nenhum usuário
                                </option>

                                <?php foreach ($usuarios as $u): ?>
                                    <option value="<?= (int) $u['id_usuario'] ?>"
                                        <?= ((int) $trem['id_usuario'] === (int) $u['id_usuario']) ? 'selected' : '' ?>>
                                        <?= e($u['email']) ?>
                                        (<?= $u['tipo'] === 'administrador' ? 'Admin' : 'Funcionário' ?>)
                                    </option>
                                <?php endforeach; ?>

                            </select>
                        </div>

                        <div class="grupo-campo-trem opcoes-status-trem">
                            <span class="rotulo-campo-trem">
                                Status inicial<span class="obrigatorio">*</span>
                            </span>

                            <label class="opcao-status-trem">
                                <input type="radio" name="status" value="operacao"
                                    <?= $trem['status'] === 'operacao' ? 'checked' : '' ?> required>
                                <span class="marcador-status"></span>Em operação
                            </label>

                            <label class="opcao-status-trem">
                                <input type="radio" name="status" value="parado"
                                    <?= $trem['status'] === 'parado' ? 'checked' : '' ?>>
                                <span class="marcador-status"></span>Parado
                            </label>

                            <label class="opcao-status-trem">
                                <input type="radio" name="status" value="manutencao"
                                    <?= $trem['status'] === 'manutencao' ? 'checked' : '' ?>>
                                <span class="marcador-status"></span>Manutenção
                            </label>

                            <label class="opcao-status-trem">
                                <input type="radio" name="status" value="inativo"
                                    <?= $trem['status'] === 'inativo' ? 'checked' : '' ?>>
                                <span class="marcador-status"></span>Inativo
                            </label>
                        </div>

                        <div class="grupo-campo-trem">
                            <label class="rotulo-campo-trem" for="capacidade">
                                Capacidade<span class="obrigatorio">*</span>
                            </label>

                            <input class="campo-edicao-trem" type="number"
                                id="capacidade" name="capacidade" min="0" step="0.01"
                                value="<?= e($trem['capacidade']) ?>" required>
                        </div>

                        <div class="grupo-campo-trem campo-ano-trem">
                            <label class="rotulo-campo-trem" for="ano">
                                Ano<span class="obrigatorio">*</span>
                            </label>

                            <input class="campo-edicao-trem" type="text"
                                id="ano" name="ano" maxlength="4" pattern="[0-9]{4}"
                                value="<?= e($trem['ano']) ?>" required>
                        </div>

                    </div>

                    <div class="acoes-edicao-trem">

                        <button class="botao-salvar-sensor" type="submit">
                            Salvar alterações
                        </button>

                        <a class="botao-cancelar-cadastro" href="lista_trem.php">
                            Voltar para trens
                        </a>

                    </div>

                </form>
            </section>
        </main>
    </div>
</body>
</html>
