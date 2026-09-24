<?php
include '../../infra/conexao.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome_trem = $_POST['nome_trem'];
    $modelo_trem = $_POST['modelo_trem'];
    $localizacao_i = $_POST['localizacao_i'];
    $codigo = $_POST['codigo'];
    $capacidade = $_POST['capacidade'];
    $ano = $_POST['ano'];
    $status = $_POST['status'];

    $sql = "INSERT INTO trem (nome_trem, modelo_trem, localizacao_i, codigo, capacidade, ano, status)
            VALUES (?, ?, ?, ?, ?, ?, ?)";

    $stmt = $conexao->prepare($sql);

    if ($stmt) {
        $stmt->bind_param("ssssiss", $nome_trem, $modelo_trem, $localizacao_i, $codigo, $capacidade, $ano, $status);

        if ($stmt->execute()) {
            header("Location: lista_trem.php");
            exit;
        }

        $erro = "Não foi possível cadastrar o trem.";
        $stmt->close();
    } else {
        $erro = "Erro ao preparar o cadastro.";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastrar Trem</title>
    <link rel="stylesheet" href="../../assets/style/style.css">
</head>

<body>
    <header class="barra-superior">
        <h1>PIA Enterprise</h1>
    </header>

    <aside class="menu-lateral">
        <nav>
            <a href="../../index.php">Dashboard</a>
            <a href="../sensor/tela-home-sensor.php">Sensores</a>
            <a class="ativo" href="lista_trem.php">Trens</a>
            <a href="../rotas/tela-home-rotas.php">Rotas</a>
        </nav>
    </aside>

    <main class="area-principal cadastro-trem">
        <div class="cabecalho-cadastro-sensor">
            <h2 class="titulo-pagina">Cadastrar novo trem</h2>
            <p class="descricao-pagina">Preencha as informações para cadastrar um novo trem no sistema.</p>
        </div>

        <?php if (isset($erro)): ?>
            <div class="aviso-cadastro falha"><?php echo $erro; ?></div>
        <?php endif; ?>

        <section class="painel-cadastro-sensor">
            <form method="POST">
                <div class="grade-formulario-trem">
                    <div class="grupo-campo-trem">
                        <label class="rotulo-campo-trem" for="nome_trem">Nome do trem<span class="obrigatorio">*</span></label>
                        <input type="text" id="nome_trem" name="nome_trem" placeholder="Ex.: Paloschi trem" required>
                    </div>

                    <div class="grupo-campo-trem">
                        <label class="rotulo-campo-trem" for="modelo_trem">Modelo<span class="obrigatorio">*</span></label>
                        <input type="text" id="modelo_trem" name="modelo_trem" placeholder="Ex.: Velocidade Trem A" required>
                    </div>

                    <div class="grupo-campo-trem">
                        <label class="rotulo-campo-trem" for="localizacao_i">Localização Inicial<span class="obrigatorio">*</span></label>
                        <input type="text" id="localizacao_i" name="localizacao_i" placeholder="Ex.: Estação Central" required>
                    </div>

                    <div class="grupo-campo-trem">
                        <label class="rotulo-campo-trem" for="codigo">Código<span class="obrigatorio">*</span></label>
                        <input type="text" id="codigo" name="codigo" placeholder="Ex.: TREM-001" required>
                    </div>

                    <div class="grupo-campo-trem">
                        <label class="rotulo-campo-trem">Status Inicial<span class="obrigatorio">*</span></label>
                        <div class="opcoes-status-trem">
                            <label class="opcao-status-trem">
                                <input type="radio" name="status" value="operacao" checked>
                                <span class="marcador-status"></span>
                                Em operação
                            </label>

                            <label class="opcao-status-trem">
                                <input type="radio" name="status" value="parado">
                                <span class="marcador-status"></span>
                                Parado
                            </label>

                            <label class="opcao-status-trem">
                                <input type="radio" name="status" value="manutencao">
                                <span class="marcador-status"></span>
                                Manutenção
                            </label>

                            <label class="opcao-status-trem">
                                <input type="radio" name="status" value="inativo">
                                <span class="marcador-status"></span>
                                Inativo
                            </label>
                        </div>
                    </div>

                    <div class="grupo-campo-trem">
                        <label class="rotulo-campo-trem" for="capacidade">Capacidade<span class="obrigatorio">*</span></label>
                        <input type="number" id="capacidade" name="capacidade" placeholder="Ex.: 300" required>
                    </div>

                    <div class="grupo-campo-trem">
                        <label class="rotulo-campo-trem" for="ano">Ano<span class="obrigatorio">*</span></label>
                        <input type="number" id="ano" name="ano" placeholder="Ex.: 2026" required>
                    </div>
                </div>

                <div class="botoes-cadastro-trem">
                    <button type="submit" class="botao-salvar-trem">Salvar trem</button>
                    <a href="lista_trem.php" class="botao-voltar-trem">← Voltar para trens</a>
                </div>
            </form>
        </section>
    </main>
</body>
</html>