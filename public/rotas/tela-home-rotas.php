<?php


if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

if (
    !isset($_SESSION['id_usuario']) ||
    !isset($_SESSION['tipo'])
) {
    header('Location: ../usuario/tela-login.php');
    exit;
}

require_once '../../infra/conexao.php';


$resultado = mysqli_query(
    $conexao,
    "SELECT * FROM rotas ORDER BY id_rota"
);

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

            <a href="../../index.php">
                Dashboard
            </a>

            <a href="../sensor/tela-home-sensor.php">
                Sensores
            </a>

            <a href="../trem/lista_trem.php">
                Trens
            </a>

            <a class="ativo" href="tela-home-rotas.php">
                Rotas
            </a>

            <?php if (isset($_SESSION['tipo']) && $_SESSION['tipo'] === 'administrador'): ?>

            <a href="public/usuario/tela-home-usuario.php">Usuários</a>

            <?php endif; ?>

        </nav>

    </aside>


    <main class="area-principal">

        <section class="cabecalho-sensor topo-lista-trens">

            <h2 class="titulo-sensores">
                Listagem de rotas
            </h2>

            <?php if ($_SESSION['tipo'] === 'administrador'): ?>
            
            <a
                class="botao-novo-sensor botao-novo-trem"
                href="tela-cadastro-rotas.php"
            >
                + Nova Rota
            </a>

            <?php endif; ?>

        </section>


        <section class="painel-lista-sensores painel-lista-trens">

            <div class="tabela-sensores">


                <div class="cabecalho-tabela colunas-rotas">

                    <span>ID</span>

                    <span>Nome</span>

                    <span>Trem</span>

                    <span>Tempo Estimado</span>

                    <span>Código</span>

                    <span>Distância</span>

                    <span>Status</span>
        <?php if (isset($_SESSION['tipo']) && $_SESSION['tipo'] === 'administrador'): ?>
                    <span>Ações</span>
          <?php endif; ?>
                </div>


                <?php if ($resultado && mysqli_num_rows($resultado) > 0): ?>


                    <?php while ($rota = mysqli_fetch_assoc($resultado)): ?>

                        <div class="linha-tabela colunas-rotas">


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

<span>

    <?php if ($rota['status'] == 'Ativa'): ?>

        <span class="status-ativa">
            Ativa
        </span>

    <?php else: ?>

        <span class="status-inativa">
            Inativa
        </span>

    <?php endif; ?>

</span>
                            <span class="acoes">
 <?php if ($_SESSION['tipo'] === 'administrador'): ?>
                            <a
                                href="tela-edição-rotas.php?id=<?= (int)$rota['id_rota'] ?>"
                                class="botao-editar"
                            >
                               E  
                            </a>
<?php endif; ?>
 <?php if ($_SESSION['tipo'] === 'administrador'): ?>

                            <a
                                href="tela_exclusão-rotas.php?id=<?= (int)$rota['id_rota'] ?>"
                                class="botao-excluir"
                            >
                               D 
                              
                               /a>
          <?php endif; ?>
                        </span>


                            </span>


                        </div>

                    <?php endwhile; ?>


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