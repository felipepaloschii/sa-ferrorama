
<?php

// Verifica se o usuário é administrador e conecta ao banco de dados.
require_once '../../infra/verificar-admin.php';
require_once '../../infra/conexao.php';

// Variáveis para armazenar mensagens e os trens cadastrados.
$mensagem = '';
$tipoErro = '';
$trens = [];

// Recebe e valida o ID da rota pela URL.
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

// Se o ID for inválido, volta para a página principal das rotas.
if (!$id || $id < 1) {
    header('Location: tela-home-rotas.php');
    exit;
}

// Busca os trens cadastrados para preencher o campo de seleção.
$consultaTrens = $conexao->query(
    "SELECT id, nome_trem, modelo_trem
     FROM trem
     ORDER BY nome_trem"
);

// Armazena os trens encontrados.
if ($consultaTrens) {
    $trens = $consultaTrens->fetch_all(MYSQLI_ASSOC);
}

// Busca os dados da rota que será editada.
$sql = "SELECT * FROM rotas WHERE id_rota = ?";
$stmt = $conexao->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();

// Guarda os dados encontrados em uma variável.
$resultado = $stmt->get_result();
$rota = $resultado->fetch_assoc();
$stmt->close();

// Se a rota não existir, volta para a página principal.
if (!$rota) {
    header('Location: tela-home-rotas.php');
    exit;
}

// Verifica se o formulário foi enviado.
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Recebe os dados preenchidos no formulário.
    $nome_rota = trim($_POST['nome_rota'] ?? '');
    $id_trem = (int) ($_POST['id_trem'] ?? 0);
    $tempo_estimado = trim($_POST['tempo_estimado'] ?? '');
    $codigo = trim($_POST['codigo'] ?? '');
    $distancia = (float) ($_POST['distancia'] ?? 0);
    $status = $_POST['status'] ?? '';

    // Busca o modelo do trem selecionado.
    $consultaTrem = $conexao->prepare(
        "SELECT modelo_trem FROM trem WHERE id = ?"
    );

    $consultaTrem->bind_param("i", $id_trem);
    $consultaTrem->execute();

    // Armazena os dados do trem encontrado.
    $resultadoTrem = $consultaTrem->get_result();
    $tremSelecionado = $resultadoTrem->fetch_assoc();
    $consultaTrem->close();

    // Verifica se os campos foram preenchidos corretamente.
    if (
        $nome_rota !== '' &&
        $tremSelecionado &&
        $tempo_estimado !== '' &&
        $codigo !== '' &&
        $distancia > 0 &&
        in_array($status, ['Ativa', 'Inativa'], true)
    ) {

        // Recupera o modelo do trem selecionado.
        $modelo_trem = $tremSelecionado['modelo_trem'];

        // Comando SQL para atualizar os dados da rota.
        $sql = "UPDATE rotas SET
                    id_trem = ?,
                    nome_rota = ?,
                    modelo_trem = ?,
                    tempo_estimado = ?,
                    codigo = ?,
                    distancia = ?,
                    status = ?
                WHERE id_rota = ?";

        // Prepara o comando de atualização.
        $atualizacao = $conexao->prepare($sql);

        // Define os tipos e os valores que serão enviados ao banco.
        $atualizacao->bind_param(
            "issssdsi",
            $id_trem,
            $nome_rota,
            $modelo_trem,
            $tempo_estimado,
            $codigo,
            $distancia,
            $status,
            $id
        );

        try {

            // Executa a atualização dos dados.
            if ($atualizacao->execute()) {

                // Fecha o comando e volta para a lista de rotas.
                $atualizacao->close();
                header('Location: tela-home-rotas.php');
                exit;
            }

        } catch (mysqli_sql_exception $e) {

            // Verifica se o código informado já pertence a outra rota.
            if ($e->getCode() === 1062) {
                $mensagem = 'Esse código já está cadastrado em outra rota. Informe um código diferente.';
                $tipoErro = 'duplicado';

            } else {

                // Exibe uma mensagem caso ocorra outro erro.
                $mensagem = 'Não foi possível atualizar a rota.';
            }
        }

        $atualizacao->close();

        // Atualiza os dados exibidos na tela com os valores enviados.
        $rota['id_trem'] = $id_trem;
        $rota['nome_rota'] = $nome_rota;
        $rota['modelo_trem'] = $modelo_trem;
        $rota['tempo_estimado'] = $tempo_estimado;
        $rota['codigo'] = $codigo;
        $rota['distancia'] = $distancia;
        $rota['status'] = $status;

    } else {

        // Exibe uma mensagem caso algum campo esteja incorreto.
        $mensagem = 'Preencha todos os campos corretamente e selecione um trem válido.';

        // Mantém os valores preenchidos para não perder os dados.
        $rota['id_trem'] = $id_trem;
        $rota['nome_rota'] = $nome_rota;
        $rota['tempo_estimado'] = $tempo_estimado;
        $rota['codigo'] = $codigo;
        $rota['distancia'] = $distancia;
        $rota['status'] = $status;
    }
}

// Função para proteger os valores exibidos na página contra códigos HTML.
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

    <title>Editar Rotas - PIA Enterprise</title>

    <link rel="stylesheet" href="../../assets/style/style.css">
</head>

<body class="estrutura-dashboard">

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

    <main class="conteudo-edicao-trem">

         <div class="informacoes-pagina">
            <h1 class="titulo-pagina">Editar rota</h1>

            <p class="descricao-pagina">
                Altere as informações da rota cadastrada.
            </p>
        </div>
        <br>
        <section class="painel-formulario-trem">

            <?php if ($mensagem !== ''): ?>
                <p class="<?= $tipoErro === 'duplicado' ? 'erro-duplicado' : 'aviso-edicao-trem' ?>">
                    <?= e($mensagem) ?>
                </p>
            <?php endif; ?>

            <form method="POST">

                <div class="grade-formulario-trem">

                    <div>

                        <div class="grupo-campo-trem">
                            <label class="rotulo-campo-trem" for="nome_rota">
                                Nome da rota <span class="obrigatorio">*</span>
                            </label>

                            <input
                                class="campo-edicao-trem"
                                type="text"
                                id="nome_rota"
                                name="nome_rota"
                                placeholder="Ex.: Rota Joinville → Barra Velha"
                                value="<?= e($rota['nome_rota']) ?>"
                                required
                            >
                        </div>

                        <div class="grupo-campo-trem">
                            <label class="rotulo-campo-trem" for="tempo_estimado">
                                Tempo estimado <span class="obrigatorio">*</span>
                            </label>

                            <input
                                class="campo-edicao-trem"
                                type="text"
                                id="tempo_estimado"
                                name="tempo_estimado"
                                placeholder="Ex.: 00h45m"
                                value="<?= e($rota['tempo_estimado']) ?>"
                                required
                            >
                        </div>

                        <div class="grupo-campo-trem">
                            <label class="rotulo-campo-trem">
                                Status inicial <span class="obrigatorio">*</span>
                            </label>

                            <div class="opcoes-status-trem">

                                <label class="opcao-status-trem">
                                    <input
                                        type="radio"
                                        name="status"
                                        value="Ativa"
                                        <?= ($rota['status'] === 'Ativa') ? 'checked' : '' ?>
                                        required
                                    >
                                    <span class="marcador-status"></span>
                                    Ativa
                                </label>

                                <label class="opcao-status-trem">
                                    <input
                                        type="radio"
                                        name="status"
                                        value="Inativa"
                                        <?= ($rota['status'] === 'Inativa') ? 'checked' : '' ?>
                                    >
                                    <span class="marcador-status"></span>
                                    Inativa
                                </label>

                            </div>
                        </div>

                    </div>

                    <div>

                        <div class="grupo-campo-trem">
                            <label class="rotulo-campo-trem" for="id_trem">
                                Trem responsável <span class="obrigatorio">*</span>
                            </label>

                            <select
                                id="id_trem"
                                name="id_trem"
                                class="select-usuario"
                                required
                            >
                                <option value="">Selecione um trem...</option>

                                <?php foreach ($trens as $t): ?>
                                    <option
                                        value="<?= (int) $t['id'] ?>"
                                        <?= ((int) ($rota['id_trem'] ?? 0) === (int) $t['id']) ? 'selected' : '' ?>
                                    >
                                        <?= e($t['nome_trem']) ?>
                                        (<?= e($t['modelo_trem']) ?>)
                                    </option>
                                <?php endforeach; ?>

                            </select>
                        </div>

                        <div class="grupo-campo-trem">
                            <label class="rotulo-campo-trem" for="codigo">
                                Código <span class="obrigatorio">*</span>
                            </label>

                            <input
                                class="campo-edicao-trem"
                                type="text"
                                id="codigo"
                                name="codigo"
                                placeholder="Ex.: RT-016"
                                value="<?= e($rota['codigo']) ?>"
                                required
                            >
                        </div>

                        <div class="grupo-campo-trem">
                            <label class="rotulo-campo-trem" for="distancia">
                                Distância (km) <span class="obrigatorio">*</span>
                            </label>

                            <input
                                class="campo-edicao-trem"
                                type="number"
                                id="distancia"
                                name="distancia"
                                placeholder="Ex.: 50 km"
                                step="0.1"
                                min="0.1"
                                value="<?= e($rota['distancia']) ?>"
                                required
                            >
                        </div>

                    </div>

                </div>

                <div class="acoes-edicao-trem">

                    <a
                        href="tela-home-rotas.php"
                        class="botao-cancelar-cadastro"
                    >
                         Voltar para rotas
                    </a>

                    <button
                        type="submit"
                        class="botao-salvar-sensor"
                    >
                        Salvar alterações
                    </button>

                </div>

            </form>

        </section>

    </main>

</body>
</html>
