<?php

include'../../infra/conexao.php';

$mensagem = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $id_trem = $_POST['id_trem'];
    $nome_rota = $_POST['nome_rota'];
    $codigo = $_POST['codigo'];
    $distancia = $_POST['distancia'];
    $tempo_estimado = $_POST['tempo_estimado'];
    $capacidade_rota = $_POST['capacidade_rota'];

    $sql = "INSERT INTO rotas (id_trem, nome_rota, codigo, distancia, tempo_estimado, capacidade_rota) VALUES (?, ?, ?, ?, ?, ?)";

    $stmt = $conexao->prepare($sql);
    $stmt->bind_param("isssdd", $id_trem, $nome_rota, $codigo, $distancia, $tempo_estimado, $capacidade_rota);

    if ($stmt->execute() === TRUE) {

        header("Location: tela-home-rotas.php");
        exit;

    } else {

        $mensagem = "Erro ao cadastrar rota.";

    }
}

?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastrar </title>
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
            <a href="lista_trem.php">Trens</a>
            <a class= "ativo "href="../rotas/tela-home-rotas.php">Rotas</a>
        </nav>
    </aside>

    <main class="area-principal cadastro-trem">
        <div class="cabecalho-cadastro-sensor">
            <h2 class="titulo-pagina">Cadastrar nova rota</h2>
            <p class="descricao-pagina">Preencha as informações para cadastrar um nova rota no sistema.</p>
        </div>

        <?php if (isset($erro)): ?>
            <div class="aviso-cadastro falha"><?php echo $erro; ?></div>
        <?php endif; ?>

        <section class="painel-cadastro-sensor">
            <form method="POST">
                <div class="grade-formulario-trem">
                    <div class="grupo-campo-trem">
                        <label class="rotulo-campo-trem" for="nome_trem">Nome da rota<span class="obrigatorio">*</span></label>
                        <input type="text" id="nome_trem" name="nome_trem" placeholder="Ex.: Rota Joinville → Barra Velha" required>
                    </div>

                    <div class="grupo-campo-trem">
                        <label class="rotulo-campo-trem" for="modelo_trem">Trem<span class="obrigatorio">*</span></label>
                        <input type="text" id="modelo_trem" name="modelo_trem" placeholder="Ex.: Trem 1947" required>
                    </div>

                    <div class="grupo-campo-trem">
                        <label class="rotulo-campo-trem" for="localizacao_i">Tempo estimado<span class="obrigatorio">*</span></label>
                        <input type="text" id="localizacao_i" name="localizacao_i" placeholder="Ex.: 00h45m" required>
                    </div>

                    <div class="grupo-campo-trem">
                        <label class="rotulo-campo-trem" for="codigo">Código<span class="obrigatorio">*</span></label>
                        <input type="text" id="codigo" name="codigo" placeholder="Ex.: RT - 016" required>
                    </div>

                    <div class="grupo-campo-trem">
                        <label class="rotulo-campo-trem">Status<span class="obrigatorio">*</span></label>
                        <div class="opcoes-status-trem">
                            <label class="opcao-status-trem">
                                <input type="radio" name="status" value="operacao" checked>
                                <span class="marcador-status"></span>
                                Ativa
                            </label>

                            <label class="opcao-status-trem">
                                <input type="radio" name="status" value="inativo">
                                <span class="marcador-status"></span>
                                Inativa
                            </label>
                        </div>
                    </div>

                    <div class="grupo-campo-trem">
                        <label class="rotulo-campo-trem" for="capacidade">Distância<span class="obrigatorio">*</span></label>
                        <input type="number" id="capacidade" name="capacidade" placeholder="Ex.: 50km" required>
                    </div>

                <div class="botoes-cadastro-trem">
                    <button type="submit" class="botao-salvar-trem">Salvar rota</button>
                    <a href="tela-home-rotas.php" class="botao-voltar-trem">← Voltar para rotas</a>
                </div>
            </form>
        </section>
    </main>
</body>
</html>