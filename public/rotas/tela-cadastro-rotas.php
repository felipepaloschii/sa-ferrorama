
<?php
require_once '../../infra/verificar-admin.php';
require_once '../../infra/conexao.php';

$erro = '';
$trens = [];

$consultaTrens = $conexao->query(
    "SELECT id, nome_trem, modelo_trem
     FROM trem
     ORDER BY nome_trem"
);

if ($consultaTrens) {
    $trens = $consultaTrens->fetch_all(MYSQLI_ASSOC);
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $nome_rota = trim($_POST['nome_rota'] ?? '');
    $id_trem = (int) ($_POST['id_trem'] ?? 0);
    $tempo_estimado = trim($_POST['tempo_estimado'] ?? '');
    $codigo = trim($_POST['codigo'] ?? '');
    $distancia = (float) ($_POST['distancia'] ?? 0);
    $status = $_POST['status'] ?? '';

    $consulta = $conexao->prepare(
        "SELECT modelo_trem FROM trem WHERE id = ?"
    );

    $consulta->bind_param("i", $id_trem);
    $consulta->execute();

    $resultadoTrem = $consulta->get_result();
    $trem = $resultadoTrem->fetch_assoc();
    $consulta->close();

    if (
        $nome_rota === '' ||
        !$trem ||
        $tempo_estimado === '' ||
        $codigo === '' ||
        $distancia <= 0 ||
        !in_array($status, ['Ativa', 'Inativa'], true)
    ) {
        $erro = "Preencha todos os campos corretamente.";
    } else {

        $modelo_trem = $trem['modelo_trem'];

        $sql = "INSERT INTO rotas
        (id_trem, nome_rota, modelo_trem, tempo_estimado, codigo, distancia, status)
        VALUES (?, ?, ?, ?, ?, ?, ?)";

        $stmt = $conexao->prepare($sql);

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

        if ($stmt->execute()) {
            header("Location: tela-home-rotas.php");
            exit;
        } else {
            $erro = "Erro ao cadastrar rota. Verifique se o código já existe.";
        }

        $stmt->close();
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
                ← Voltar para rotas
            </a>

        </section>

        <?php if ($erro !== ''): ?>
            <div class="aviso-cadastro falha">
                <?= htmlspecialchars($erro) ?>
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
                            value="<?= htmlspecialchars($_POST['nome_rota'] ?? '') ?>"
                            required
                        >
                    </div>

                    <div class="grupo-campo-trem">
                        <label class="rotulo-campo-trem" for="id_trem">
                            Trem responsável<span class="obrigatorio">*</span>
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
                                    <?= ((int) ($_POST['id_trem'] ?? 0) === (int) $t['id']) ? 'selected' : '' ?>
                                >
                                    <?= htmlspecialchars($t['nome_trem']) ?>
                                    (<?= htmlspecialchars($t['modelo_trem']) ?>)
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
                            value="<?= htmlspecialchars($_POST['tempo_estimado'] ?? '') ?>"
                            required
                        >
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
                            value="<?= htmlspecialchars($_POST['codigo'] ?? '') ?>"
                            required
                        >
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
                                >
                                <span class="marcador-status"></span>
                                Ativa
                            </label>

                            <label class="opcao-status-trem">
                                <input
                                    type="radio"
                                    name="status"
                                    value="Inativa"
                                    <?= ($_POST['status'] ?? '') === 'Inativa' ? 'checked' : '' ?>
                                >
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
                            value="<?= htmlspecialchars($_POST['distancia'] ?? '') ?>"
                            required
                        >
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
