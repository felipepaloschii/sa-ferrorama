<?php

// Verifica se o usuário é administrador e conecta ao banco de dados
require_once '../../infra/verificar-admin.php';
require_once '../../infra/conexao.php';

// Variáveis para armazenar mensagens de erro e os trens cadastrados
$erro = '';
$tipoErro = '';
$trens = [];

// Busca os trens cadastrados para exibi-los no campo de seleção
$consultaTrens = $conexao->query(
    "SELECT id, nome_trem, modelo_trem
     FROM trem
     ORDER BY nome_trem"
);

// Armazena os trens encontrados em uma lista
if ($consultaTrens) {
    $trens = $consultaTrens->fetch_all(MYSQLI_ASSOC);
}

// Verifica se o formulário foi enviado
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // Recebe os dados preenchidos e remove espaços extras
    $nome_rota = trim($_POST['nome_rota'] ?? '');
    $tempo_estimado = trim($_POST['tempo_estimado'] ?? '');
    $codigo = trim($_POST['codigo'] ?? '');
    $distancia = (float) ($_POST['distancia'] ?? 0);
    $status = $_POST['status'] ?? '';

    // Começa sem nenhum trem associado
    $id_trem = null;
    $modelo_trem = '';
    $trem = null;
    $tremExiste = true;
    $idTremInvalido = false;

    // Verifica se algum trem foi selecionado
    if (isset($_POST['id_trem']) && $_POST['id_trem'] !== '') {

        // Verifica se o ID informado é um número inteiro válido
        $id_trem = filter_var($_POST['id_trem'], FILTER_VALIDATE_INT);

        // Verifica se o ID é inválido ou menor que um
        if ($id_trem === false || $id_trem < 1) {
            $idTremInvalido = true;
            $tremExiste = false;
        }
    }

    // Se um trem foi selecionado e o ID é válido, verifica se ele existe
    if ($id_trem !== null && !$idTremInvalido) {

        // Prepara a consulta para buscar o modelo do trem
        $consulta = $conexao->prepare(
            "SELECT modelo_trem FROM trem WHERE id = ?"
        );

        $consulta->bind_param("i", $id_trem);
        $consulta->execute();

        // Guarda as informações do trem encontrado
        $resultadoTrem = $consulta->get_result();
        $trem = $resultadoTrem->fetch_assoc();

        $consulta->close();

        // Verifica se o trem foi encontrado
        $tremExiste = (bool) $trem;

        // Recupera o modelo do trem selecionado
        if ($tremExiste) {
            $modelo_trem = $trem['modelo_trem'];
        }
    }

    // Verifica se todos os campos obrigatórios foram preenchidos corretamente
    // A associação com um trem não é obrigatória
    if (
        $nome_rota === '' ||
        !$tremExiste ||
        $idTremInvalido ||
        $tempo_estimado === '' ||
        $codigo === '' ||
        $distancia <= 0 ||
        !in_array($status, ['Ativa', 'Inativa'], true)
    ) {

        $erro = 'Preencha todos os campos obrigatórios corretamente.';

    } else {

        // Verifica se o código já está cadastrado em outra rota
        $consultaCodigo = $conexao->prepare(
            "SELECT id_rota FROM rotas WHERE codigo = ?"
        );

        $consultaCodigo->bind_param("s", $codigo);
        $consultaCodigo->execute();

        // Verifica se encontrou uma rota com o mesmo código
        $resultadoCodigo = $consultaCodigo->get_result();
        $codigoExiste = $resultadoCodigo->num_rows > 0;

        $consultaCodigo->close();

        if ($codigoExiste) {

            // Exibe uma mensagem caso o código já esteja cadastrado
            $erro = 'Esse código já está cadastrado em outra rota. Informe um código diferente.';
            $tipoErro = 'duplicado';

        } else {

            // Comando SQL para inserir uma nova rota no banco de dados
            $sql = "INSERT INTO rotas
                    (id_trem, nome_rota, modelo_trem, tempo_estimado,
                     codigo, distancia, status)
                    VALUES (?, ?, ?, ?, ?, ?, ?)";

            // Prepara o comando SQL
            $stmt = $conexao->prepare($sql);

            if ($stmt) {

                // Define os tipos e envia os valores para o banco
                // O valor null em id_trem representa uma rota sem trem associado
                $stmt->bind_param(
                    "issssds",
                    $id_trem,
                    $nome_rota,
                    $modelo_trem,
                    $tempo_estimado,
                    $codigo,
                    $distancia,
                    $status
                );

                // Executa o cadastro e verifica se deu certo
                if ($stmt->execute()) {

                    // Fecha o comando e volta para a página principal das rotas
                    $stmt->close();

                    header("Location: tela-home-rotas.php");
                    exit;

                } else {

                    // Exibe uma mensagem caso não consiga cadastrar
                    $erro = 'Não foi possível cadastrar a rota.';
                }

                // Fecha o comando SQL
                $stmt->close();

            } else {

                // Informa caso não seja possível preparar o comando SQL
                $erro = 'Erro ao preparar o cadastro da rota.';
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

    <title>Cadastrar rota - PIA Enterprise</title>

    <link rel="stylesheet" href="../../assets/style/style.css">
</head>

<body>

    <header class="barra-superior">
        <h1>PIA Enterprise</h1>

        <nav class="navegacao-principal">
            <a href="../../index.php">Dashboard</a>
            <a href="../sensor/tela-home-sensor.php">Sensores</a>
            <a href="../trem/lista_trem.php">Trens</a>
            <a class="ativo" href="tela-home-rotas.php">Rotas</a>

            <?php if (isset($_SESSION['tipo']) && $_SESSION['tipo'] === 'administrador'): ?>
                <a href="../usuario/tela-home-usuario.php">Usuários</a>
            <?php endif; ?>
        </nav>
    </header>

    <main class="area-principal cadastro-rota">

        <section class="cabecalho-cadastro-sensor">

            <div class="informacoes-pagina">
                <h1 class="titulo-pagina">Cadastrar nova rota</h1>

                <p class="descricao-pagina">
                    Preencha as informações para cadastrar uma nova rota no sistema.
                </p>
            </div>

            <a href="tela-home-rotas.php" class="botao-voltar-sensores">
                Voltar para rotas
            </a>

        </section>

        <?php if ($erro !== ''): ?>
            <div class="<?= $tipoErro === 'duplicado' ? 'erro-duplicado' : 'aviso-cadastro falha' ?>">
                <?= htmlspecialchars($erro, ENT_QUOTES, 'UTF-8') ?>
            </div>
        <?php endif; ?>

        <section class="painel-cadastro-sensor">

            <form action="tela-cadastro-rotas.php" method="POST">

                <div class="grade-formulario-trem">

                    <div class="grupo-campo-trem">
                        <label class="rotulo-campo-trem" for="nome_rota">
                            Nome da rota<span class="obrigatorio">*</span>
                        </label>

                        <input
                            type="text"
                            id="nome_rota"
                            name="nome_rota"
                            placeholder="Ex.: Rota Joinville → Barra Velha"
                            value="<?= htmlspecialchars($_POST['nome_rota'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                            required>
                    </div>

                    <div class="grupo-campo-trem">
                        <label class="rotulo-campo-trem" for="id_trem">
                            Trem responsável (opcional)
                        </label>

                        <select
                            id="id_trem"
                            name="id_trem"
                            class="select-usuario">

                            <option value="">Sem trem associado</option>

                            <?php foreach ($trens as $t): ?>
                                <option
                                    value="<?= (int) $t['id'] ?>"
                                    <?= (($_POST['id_trem'] ?? '') == $t['id']) ? 'selected' : '' ?>>

                                    <?= htmlspecialchars($t['nome_trem'], ENT_QUOTES, 'UTF-8') ?>
                                    (<?= htmlspecialchars($t['modelo_trem'], ENT_QUOTES, 'UTF-8') ?>)

                                </option>
                            <?php endforeach; ?>

                        </select>
                    </div>

                    <div class="grupo-campo-trem">
                        <label class="rotulo-campo-trem" for="tempo_estimado">
                            Tempo estimado (Min)<span class="obrigatorio">*</span>
                        </label>

                        <input
                            type="text"
                            id="tempo_estimado"
                            name="tempo_estimado"
                            placeholder="Ex.: 45 minutos"
                            value="<?= htmlspecialchars($_POST['tempo_estimado'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
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
                            placeholder="Ex.: RT-016"
                            value="<?= htmlspecialchars($_POST['codigo'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                            required>
                    </div>

                    <div class="grupo-campo-trem">
                        <label class="rotulo-campo-trem">
                            Status<span class="obrigatorio">*</span>
                        </label>

                        <div class="opcoes-status-trem">

                            <label class="opcao-status-trem">
                                <input
                                    type="radio"
                                    name="status"
                                    value="Ativa"
                                    <?= ($_POST['status'] ?? 'Ativa') === 'Ativa' ? 'checked' : '' ?>
                                    required>

                                <span class="marcador-status"></span>
                                Ativa
                            </label>

                            <label class="opcao-status-trem">
                                <input
                                    type="radio"
                                    name="status"
                                    value="Inativa"
                                    <?= ($_POST['status'] ?? '') === 'Inativa' ? 'checked' : '' ?>>

                                <span class="marcador-status"></span>
                                Inativa
                            </label>

                        </div>
                    </div>

                    <div class="grupo-campo-trem">
                        <label class="rotulo-campo-trem" for="distancia">
                            Distância (km)<span class="obrigatorio">*</span>
                        </label>

                        <input
                            type="number"
                            id="distancia"
                            name="distancia"
                            step="0.1"
                            min="0.1"
                            placeholder="Ex.: 50"
                            value="<?= htmlspecialchars($_POST['distancia'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                            required>
                    </div>

                </div>

                <div class="botoes-cadastro-trem botoes-cadastro-sensor">

                    <button type="submit" class="botao-salvar-trem">
                        Salvar rota
                    </button>

                    <a href="tela-home-rotas.php" class="botao-cancelar-trem">
                        Cancelar
                    </a>

                </div>

            </form>

        </section>

    </main>

</body>
</html>
```