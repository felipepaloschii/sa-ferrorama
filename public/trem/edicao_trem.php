
<?php

// Inicia a sessão caso ainda não esteja ativa

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

// Verifica se o usuário logado é administrador e conecta ao banco de dados

require_once '../../infra/verificar-admin.php';
require_once '../../infra/conexao.php';

// filter_input() lê um valor de fora (URL ou formulário) e valida.
// INPUT_GET pega o valor enviado pela URL (?id=5).
// FILTER_VALIDATE_INT verifica se o valor é um número inteiro.
// Se o valor for inválido, retorna false ou null.

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

// Variáveis para mensagens de erro

$erro = '';
$tipoErro = '';

// Recupera o ID enviado pelo formulário.
// Quando o formulário é enviado pelo método POST, o ID vem de um campo escondido.
// Nesse caso, o valor recebido pelo POST substitui o valor recebido pela URL.

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
}

// Se o ID não existe, é inválido ou é menor que 1,
// redireciona para a lista de trens e interrompe o código.

if (!$id || $id < 1) {
    header('Location: lista_trem.php');
    exit;
}

// Busca os usuários disponíveis

$usuarios = [];

$resultadoUsuarios = $conexao->query(
    "SELECT id_usuario, email, tipo FROM usuario ORDER BY email"
);

// Se a consulta deu certo, transforma o resultado em um array PHP.
// MYSQLI_ASSOC organiza cada linha usando os nomes das colunas como índices.

if ($resultadoUsuarios) {
    $usuarios = $resultadoUsuarios->fetch_all(MYSQLI_ASSOC);
}

// Busca os dados atuais do trem.
// O prepared statement utiliza ? no lugar do valor recebido pela URL.

$consulta = $conexao->prepare(
    "SELECT nome_trem, modelo_trem, localizacao_i, codigo,
            capacidade, ano, status, id_usuario
     FROM trem
     WHERE id = ?"
);

// bind_param() coloca o ID no lugar do ?.
// O "i" significa que o valor é um número inteiro.

$consulta->bind_param("i", $id);

// Executa a consulta no banco de dados.

$consulta->execute();

// get_result() recupera o resultado da consulta.
// fetch_assoc() transforma a primeira linha em um array associativo.

$resultado = $consulta->get_result();
$trem = $resultado->fetch_assoc();

// Libera os recursos da consulta.

$consulta->close();

// Se não encontrou o trem, volta para a lista.

if (!$trem) {
    header('Location: lista_trem.php');
    exit;
}

// Atualiza os dados somente quando o formulário é enviado.

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // trim() remove os espaços sobrando no começo e no fim do texto.
    // ?? significa que, se o campo não existir, será utilizado um texto vazio.

    $trem['nome_trem'] = trim($_POST['nome_trem'] ?? '');
    $trem['modelo_trem'] = trim($_POST['modelo_trem'] ?? '');
    $trem['localizacao_i'] = trim($_POST['localizacao_i'] ?? '');
    $trem['codigo'] = trim($_POST['codigo'] ?? '');

    // Guarda os valores originais para validar antes de converter.

    $capacidadeOriginal = trim($_POST['capacidade'] ?? '');
    $anoOriginal = trim($_POST['ano'] ?? '');

    $trem['status'] = $_POST['status'] ?? '';

    // is_numeric() verifica se a capacidade é um número.
    // (float) converte o valor para decimal.

    $trem['capacidade'] = is_numeric($capacidadeOriginal)
        ? (float) $capacidadeOriginal
        : 0;

    // filter_var() verifica se o ano é um número inteiro válido.
    // Se não for válido, retorna false.

    $anoValido = filter_var($anoOriginal, FILTER_VALIDATE_INT);

    $trem['ano'] = $anoOriginal;

    // O usuário responsável é OPCIONAL.
    // Se o campo estiver vazio, guarda null (NULL no banco).
    // Se estiver preenchido, verifica se o valor é um número inteiro.

    $usuarioSelecionado = trim($_POST['id_usuario'] ?? '');

    if ($usuarioSelecionado === '') {
        $trem['id_usuario'] = null;
    } else {
        $trem['id_usuario'] = filter_var(
            $usuarioSelecionado,
            FILTER_VALIDATE_INT
        );
    }

    // Lista dos únicos valores aceitos para o status do trem.

    $statusValidos = ['operacao', 'parado', 'manutencao', 'inativo'];

    // Faz a validação dos campos.
    // O servidor precisa validar novamente porque o required do HTML pode ser burlado.
    // || significa OU, então basta uma condição ser verdadeira para os dados serem inválidos.

    if (
        $trem['nome_trem'] === '' ||
        $trem['modelo_trem'] === '' ||
        $trem['localizacao_i'] === '' ||
        $trem['codigo'] === '' ||
        !is_numeric($capacidadeOriginal) ||
        $trem['capacidade'] <= 0 ||

        // preg_match() verifica se o ano possui exatamente 4 dígitos.

        !preg_match('/^\d{4}$/', $anoOriginal) ||
        $anoValido === false ||
        $anoValido < 1900 ||
        $anoValido > (int) date('Y') + 1 ||

        // in_array() verifica se o status está na lista permitida.
        // O true ativa a comparação estrita.

        !in_array($trem['status'], $statusValidos, true) ||

        // Se o usuário selecionou alguém, verifica se o valor é um inteiro válido.

        (
            $usuarioSelecionado !== '' &&
            ($trem['id_usuario'] === false || $trem['id_usuario'] < 1)
        )
    ) {
        $erro = 'Preencha todos os campos com valores válidos.';
        $tipoErro = 'normal';

    } else {

        // Começa assumindo que o usuário existe.
        // Se o administrador não selecionou ninguém, não precisa consultar o banco.

        $usuarioExiste = true;

        // Verifica o usuário somente se algum foi selecionado.

        if ($trem['id_usuario'] !== null) {

            // Prepara uma consulta para verificar se o usuário existe.

            $verifica = $conexao->prepare(
                "SELECT id_usuario FROM usuario WHERE id_usuario = ?"
            );

            // bind_param() associa o ID ao ?.
            // O "i" significa inteiro.

            $verifica->bind_param("i", $trem['id_usuario']);

            // Executa a consulta.

            $verifica->execute();

            // store_result() guarda o resultado na memória para contar as linhas retornadas.

            $verifica->store_result();

            // Se voltou pelo menos uma linha, significa que o usuário existe.

            $usuarioExiste = $verifica->num_rows > 0;

            // Libera os recursos da consulta.

            $verifica->close();
        }

        // Se o usuário não existe, mostra uma mensagem de erro.

        if (!$usuarioExiste) {

            $erro = 'Selecione um usuário responsável válido.';
            $tipoErro = 'normal';

        } else {

            // Verifica se o código já pertence a outro trem.
            // O id <> ? ignora o próprio trem que está sendo editado.

            $verificaCodigo = $conexao->prepare(
                "SELECT id FROM trem WHERE codigo = ? AND id <> ?"
            );

            // Associa o código e o ID do trem à consulta.
            // "s" significa texto e "i" significa inteiro.

            $verificaCodigo->bind_param("si", $trem['codigo'], $id);

            // Executa a consulta e guarda o resultado.

            $verificaCodigo->execute();
            $verificaCodigo->store_result();

            // Se voltou alguma linha, o código já pertence a outro trem.

            $codigoExiste = $verificaCodigo->num_rows > 0;

            // Libera os recursos da consulta.

            $verificaCodigo->close();

            if ($codigoExiste) {

                $erro = 'Esse código já está cadastrado em outro trem. Informe um código diferente.';
                $tipoErro = 'duplicado';

            } else {

                // SQL de atualização.
                // Cada ? será substituído por um valor na mesma ordem.
                // O WHERE id = ? garante que somente o trem selecionado seja alterado.

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

                // Prepara o comando de atualização.

                $atualizacao = $conexao->prepare($sql);

                if ($atualizacao) {

                    // Converte o ano para inteiro depois de validar.

                    $ano = (int) $anoValido;

                    // bind_param() associa os valores aos 9 ? do UPDATE.
                    // s = texto, d = decimal e i = inteiro.
                    // O id_usuario pode receber null porque a associação é opcional.

                    $atualizacao->bind_param(
                        "ssssdisii",
                        $trem['nome_trem'],
                        $trem['modelo_trem'],
                        $trem['localizacao_i'],
                        $trem['codigo'],
                        $trem['capacidade'],
                        $ano,
                        $trem['status'],
                        $trem['id_usuario'],
                        $id
                    );

                    // Executa a atualização no banco.

                    if ($atualizacao->execute()) {

                        // Se deu certo, libera os recursos e volta para a lista.

                        $atualizacao->close();

                        header('Location: lista_trem.php');
                        exit;

                    } else {

                        // Se a atualização falhou, mostra uma mensagem de erro.

                        $erro = 'Não foi possível salvar as alterações. Tente novamente.';
                        $tipoErro = 'normal';
                    }

                    // Libera os recursos do comando UPDATE.

                    $atualizacao->close();

                } else {

                    // Se não conseguiu preparar a consulta, mostra uma mensagem de erro.

                    $erro = 'Não foi possível preparar a atualização.';
                    $tipoErro = 'normal';
                }
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

            <div class="informacoes-pagina">
                <h1 class="titulo-pagina">Editar trem</h1>

                <p class="descricao-pagina">
                    Altere as informações do trem cadastrado.
                </p>
            </div>

            <br>

            <section class="painel-formulario-trem">

                <?php if ($erro !== ''): ?>
                    <p class="<?= $tipoErro === 'duplicado' ? 'erro-duplicado' : 'aviso-edicao-trem' ?>">
                        <?= htmlspecialchars($erro, ENT_QUOTES, 'UTF-8') ?>
                    </p>
                <?php endif; ?>

                <form method="post" action="edicao_trem.php?id=<?= htmlspecialchars((string) $id, ENT_QUOTES, 'UTF-8') ?>">

                    <input type="hidden" name="id" value="<?= htmlspecialchars((string) $id, ENT_QUOTES, 'UTF-8') ?>">

                    <div class="grade-formulario-trem">

                        <div class="grupo-campo-trem">
                            <label class="rotulo-campo-trem" for="nome_trem">
                                Nome do trem<span class="obrigatorio">*</span>
                            </label>

                            <input class="campo-edicao-trem" type="text"
                                id="nome_trem" name="nome_trem" maxlength="100"
                                value="<?= htmlspecialchars($trem['nome_trem'], ENT_QUOTES, 'UTF-8') ?>" required>
                        </div>

                        <div class="grupo-campo-trem">
                            <label class="rotulo-campo-trem" for="modelo_trem">
                                Modelo<span class="obrigatorio">*</span>
                            </label>

                            <input class="campo-edicao-trem" type="text"
                                id="modelo_trem" name="modelo_trem" maxlength="100"
                                value="<?= htmlspecialchars($trem['modelo_trem'], ENT_QUOTES, 'UTF-8') ?>" required>
                        </div>

                        <div class="grupo-campo-trem">
                            <label class="rotulo-campo-trem" for="localizacao_i">
                                Localização inicial<span class="obrigatorio">*</span>
                            </label>

                            <input class="campo-edicao-trem" type="text"
                                id="localizacao_i" name="localizacao_i" maxlength="150"
                                value="<?= htmlspecialchars($trem['localizacao_i'], ENT_QUOTES, 'UTF-8') ?>" required>
                        </div>

                        <div class="grupo-campo-trem">
                            <label class="rotulo-campo-trem" for="codigo">
                                Código<span class="obrigatorio">*</span>
                            </label>

                            <input class="campo-edicao-trem" type="text"
                                id="codigo" name="codigo" maxlength="30"
                                value="<?= htmlspecialchars($trem['codigo'], ENT_QUOTES, 'UTF-8') ?>" required>
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
                                        <?= htmlspecialchars($u['email'], ENT_QUOTES, 'UTF-8') ?>
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
                                value="<?= htmlspecialchars((string) $trem['capacidade'], ENT_QUOTES, 'UTF-8') ?>" required>
                        </div>

                        <div class="grupo-campo-trem campo-ano-trem">
                            <label class="rotulo-campo-trem" for="ano">
                                Ano<span class="obrigatorio">*</span>
                            </label>

                            <input class="campo-edicao-trem" type="text"
                                id="ano" name="ano" maxlength="4" pattern="[0-9]{4}"
                                value="<?= htmlspecialchars((string) $trem['ano'], ENT_QUOTES, 'UTF-8') ?>" required>
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
