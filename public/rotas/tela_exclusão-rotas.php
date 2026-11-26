
<?php

// Verifica se o usuário é administrador e conecta ao banco de dados
require_once '../../infra/verificar-admin.php';
require_once '../../infra/conexao.php';

// Variável para armazenar mensagens de erro
$erro = '';

// Verifica se o ID da rota foi informado corretamente pela URL
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

// Se o ID for inválido, volta para a página principal
if (!$id || $id < 1) {
    header('Location: tela-home-rotas.php');
    exit;
}

// Busca os dados da rota no banco de dados
$sql = "SELECT * FROM rotas WHERE id_rota = ?";

$stmt = $conexao->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();

// Guarda o resultado da consulta
$resultado = $stmt->get_result();

// Verifica se a rota existe
if ($resultado->num_rows === 0) {
    $stmt->close();
    header('Location: tela-home-rotas.php');
    exit;
}

// Armazena os dados da rota encontrada
$rota = $resultado->fetch_assoc();
$stmt->close();

// Verifica se o formulário de exclusão foi enviado
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // Verifica se existe um trem associado à rota
    if ($rota['id_trem'] !== null) {

        // Impede a exclusão caso exista um trem associado
        $erro = 'Não é possível excluir esta rota, pois existe um trem vinculado a ela.';

    } else {

        // Verifica se existem sensores vinculados à rota
        $sql_verificar = "SELECT COUNT(*) AS total FROM sensor WHERE id_rota = ?";

        $stmt_verificar = $conexao->prepare($sql_verificar);
        $stmt_verificar->bind_param("i", $id);
        $stmt_verificar->execute();

        // Guarda o resultado da verificação
        $resultado_verificar = $stmt_verificar->get_result();
        $dados = $resultado_verificar->fetch_assoc();

        $stmt_verificar->close();

        // Impede a exclusão caso existam sensores associados
        if ($dados['total'] > 0) {

            $erro = 'Não é possível excluir esta rota, pois existem sensores vinculados a ela.';

        } else {

            // Comando SQL para excluir a rota pelo ID
            $sql = "DELETE FROM rotas WHERE id_rota = ? AND id_trem IS NULL";

            // Prepara o comando de exclusão
            $stmt = $conexao->prepare($sql);

            if ($stmt) {

                $stmt->bind_param("i", $id);

                // Executa a exclusão e verifica se algum registro foi removido
                if ($stmt->execute() && $stmt->affected_rows > 0) {

                    $stmt->close();

                    // Volta para a página principal das rotas
                    header('Location: tela-home-rotas.php');
                    exit;

                } else {

                    $erro = 'Não foi possível excluir a rota. Verifique se existe algum trem ou sensor associado.';
                }

                // Fecha o comando SQL
                $stmt->close();

            } else {

                // Informa caso não seja possível preparar a exclusão
                $erro = 'Erro ao preparar a exclusão da rota.';
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

    <title>Excluir Rota - PIA Enterprise</title>

    <link rel="stylesheet" href="../../assets/style/style.css">
</head>

<body id="home-corpo">

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

    <main class="area-principal">

        <section class="cabecalho-cadastro-sensor">

            <div class="informacoes-pagina">

                <h1 class="titulo-pagina">Excluir rota</h1>

                <p class="descricao-sensores">
                    Confirme a exclusão da rota selecionada.
                </p>

            </div>

            <button
                type="button"
                class="botao-voltar-sensores"
                onclick="window.location.href='tela-home-rotas.php'">

                Voltar para rotas

            </button>

        </section>

        <section class="painel-excluir-sensor">

            <div class="icone-excluir-sensor">
                
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
                    <?= htmlspecialchars($rota['nome_rota'], ENT_QUOTES, 'UTF-8'); ?>
                </span>
                <br>

                <span>
                    Modelo do trem:
                    <?= htmlspecialchars($rota['modelo_trem'] ?? '', ENT_QUOTES, 'UTF-8'); ?>
                </span>
                <br>

                <span>
                    Código:
                    <?= htmlspecialchars($rota['codigo'], ENT_QUOTES, 'UTF-8'); ?>
                </span>
                <br>

                <span>
                    Tempo estimado:
                    <?= htmlspecialchars($rota['tempo_estimado'], ENT_QUOTES, 'UTF-8'); ?>
                </span>
                <br>

                <span>
                    Distância:
                    <?= htmlspecialchars((string) $rota['distancia'], ENT_QUOTES, 'UTF-8'); ?> km
                </span>
                <br>

                <span>
                    Status:
                    <?= htmlspecialchars($rota['status'], ENT_QUOTES, 'UTF-8'); ?>
                </span>

            </div>

            <?php if ($erro !== ''): ?>

                <p class="erro-excluir-sensor">
                    <?= htmlspecialchars($erro, ENT_QUOTES, 'UTF-8'); ?>
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
