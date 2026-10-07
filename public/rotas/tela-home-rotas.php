<?php

include '../../infra/conexao.php';

$rotas = [];

$sql = "SELECT * FROM rotas ORDER BY nome_rota DESC";

$resultado = $conexao->query($sql);

if ($resultado) {
    while ($rota = $resultado->fetch_assoc()) {
        $rotas[] = $rota;
    }
}

?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Listagem de Rotas</title>
    <link rel="stylesheet" href="../../assets/style/style.css">
</head>

<body>

    <header class="barra-superior">
        <h1>PIA Enterprise</h1>
    </header>

    <aside class="menu-lateral">
        <nav>
            <a href="../../index.php">Dashboard</a>
            <a href="tela-home-sensor.php">Sensores</a>
            <a href="../trem/lista_trem.php">Trens</a>
            <a class="ativo" href="tela-home-rotas.php">Rotas</a>
            <a href="../usuario/tela-home-usuario.php">Usuários</a>
        </nav>
    </aside>

    <main class="area-principal">

        <section class="cabecalho-sensor topo-lista-trens">
            <h2 class="titulo-sensores">Listagem de rotas</h2>

            <a class="botao-novo-sensor botao-novo-trem"
               href="tela-cadastro-rotas.php">
                + Nova Rota
            </a>
        </section>

        <section class="painel-lista-sensores painel-lista-trens">

            <div class="tabela-sensores">

                <div class="cabecalho-tabela colunas-trens">
                    <span>ID</span>
                    <span>Nome</span>
                    <span>Trem</span>
                    <span>Tempo estimado</span>
                    <span>Código</span>
                    <span>Distância</span>
                    <span>Ações</span>
                </div>

                <?php if (count($rotas) > 0): ?>

                    <?php foreach ($rotas as $rota): ?>

                        <div class="linha-tabela colunas-trens">

                            <span>
                                 <?= htmlspecialchars($rota['id_rota']) ?>
                            </span>

                            <span>
                                <?= htmlspecialchars($rota['nome_rota']) ?>
                            </span>

                            <span>
                                <?= htmlspecialchars($rota['modelo_trem']) ?>
                            </span>

                            <span>
                                <?= htmlspecialchars($rota['tempo_estimado']) ?>
                            </span>

                            <span>
                                <?= htmlspecialchars($rota['codigo']) ?>
                            </span>

                            <span>
                                <?= htmlspecialchars($rota['distancia']) ?>
                            </span>

                        </div>

                        <span class="acoes">

                    <a href="tela-edit-usuario.php?id=<?php echo $usuario['id_rota']; ?>"
                   class="botao-editar"
                   title="Editar">✎</a>

                    <a href="tela-excluir-usuario.php?id=<?php echo $usuario['id_rota']; ?>"
                    class="botao-excluir"
                    title="Excluir">🗑</a>

            </span>

                    <?php endforeach; ?>

                <?php else: ?>

                    <p class="nenhum-sensor">
                        Nenhuma rota cadastrada.
                    </p>

                <?php endif; ?>

            </div>

        </section>

    </main>

</body>

</html>