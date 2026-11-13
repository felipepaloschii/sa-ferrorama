<?php 
session_start();

if (!isset($_SESSION['usuario'])) {
    header("Location: public/landing.php");
    exit;
}

if (!in_array($_SESSION['tipo'] ?? '', ['administrador', 'funcionario'], true)) {
    session_destroy();
    header("Location: public/landing.php");
    exit;
}
?>

<?php
require_once 'infra/conexao.php';

$sensoresAtivos = mysqli_fetch_assoc(mysqli_query(
    $conexao,
    "SELECT COUNT(*) AS total FROM sensor
     WHERE LOWER(TRIM(status_inicial)) = 'ativo'"
))['total'];

$trensAtivos = mysqli_fetch_assoc(mysqli_query(
    $conexao,
    "SELECT COUNT(*) AS total FROM trem
     WHERE status = 'operacao'"
))['total'];

$rotasAtivas = mysqli_fetch_assoc(mysqli_query(
    $conexao,
    "SELECT COUNT(*) AS total FROM rotas
     WHERE status = 'ativa'"
))['total'];
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home - PIA Enterprise</title>
    <link rel="stylesheet" href="assets/style/style.css">
</head>

<body id="home-corpo">

   <header class="barra-superior">
    <h1>PIA Enterprise</h1>

    <aside class="menu-lateral">
        <nav>
            <a class="ativo" href="index.php">Dashboard</a>
            <a href="public/sensor/tela-home-sensor.php">Sensores</a>
            <a href="public/trem/lista_trem.php">Trens</a>
            <a href="public/rotas/tela-home-rotas.php">Rotas</a>

            <?php if ($_SESSION['tipo'] === 'administrador'): ?>
                <a href="public/usuario/tela-home-usuario.php">Usuários</a>
            <?php endif; ?>

            <div class="caixa-usuario">
                <span class="tipo-usuario">
                    <?= $_SESSION['tipo'] === 'administrador' ? 'Administrador' : 'Funcionário' ?>
                </span>

                <a class="botao-logout" href="public/logout/logout.php">
                    Sair
                </a>
            </div>
            </div>
        </nav>
    </aside>
</header>

    <main class="conteudo">

        <section class="painel-principal">

            <h2>Dashboard</h2>
            <div class="metricas">

            <article class="cartao-metrica">
                <h3>Sensores ativos</h3>
                <p class="valor-metrica"><strong><?= $sensoresAtivos ?></strong></p>
            </article>

            <article class="cartao-metrica">
                <h3>Trens ativos</h3>
                <p class="valor-metrica"><strong><?= $trensAtivos ?></strong></p>
            </article>

            <article class="cartao-metrica">
                <h3>Rotas ativas</h3>
                <p class="valor-metrica"><strong><?= $rotasAtivas ?></strong></p>
            </article>

               <article class="cartao-metrica">
                <h3>Clima em Joinville</h3>

                <div class="linha-metrica">
                <strong class="ciano" id="clima-temperatura">-- °C</strong>
                </div>

                <p id="clima-condicao">Carregando clima...</p>
                <small id="clima-atualizado"></small>
                </article>


            </div>


              

                <section class="cartao-mapa">

                    <iframe
                        src="https://www.google.com/maps?q=Joinville,SC,Brasil&output=embed"
                        width="100%"
                        height="100%"
                        style="border: 0;"
                        loading="lazy">
                    </iframe>

                </section>

            </div>

        </section>

    </main>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="script/grafico.js"></script>
    <script src="script/clima.js"></script>

</body>

</html>