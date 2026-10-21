
<?php


require_once '../../infra/verificar-admin.php';
require_once '../../infra/conexao.php';


$erro = '';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: tela-home-rotas.php');
    exit;
}

$id = (int) $_GET['id'];

$sql = "SELECT * FROM rotas WHERE id_rota = ?";

$stmt = $conexao->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();

$resultado = $stmt->get_result();

if ($resultado->num_rows == 0) {
    $stmt->close();
    header('Location: tela-home-rotas.php');
    exit;
}

$rota = $resultado->fetch_assoc();

$stmt->close();

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $sql = "DELETE FROM rotas WHERE id_rota = ?";

    $stmt = $conexao->prepare($sql);

    if ($stmt) {

        $stmt->bind_param("i", $id);

        if ($stmt->execute() && $stmt->affected_rows > 0) {
            $stmt->close();
            header('Location: tela-home-rotas.php');
            exit;
        } else {
            $erro = 'Não foi possível excluir a rota.';
        }

        $stmt->close();

    } else {
        $erro = 'Erro ao preparar a exclusão da rota.';
    }
}

?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Excluir Rota - PIA Enterprise</title>

    <link rel="stylesheet" href="../../assets/style/style.css">
</head>

<body id="home-corpo">

    <header class="barra-superior">

        <h1>PIA Enterprise</h1>

        <div class="acoes-superiores">

            <span class="ponto-online"></span>

            <span class="texto-online">Online</span>

            <span class="icone-sino"></span>

            <span class="icone-usuario"></span>

        </div>

    </header>

    <aside class="menu-lateral">

        <nav>

            <a href="../../index.php">Dashboard</a>

            <a href="../sensor/tela-home-sensor.php">Sensores</a>

            <a href="../trem/tela-home-trem.php">Trens</a>

             <?php if (isset($_SESSION['tipo']) && $_SESSION['tipo'] === 'administrador'): ?>

            <a href="public/usuario/tela-home-usuario.php">Usuários</a>

            <?php endif; ?>

        </nav>

    </aside>

    <main class="area-principal">

        <section class="cabecalho-cadastro-sensor">

            <div class="informacoes-pagina">

                <h1 class="titulo-pagina">Excluir rota</h1>

                <p class="descricao-pagina">
                    Confirme a exclusão da rota selecionada.
                </p>

            </div>

            <button
                type="button"
                class="botao-voltar-sensores"
                onclick="window.location.href='tela-home-rotas.php'">

                ← Voltar para rotas

            </button>

        </section>

        <section class="painel-excluir-sensor">

            <div class="icone-excluir-sensor">
                🗑
            </div>

            <h2>
                Tem certeza que deseja excluir esta rota?
            </h2>

            <p>
                Essa ação não poderá ser desfeita.
            </p>

            <div class="informacoes-sensor-excluir">

                <span>
                    Nome:
                    <?php echo htmlspecialchars($rota['nome_rota']); ?>
                </span>

                <span>
                    Modelo do trem:
                    <?php echo htmlspecialchars($rota['modelo_trem']); ?>
                </span>

                <span>
                    Código:
                    <?php echo htmlspecialchars($rota['codigo']); ?>
                </span>

                <span>
                    Tempo estimado:
                    <?php echo htmlspecialchars($rota['tempo_estimado']); ?>
                </span>

                <span>
                    Distância:
                    <?php echo htmlspecialchars($rota['distancia']); ?> km
                </span>

                <span>
                    Status:
                    <?php echo htmlspecialchars($rota['status']); ?>
                </span>

            </div>

            <?php if ($erro != ''): ?>

                <p class="erro-excluir-sensor">
                    <?php echo htmlspecialchars($erro); ?>
                </p>

            <?php endif; ?>

            <form method="POST" class="botoes-excluir-sensor">

                <button
                    type="submit"
                    class="botao-confirmar-exclusao">

                    Excluir rota

                </button>

                <button
                    type="button"
                    class="botao-cancelar-cadastro"
                    onclick="window.location.href='tela-home-rotas.php'">

                    Cancelar

                </button>

            </form>

        </section>

    </main>

</body>

</html>
